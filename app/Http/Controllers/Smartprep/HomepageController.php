<?php

namespace App\Http\Controllers\Smartprep;

use App\Http\Controllers\Controller;
use App\Helpers\UiSettingsHelper;

class HomepageController extends Controller
{
    public function welcome()
    {
        // Get settings from UiSetting database (same as admin settings controller)
        $uiSettings = UiSettingsHelper::getAll();
        
        return view('smartprep.homepage.welcome', compact('uiSettings'));
    }
}
