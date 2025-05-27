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

            $query = $this->filteredReservations($request);

            // Search filter
            if (!empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->whereHas('guest', function ($guestQuery) use ($search) {
                        $guestQuery->where('name', 'like', "%{$search}%");
                    });
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
        $file = $this->fileAndTitle($request, 'xlsx')['file'];
        return Excel::download(new ReservationsExport($request), $file);
    }

    
    public function exportPDF(Request $request)
    {
        $reservations = $this->filteredReservations($request)->get();

        $fileAndTitle = $this->fileAndTitle($request, 'pdf');

        $pdf = Pdf::loadView('report.laporan_reservasi_pdf', compact('reservations'),[
            'title' => $fileAndTitle['title']
        ]);

        return $pdf->download($fileAndTitle['file']);
    }

    private function filteredReservations(Request $request)
    {
        $query = Reservation::with(['guest', 'room','createdBy', 'updatedby']);

        // jika tidak ada filter tanggal, buat default pertanggal hari ini
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        // Filter tanggal check-in
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;                
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $query->whereBetween('check_in_date', [
            $start_date,
            $end_date
        ]);

        return $query;
    }

    public function fileAndTitle(Request $request , string $format = '')
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;                
        }

        $file = 'laporan_reservasi_per_' .$start_date. '_' .$end_date. '.'.$format;
        $title = 'Laporan Reservasi Per ' .$start_date. ' s.d ' .$end_date;

        return [
            'file' => $file,
            'title' => $title,
        ];
    }
}
