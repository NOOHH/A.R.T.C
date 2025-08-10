<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;

class HomepageController extends Controller
{
    public function index()
    {
        // Get programs from database (not archived)
        $programs = Program::where('is_archived', false)
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        // Get homepage title from settings
        $settings = \App\Helpers\SettingsHelper::getSettings();
        $homepageTitle = $settings['homepage']['title'] ?? 'ENROLL NOW';
        
        return view('welcome.homepage', compact('programs', 'homepageTitle'));
    }
}
