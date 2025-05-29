<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\Payment;

class CheckinController extends Controller
{
    protected string $title = "Reservasi";
    
    public function checkin(string $id)
    {
        $reservation = Reservation::with(['guest', 'room', 'payments'])->findOrFail($id);

        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date)->toDateString();
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date)->toDateString();
        $checkInDate = \Carbon\Carbon::createFromFormat('Y-m-d', $checkIn);
        $checkOutDate = \Carbon\Carbon::createFromFormat('Y-m-d', $checkOut);

        $reservation->duration = $checkInDate->diffInDays($checkOutDate);
        $reservation->total_paid = $reservation->payments->sum('amount');

        return view('reservation.checkin', compact('reservation'), [
            'title' => $this->title,
        ]);
    }

    public function checkInProcess(Request $request, string $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5',
            'method' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        try {
            $reservation = Reservation::findOrFail($id);

            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            if ($reservation->status !== 'confirmed') {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Reservasi tidak dalam status confirmed.",
                ], 400));
            }

            $checkIn = \Carbon\Carbon::parse($reservation->check_in_date)->toDateString();
            $checkOut = \Carbon\Carbon::parse($reservation->check_out_date)->toDateString();
            $checkInDate = \Carbon\Carbon::createFromFormat('Y-m-d', $checkIn);
            $checkOutDate = \Carbon\Carbon::createFromFormat('Y-m-d', $checkOut);

            $duration = $checkInDate->diffInDays($checkOutDate);

            $total = $reservation->room->price * $duration;
            $paid = $reservation->payments->sum('amount');
            $remaining = $total - $paid;

            // Buat pembayaran otomatis jika belum lunas
            if ($remaining > 0) {
                if ($request->amount != $remaining) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => "Jumlah pembayaran harus sesuai jumlah sisa tagihan.",
                    ], 400));
                }
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $request->amount,
                    'payment_date' => now(),
                    'method' => $request->method, 
                    'notes' => $request->notes,
                    'created_by' => Auth::id()
                ]);
            }

            $reservation->update([
                'status' => 'checked_in',
                'updated_by' => Auth::id()
            ]);

            $reservation->room->update(['status' => 'occupied']);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di check-in.',
                'reservation' => $reservation,
                'redirect' => route('reservation.checkIn', $id)
            ], 200);
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function createDirectCheckin()
    {
        return view('reservation.direct_checkin',[
            'title' => 'Check-in Lansung'
        ]);
    }

    public function storeDirectCheckin(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_out_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:5',
            'payment_method' => 'required|string',
            'notes_down_payment' => 'nullable|string',
        ]);

        // Cek apakah tamu sudah punya reservasi/checkin aktif
        $existing = Reservation::where('guest_id', $request->guest_id)
            ->whereIn('status', ['pending', 'checked_in'])
            ->first();
        
        if ($existing) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Tamu sudah booking atau check-in! Silakan selesaikan pembayaran atau batalkan reservasi sebelumnya.',
            ], 400));
        }

        DB::beginTransaction();

        try {
            $newReservationNumber = 'RES' . now()->format('Ymd') . '' . str_pad(Reservation::count() + 1, 4, '0', STR_PAD_LEFT);
            $reservation = Reservation::create([
                'reservation_number' => $newReservationNumber,
                'guest_id' => $request->guest_id,
                'room_id' => $request->room_id,
                'check_in_date' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'check_out_date' => \Carbon\Carbon::parse($request->check_out_date)->setTime(12, 0),
                'status' => 'checked_in',
                'created_by' => Auth::id()
            ]);

            $checkIn = \Carbon\Carbon::parse($reservation->check_in_date)->toDateString();
            $checkOut = \Carbon\Carbon::parse($reservation->check_out_date)->toDateString();
            $checkInDate = \Carbon\Carbon::createFromFormat('Y-m-d', $checkIn);
            $checkOutDate = \Carbon\Carbon::createFromFormat('Y-m-d', $checkOut);
            
            $duration = $checkInDate->diffInDays($checkOutDate);
            $totalRoomPrices = $reservation->room->price * $duration;

            if(!$this->checkTotalAmount($totalRoomPrices, $request->total_amount)) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'duration' => $duration,
                    'message' => 'Jumlah pembayaran harusnya Rp ' .number_format($totalRoomPrices, 0, ',', '.'). ' !',
                ], 400));
            }
            
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $request->total_amount,
                'payment_date' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'method' => $request->payment_method,
                'notes' => $request->notes_down_payment,
                'created_by' => Auth::id(),
            ]);

            $reservation->room->update(['status' => 'occupied']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'reservation' => $reservation,
                'redirect' => route('reservation.direct.create')
            ], 201);
        } catch (HttpResponseException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function checkTotalAmount($totalRoomPrices, $totalAmount) {
        if ($totalAmount == $totalRoomPrices ) {
            return true;
        } else {
            return false;
        }
    }
}
