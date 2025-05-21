<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request) : View
    {
        $title = "Dashboard";
        return view('dashboard', [
            'title' => $title,
        ]);
    }
}
