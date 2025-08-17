<?php

namespace App\Http\Controllers\Smartprep;

use App\Http\Controllers\Controller;

class HomepageController extends Controller
{
    public function welcome()
    {
        return view('smartprep.homepage.welcome');
    }
}
