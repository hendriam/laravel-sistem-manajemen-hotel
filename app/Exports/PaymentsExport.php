<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class PaymentsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $day = date('Y-m-d');
        $month = date('m');
        $year = date('Y');

        // Filter tanggal
        if ($this->request->filled('start_date')) {
            $day = $this->request->start_date;
        }

        // Filter month
        if ($this->request->filled('month')) {
            $month = $this->request->month;
        }

        // Filter year
        if ($this->request->filled('year')) {
            $year = $this->request->year;
        }

        $query = DB::table('payments')
                ->join('reservations', 'payments.reservation_id', '=', 'reservations.id')
                ->join('guests', 'reservations.guest_id', '=', 'guests.id')
                ->join('rooms', 'reservations.room_id', '=', 'rooms.id')
                ->select(
                    'payments.payment_date',
                    'guests.name as guest_name',
                    'rooms.room_number',
                    'payments.amount',
                    'payments.method'
                )
                ->whereYear('payments.payment_date', $year)
                ->whereMonth('payments.payment_date', $month)
                ->whereDate('payments.payment_date', $day);

        return $query->get()->map(function ($item) {
            return [
                'Tanggal' => $item->payment_date,
                'Nama Tamu' => $item->guest_name ?? '-',
                'Kamar' => $item->room_number ?? '-',
                'Jumlah Bayar' => $item->amount,
                'Metode Bayar' => ucfirst($item->method)
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Tamu',
            'Kamar',
            'Jumlah Bayar',
            'Metode Bayar',
        ];
    }
}
