<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request) : View
    {
        $title = "Dashboard";

        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::whereIn('status', ['occupied', 'booked'])->count();
        
        $totalGuests = Guest::count();

        $now = Carbon::now();
        $monthlyReservations = Reservation::whereMonth('check_in_date', $now->month)->count();
        $monthlyIncome = Payment::whereMonth('payment_date', $now->month)->sum('amount');
        
        return view('dashboard',compact(
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'totalGuests',
            'monthlyReservations',
            'monthlyIncome'
        ),[
            'title' => $title,
        ]);
    }
}
