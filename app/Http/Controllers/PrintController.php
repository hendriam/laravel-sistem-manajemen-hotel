<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class PrintController extends Controller
{

    public function invoice(string $id)
    {
        $reservation = Reservation::with(['guest', 'room.roomType', 'payments'])->findOrFail($id);

        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $reservation->duration = $checkIn->diffInDays($checkOut);
        $reservation->total_paid = $reservation->payments->sum('amount');

        return view('print.invoice',[
            'reservation' => $reservation
        ]);
    }
}
