<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportPaymentController extends Controller
{
    protected string $title = "Laporan Pembayaran";

    public function index(Request $request) 
    {
        if ($request->ajax()) {
            $columns = [
                'payment_date',
                'guest_name',
                'room_number',
                'amount',
                'method',
            ];

            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search.value');

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = $this->filteredPayments($request);
          
            $totalRecords = $query->count();

            // Apply search if needed
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('guests.name', 'like', "%$search%")
                    ->orWhere('rooms.room_number', 'like', "%$search%")
                    ->orWhere('payments.method', 'like', "%$search%");
                });
            }

            $filteredRecords = $query->count();

            $data = $query->offset($start)
                        ->limit($length)
                        ->orderBy($order, $dir)
                        ->get()
                        ->map(function ($item) {
                            return [
                                'payment_date' => $item->payment_date,
                                'guest_name' => $item->guest_name,
                                'room_number' => $item->room_number,
                                'amount' => 'Rp' . number_format($item->amount, 0, ',', '.'),
                                'method' => ucfirst($item->method),
                            ];
                        });

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        }

        return view('report.payment', ['title' => $this->title]);
    }

    public function exportExcel(Request $request)
    {
        $fileAndTitle = $this->filterAndTitle($request, 'xlsx');
        return Excel::download(new PaymentsExport($request), $fileAndTitle['file']);
    }
    
    public function exportPDF(Request $request)
    {
        $payments = $this->filteredPayments($request)->get();
        
        $fileAndTitle = $this->filterAndTitle($request, 'pdf');

        $pdf = Pdf::loadView('report.laporan_pembayaran_pdf', compact('payments'), [
            'title' => $fileAndTitle['title']
        ]);

        return $pdf->download($fileAndTitle['file']);
    }

    private function filteredPayments(Request $request)
    {
        $filter = $this->filterAndTitle($request)['filter'];
       
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
            ->whereYear('payments.payment_date', $filter['year'])
            ->whereMonth('payments.payment_date', $filter['month'])
            ->whereDate('payments.payment_date', $filter['day']);

        return $query;
    }

    public function filterAndTitle(Request $request , string $format = '')
    {
        $day = date('Y-m-d');
        $month = date('m');
        $year = date('Y');

        $file = 'laporan_pembayaran_per_' .$day.'.'.$format;
        $title = 'Laporan Pembayaran Per '.$day;

        // Filter tanggal
        if ($request->filled('start_date')) {
            $day = $request->start_date;
            $file = 'laporan_pembayaran_per_' .$day.'.'.$format;
            $title = 'Laporan Pembayaran Per Tanggal '.$day;
        }

        // Filter month
        if ($request->filled('month')) {
            $month = $request->month;
            $file = 'laporan_pembayaran_per_' .$month.'.'.$format;
            $title = 'Laporan Pembayaran Per Bulan '.$month;
        }

        // Filter year
        if ($request->filled('year')) {
            $year = $request->year;
            $file = 'laporan_pembayaran_per_' .$year.'.'.$format;
            $title = 'Laporan Pembayaran Per Tahun '.$year;
        }

        return [
            'filter' => ['day' => $day, 'month' => $month, 'year' => $year],
            'file' => $file,
            'title' => $title,
        ];
    }
}
