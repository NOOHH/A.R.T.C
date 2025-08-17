<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('smartprep.admin.dashboard');
    }
}
