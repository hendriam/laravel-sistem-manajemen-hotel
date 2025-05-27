<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReservationsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Reservation::with(['guest', 'room','createdBy', 'updatedby']);

        if ($this->request->status) {
            $query->where('status', $this->request->status);
        }

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        if ($this->request->start_date && $this->request->end_date) {
            $start_date = $this->request->start_date;
            $end_date = $this->request->end_date;     
        }

        $query->whereBetween('check_in_date', [$start_date, $end_date]);


        return $query->get()->map(function ($item) {
            return [
                'ID' => $item->id,
                'No. Reservasi' => $item->reservation_number,
                'Nama Tamu' => $item->guest->name ?? '-',
                'Kamar' => $item->room->room_number ?? '-',
                'Tanggal Check-in' => $item->check_in_date,
                'Tanggal Check-out' => $item->check_out_date,
                'Status' => $item->status,
                'Catatan' => $item->notes ?? '-',
                'Diinput oleh' => $item->createdBy->name ?? '-',
                'Tanggal input' => $item->created_at->format('Y-m-d H:i:s')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'No. Reservasi',
            'Nama Tamu',
            'Kamar',
            'Tanggal Check-in',
            'Tanggal Check-out',
            'Status',
            'Catatan',
            'Diinput oleh',
            'Tanggal input'
        ];
    }
}
