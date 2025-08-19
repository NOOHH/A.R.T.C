<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Program;

class HomepageController extends Controller
{
    public function index()
    {
        // Get programs from database (not archived)
        $programs = Program::where('is_archived', false)
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        // Get homepage content directly from UiSettingsHelper for real-time database updates
        $homepageSettings = \App\Helpers\UiSettingsHelper::getSection('homepage')->toArray();
        
        // DEBUG: Log raw database data
        Log::info('Raw Database Settings', [
            'homepageSettings' => $homepageSettings,
            'hero_title_exists' => array_key_exists('hero_title', $homepageSettings),
            'hero_title_value' => $homepageSettings['hero_title'] ?? 'NOT SET'
        ]);
        
        // Merge with sensible defaults for any missing values
        $homepageContent = array_merge([
            'hero_title' => 'Welcome to Ascendo Review and Training Center',
            'hero_subtitle' => 'Your premier destination for comprehensive review programs and professional training.',
            'hero_button_text' => 'ENROLL NOW',
            'programs_title' => 'Our Programs',
            'programs_subtitle' => 'Choose from our comprehensive range of review and training programs',
            'modalities_title' => 'Learning Modalities',
            'modalities_subtitle' => 'Flexible learning options designed to fit your schedule and learning style',
            'about_title' => 'About Us',
            'about_subtitle' => 'We are committed to providing high-quality education and training'
        ], $homepageSettings);
        
               // FORCE the correct value for testing
        
        // DEBUG: Log what we're passing to the view
        Log::info('HomepageController Data', [
            'hero_title' => $homepageContent['hero_title'],
            'hero_subtitle' => $homepageContent['hero_subtitle'],
            'homepageSettings_count' => count($homepageSettings)
        ]);

        
        // For backward compatibility
        $homepageTitle = $homepageContent['hero_button_text'] ?? 'ENROLL NOW';
        
        $response = response()->view('welcome.homepage', compact('programs', 'homepageTitle', 'homepageContent'));
        
        // Add cache headers to prevent caching
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');
        
        return $response;
    }
}
