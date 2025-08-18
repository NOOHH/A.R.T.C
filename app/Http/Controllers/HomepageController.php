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
        
        // Get homepage content from the proper method that includes database settings
        $homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
        
        // For backward compatibility
        $homepageTitle = $homepageContent['hero_button_text'] ?? 'ENROLL NOW';
        
        return view('welcome.homepage', compact('programs', 'homepageTitle', 'homepageContent'));
    }
}
