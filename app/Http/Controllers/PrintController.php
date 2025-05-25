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

        // dd($reservation);

        $print = $this->printInvoice($reservation);

        return view('print.invoice',[
            'print' => $print,
        ]);
    }

    public function printInvoice($reservation)
    {
        $print = '';
        $print.='<div style="width:100%;padding-bottom:20px;">
                  <table>
                    <tr>
                        <td align="center" colspan="4"><strong>Bukti Pembayaran</strong></td>
                    </tr>
                    <tr>
                        <td>Nama Tamu</td><td>: '.$reservation->guest->name.'</td><td>Jumlah Hari</td><td>: '.$reservation->duration.'</td>
                    </tr>
                    <tr>
                        <td>Kamar</td><td>: '.$reservation->room->room_number.'</td><td>Total Pembayaran</td><td>: '.$reservation->total_paid.'</td>
                    </tr>
                    <tr>
                        <td>Tgl. Check-in</td><td>: '.$reservation->check_in_date.'</td><td></td><td>: </td>
                    </tr>
                    <tr>
                        <td>Tgl. Check-out</td><td>: '.$reservation->check_out_date.'</td><td></td><td>: </td>
                    </tr>
                    <tr></tr>
                    <tr></tr>
                    <tr>
                        <td colspan="4" align="center">
                        	<strong>Terima Kasih</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">
                        	<strong>Hotel Sandika, Jln. Bandung No. 40</strong>
                        </td>
                    </tr>
                </table>
                </div>';

        return $print;
    }
}
