<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;

class ClientDashboardController extends Controller
{
    public function index()
    {
        return view('smartprep.dashboard.client');
    }
}
