<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;

class ReservationController extends Controller
{
    protected string $title = "Reservasi";

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'guest.name',
                'room.name',
                'check_in_date',
                'check_out_date',
                'status',
                'notes',
                'created_by.name',
                'created_at' 
            ];
            $totalData = Reservation::count();

            $limit = $request->input('length');   // jumlah data per halaman
            $start = $request->input('start');    // offset

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = Reservation::with(['guest', 'room', 'createdBy', 'updatedby']);

            // Search filter
            if (!empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            $data = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

           return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalData,
                "recordsFiltered" => $totalFiltered,
                "data" => $data
            ]);
        }

        return view('reservation.index', ['title' => $this->title]);
    }

    public function create()
    {
        return view('reservation.create', [
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'notes' => 'nullable|string',
            'down_payment' => 'required|numeric|min:5',
            'down_payment_method' => 'required|string',
            'notes_down_payment' => 'nullable|string',
        ]);

        
        // Cek apakah tamu sudah punya reservasi aktif
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
            $reservation = Reservation::create($request->all() + ['created_by' => Auth::id()]);

            // Jika ada DP, simpan ke payments
            if ($request->filled('down_payment') && $request->down_payment > 0) {

                if(!$this->checkDpAmout($reservation->room->price, $request->down_payment)) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => 'Jumlah DP harus minimal 25% dari total biaya.',
                    ], 400));
                }

                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $request->down_payment,
                    'payment_date' => now(),
                    'method' => $request->down_payment_method,
                    'notes' => $request->notes_down_payment,
                    'created_by' => Auth::id(),
                ]);

                // Ubah status ke confirmed jika DP masuk
                // $reservation->update(['status' => 'confirmed']);
                
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'redirect' => route('reservation.create')
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

    public function checkDpAmout($totalBayar, $nominalDP) {
        $dpMinimum = $totalBayar * 0.25; 
        if ($nominalDP >= $dpMinimum) {
            return true;
        } else {
            return false;
        }
    }

    public function show(string $id)
    {
        $reservation = Reservation::with(['guest', 'room', 'payments'])->findOrFail($id);

        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $reservation->duration = $checkIn->diffInDays($checkOut);
        $reservation->total_paid = $reservation->payments->sum('amount');

        return view('reservation.show', compact('reservation'), [
            'title' => $this->title,
        ]);
    }

    public function edit(string $id)
    {
        $data = Reservation::findOrFail($id);
        return view('reservation.edit', compact('data'), [
            'title' => $this->title,
            'guests' => Guest::all(),
            'rooms' => Room::where('status', 'available')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'notes' => 'nullable|string',
            // 'status' => 'required|in:pending,confirmed,checked_in,cancelled,completed',
        ]);

        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            $reservation->update($request->all() + ['updated_by' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diubah.',
                'redirect' => route('reservation.index')
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

    public function confirm(string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            $reservation->update([
                'status' => 'confirmed',
                'updated_by' => Auth::id()
            ]);

            // ubah status kamar yang dipesan menjadi booked 
            $reservation->room->update(['status' => 'booked']);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di check-in.',
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

    public function checkOut(string $id)
    {
        DB::beginTransaction();

        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            if ($reservation->status !== 'checked_in') {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Reservasi belum dalam status check-in.",
                ], 400));
            }

            $reservation->update([
                'status' => 'completed',
                'updated_by' => Auth::id()
            ]);

            $reservation->room->update(['status' => 'cleaning']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di check-out.',
            ], 200);
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

    public function cancel(string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            if ($reservation->status === 'cancelled') {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Reservasi sudah dalam status batal.",
                ], 400));
            }

            $reservation->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id()
            ]);

            $reservation->room->update(['status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di batalkan.',
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
}