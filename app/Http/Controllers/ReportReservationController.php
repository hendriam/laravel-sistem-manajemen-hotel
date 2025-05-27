<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Exports\ReservationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportReservationController extends Controller
{
    protected string $title = "Laporan Reservasi";
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'reservation_number',
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

            // jika tidak ada filter tanggal, buat default pertanggal hari ini
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');

            // Filter tanggal peminjaman
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;                
            }

            $query->whereBetween('check_in_date', [
                $start_date,
                $end_date
            ]);

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

        return view('report.reservation', ['title' => $this->title]);
    }

    public function exportExcel(Request $request)
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        if ($request->start_date && $request->end_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;     
        }

        $title = 'laporan_reservasi_' .$start_date. '_' .$end_date. '.xlsx';

        return Excel::download(new ReservationsExport($request), $title);
    }

    
    public function exportPDF(Request $request)
    {
        $reservations = $this->filteredReservations($request)->get();
        $pdf = Pdf::loadView('report.laporan_reservasi_pdf', compact('reservations'));

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        if ($request->start_date && $request->end_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;     
        }

        $title = 'laporan_reservasi_' .$start_date. '_' .$end_date. '.pdf';

        return $pdf->download($title);
    }

    private function filteredReservations(Request $request)
    {
        $query = Reservation::with(['guest', 'room','createdBy', 'updatedby']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        if ($request->start_date && $request->end_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;     
        }

        $query->whereBetween('check_in_date', [$start_date, $end_date]);

        return $query;
    }
}
