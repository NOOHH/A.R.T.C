<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;

class CustomizeWebsiteController extends Controller
{
    public function current()
    {
        return view('smartprep.dashboard.customize-website');
    }

    public function old()
    {
        return view('smartprep.dashboard.customize-website-old');
    }

    public function new()
    {
        return view('smartprep.dashboard.customize-website-new');
    }

    public function cacheTest()
    {
        return view('smartprep.dashboard.cache-test');
    }
}
