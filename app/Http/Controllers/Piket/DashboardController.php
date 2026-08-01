<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('piket.dashboard');
    }
}
