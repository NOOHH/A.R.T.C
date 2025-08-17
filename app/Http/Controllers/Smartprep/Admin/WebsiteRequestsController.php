<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;

class WebsiteRequestsController extends Controller
{
    public function index()
    {
        return view('smartprep.admin.website-requests');
    }
}
