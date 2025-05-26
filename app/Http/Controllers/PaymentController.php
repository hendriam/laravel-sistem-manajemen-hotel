<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create(int $reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);

        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $duration = $checkIn->diffInDays($checkOut);

        $reservation->duration = $duration;
        $reservation->total_paid = $reservation->payments->sum('amount');
        
        return view('payment.create', compact('reservation'),[
            'title' => 'Tambah Pembayaran'
        ]);
    }

    public function store(Request $request, $reservation_id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5',
            'payment_date' => 'required|date',
            'method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $reservation = Reservation::findOrFail($reservation_id);

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'method' => $request->method,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            $reservation->update([
                'status' => 'confirmed',
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan.',
                'redirect' => route('reservation.show', $reservation->id)
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

}
