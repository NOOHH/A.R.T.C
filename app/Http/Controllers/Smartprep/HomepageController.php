<?php

namespace App\Http\Controllers\Smartprep;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class HomepageController extends Controller
{
    public function welcome()
    {
        // Get settings from JSON file (same as admin settings)
        $settingsPath = storage_path('app/smartprep_settings.json');
        
        if (File::exists($settingsPath)) {
            $uiSettings = json_decode(File::get($settingsPath), true);
        } else {
            // Default settings if file doesn't exist
            $uiSettings = [
                'general' => [
                    'site_name' => 'SmartPrep',
                    'site_tagline' => 'Multi-Tenant Learning Management Platform',
                ],
                'navbar' => [
                    'brand_name' => 'SmartPrep',
                    'style' => 'fixed-top',
                    'show_login_button' => '1',
                ],
                'branding' => [
                    'primary_color' => '#2563eb',
                    'secondary_color' => '#059669',
                    'accent_color' => '#0891b2',
                    'font_family' => 'Inter',
                ],
                'homepage' => [
                    'hero_title' => 'Transform Education with SmartPrep',
                    'hero_subtitle' => 'Empower your educational institution with our cutting-edge multi-tenant learning management platform. Build professional training websites that scale with your success.',
                    'cta_primary_text' => 'Get Started',
                    'cta_primary_link' => '/programs',
                    'cta_secondary_text' => 'Learn More',
                    'cta_secondary_link' => '/about',
                    'features_title' => 'Why Choose Us?',
                    'copyright' => '© Copyright SmartPrep. All Rights Reserved.',
                ],
            ];
        }
        
        return view('smartprep.homepage.welcome', compact('uiSettings'));
    }
}
