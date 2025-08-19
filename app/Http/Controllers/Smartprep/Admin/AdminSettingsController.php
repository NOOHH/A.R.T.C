<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\UiSetting;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Get current settings from database and JSON file
        $settings = $this->getCurrentSettings();
        
        // Use the admin settings interface
        return view('smartprep.admin.admin-settings.index', compact('settings'));
    }
    
    public function save(Request $request)
    {
        // Legacy save method - redirect to appropriate specific method
        return $this->updateGeneral($request);
    }

    public function updateNavbar(Request $request)
    {
        $request->validate([
            'brand_name' => 'nullable|string|max:255',
            'navbar_brand_name' => 'nullable|string|max:255',
            'navbar_brand_image' => 'nullable|string|max:500',
            'navbar_style' => 'nullable|string|in:fixed-top,sticky-top,static',
            'navbar_menu_items' => 'nullable|string',
            'show_login_button' => 'nullable|boolean',
        ]);

        // Accept both field names for backward compatibility
        $brandName = $request->input('brand_name') ?? $request->input('navbar_brand_name', 'SmartPrep Admin');

        // Save to database using UiSetting model
        UiSetting::set('navbar', 'brand_name', $brandName, 'text');
        UiSetting::set('navbar', 'brand_image', $request->input('navbar_brand_image', ''), 'file');
        UiSetting::set('navbar', 'style', $request->input('navbar_style', 'fixed-top'), 'text');
        UiSetting::set('navbar', 'menu_items', $request->input('navbar_menu_items', '[]'), 'json');
        UiSetting::set('navbar', 'show_login_button', $request->has('show_login_button') ? '1' : '0', 'boolean');

        // Also save to JSON file for backward compatibility
        $settings = $this->getCurrentSettings();
        $settings['navbar'] = array_merge($settings['navbar'] ?? [], [
            'brand_name' => $brandName,
            'brand_image' => $request->input('navbar_brand_image', $settings['navbar']['brand_image'] ?? ''),
            'style' => $request->input('navbar_style', $settings['navbar']['style'] ?? 'fixed-top'),
            'menu_items' => $request->input('navbar_menu_items', $settings['navbar']['menu_items'] ?? '[]'),
            'show_login_button' => $request->has('show_login_button') ? '1' : '0',
            'updated_at' => now()->toISOString()
        ]);
        $this->saveSettings($settings);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Navbar settings updated successfully!']);
        }

        return back()->with('success', 'Navbar settings updated successfully!');
    }

    public function updateHomepage(Request $request)
    {
        $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'homepage_background_color' => 'nullable|string|max:7',
            'homepage_gradient_color' => 'nullable|string|max:7',
            'homepage_text_color' => 'nullable|string|max:7',
            'homepage_button_color' => 'nullable|string|max:7',
            'homepage_primary_color' => 'nullable|string|max:7',
            'homepage_secondary_color' => 'nullable|string|max:7',
            'homepage_overlay_color' => 'nullable|string|max:7',
            // Section-specific colors
            'homepage_hero_bg_color' => 'nullable|string|max:7',
            'homepage_hero_title_color' => 'nullable|string|max:7',
            'homepage_programs_title_color' => 'nullable|string|max:7',
            'homepage_programs_subtitle_color' => 'nullable|string|max:7',
            'homepage_modalities_bg_color' => 'nullable|string|max:7',
            'homepage_modalities_text_color' => 'nullable|string|max:7',
            'homepage_about_title_color' => 'nullable|string|max:7',
            'homepage_about_text_color' => 'nullable|string|max:7',
            'cta_primary_text' => 'nullable|string|max:100',
            'cta_primary_link' => 'nullable|string|max:255',
            'cta_secondary_text' => 'nullable|string|max:100',
            'cta_secondary_link' => 'nullable|string|max:255',
            'features_title' => 'nullable|string|max:255',
            'copyright' => 'nullable|string|max:500',
        ]);

        // Save to database using UiSetting model
        UiSetting::set('homepage', 'hero_title', $request->input('hero_title', 'Review Smarter. Learn Better. Succeed Faster.'), 'text');
        UiSetting::set('homepage', 'hero_subtitle', $request->input('hero_subtitle', 'Your premier destination for comprehensive review programs and professional training.'), 'text');
        UiSetting::set('homepage', 'background_color', $request->input('homepage_background_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'gradient_color', $request->input('homepage_gradient_color', '#764ba2'), 'color');
        UiSetting::set('homepage', 'text_color', $request->input('homepage_text_color', '#ffffff'), 'color');
        UiSetting::set('homepage', 'button_color', $request->input('homepage_button_color', '#28a745'), 'color');
        UiSetting::set('homepage', 'primary_color', $request->input('homepage_primary_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'secondary_color', $request->input('homepage_secondary_color', '#764ba2'), 'color');
        UiSetting::set('homepage', 'overlay_color', $request->input('homepage_overlay_color', '#000000'), 'color');
        
        // Section-specific colors
        UiSetting::set('homepage', 'hero_bg_color', $request->input('homepage_hero_bg_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'hero_title_color', $request->input('homepage_hero_title_color', '#ffffff'), 'color');
        UiSetting::set('homepage', 'programs_title_color', $request->input('homepage_programs_title_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'programs_subtitle_color', $request->input('homepage_programs_subtitle_color', '#6c757d'), 'color');
        UiSetting::set('homepage', 'modalities_bg_color', $request->input('homepage_modalities_bg_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'modalities_text_color', $request->input('homepage_modalities_text_color', '#ffffff'), 'color');
        UiSetting::set('homepage', 'about_title_color', $request->input('homepage_about_title_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'about_text_color', $request->input('homepage_about_text_color', '#6c757d'), 'color');
        
        UiSetting::set('homepage', 'cta_primary_text', $request->input('cta_primary_text', 'Get Started'), 'text');
        UiSetting::set('homepage', 'cta_primary_link', $request->input('cta_primary_link', '/programs'), 'text');
        UiSetting::set('homepage', 'cta_secondary_text', $request->input('cta_secondary_text', 'Learn More'), 'text');
        UiSetting::set('homepage', 'cta_secondary_link', $request->input('cta_secondary_link', '/about'), 'text');
        UiSetting::set('homepage', 'features_title', $request->input('features_title', 'Why Choose Us?'), 'text');
        UiSetting::set('homepage', 'copyright', $request->input('copyright', '© Copyright Ascendo Review and Training Center. All Rights Reserved.'), 'text');

        // Also save to JSON file for backward compatibility
        $settings = $this->getCurrentSettings();
        $settings['homepage'] = array_merge($settings['homepage'] ?? [], [
            'hero_title' => $request->input('hero_title', $settings['homepage']['hero_title'] ?? 'Review Smarter. Learn Better. Succeed Faster.'),
            'hero_subtitle' => $request->input('hero_subtitle', $settings['homepage']['hero_subtitle'] ?? 'Your premier destination for comprehensive review programs and professional training.'),
            'background_color' => $request->input('homepage_background_color', $settings['homepage']['background_color'] ?? '#667eea'),
            'gradient_color' => $request->input('homepage_gradient_color', $settings['homepage']['gradient_color'] ?? '#764ba2'),
            'text_color' => $request->input('homepage_text_color', $settings['homepage']['text_color'] ?? '#ffffff'),
            'button_color' => $request->input('homepage_button_color', $settings['homepage']['button_color'] ?? '#28a745'),
            'primary_color' => $request->input('homepage_primary_color', $settings['homepage']['primary_color'] ?? '#667eea'),
            'secondary_color' => $request->input('homepage_secondary_color', $settings['homepage']['secondary_color'] ?? '#764ba2'),
            'overlay_color' => $request->input('homepage_overlay_color', $settings['homepage']['overlay_color'] ?? '#000000'),
            // Section-specific colors
            'hero_bg_color' => $request->input('homepage_hero_bg_color', $settings['homepage']['hero_bg_color'] ?? '#667eea'),
            'hero_title_color' => $request->input('homepage_hero_title_color', $settings['homepage']['hero_title_color'] ?? '#ffffff'),
            'programs_title_color' => $request->input('homepage_programs_title_color', $settings['homepage']['programs_title_color'] ?? '#667eea'),
            'programs_subtitle_color' => $request->input('homepage_programs_subtitle_color', $settings['homepage']['programs_subtitle_color'] ?? '#6c757d'),
            'modalities_bg_color' => $request->input('homepage_modalities_bg_color', $settings['homepage']['modalities_bg_color'] ?? '#667eea'),
            'modalities_text_color' => $request->input('homepage_modalities_text_color', $settings['homepage']['modalities_text_color'] ?? '#ffffff'),
            'about_title_color' => $request->input('homepage_about_title_color', $settings['homepage']['about_title_color'] ?? '#667eea'),
            'about_text_color' => $request->input('homepage_about_text_color', $settings['homepage']['about_text_color'] ?? '#6c757d'),
            'cta_primary_text' => $request->input('cta_primary_text', $settings['homepage']['cta_primary_text'] ?? 'Get Started'),
            'cta_primary_link' => $request->input('cta_primary_link', $settings['homepage']['cta_primary_link'] ?? '/programs'),
            'cta_secondary_text' => $request->input('cta_secondary_text', $settings['homepage']['cta_secondary_text'] ?? 'Learn More'),
            'cta_secondary_link' => $request->input('cta_secondary_link', $settings['homepage']['cta_secondary_link'] ?? '/about'),
            'features_title' => $request->input('features_title', $settings['homepage']['features_title'] ?? 'Why Choose Us?'),
            'copyright' => $request->input('copyright', $settings['homepage']['copyright'] ?? '© Copyright Ascendo Review and Training Center. All Rights Reserved.'),
            'updated_at' => now()->toISOString()
        ]);
        $this->saveSettings($settings);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Homepage content updated successfully!']);
        }

        return back()->with('success', 'Homepage content updated successfully!');
    }

    public function updateBranding(Request $request)
    {
        $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'logo_url' => 'nullable|string|max:500',
            'favicon_url' => 'nullable|string|max:500',
            'font_family' => 'nullable|string|max:100',
        ]);

        // Save to database using UiSetting model
        UiSetting::set('branding', 'primary_color', $request->input('primary_color', '#667eea'), 'color');
        UiSetting::set('branding', 'secondary_color', $request->input('secondary_color', '#764ba2'), 'color');
        UiSetting::set('branding', 'background_color', $request->input('background_color', '#ffffff'), 'color');
        UiSetting::set('branding', 'logo_url', $request->input('logo_url', ''), 'file');
        UiSetting::set('branding', 'favicon_url', $request->input('favicon_url', ''), 'file');
        UiSetting::set('branding', 'font_family', $request->input('font_family', 'Inter'), 'text');

        // Also save to JSON file for backward compatibility
        $settings = $this->getCurrentSettings();
        $settings['branding'] = array_merge($settings['branding'] ?? [], [
            'primary_color' => $request->input('primary_color', $settings['branding']['primary_color'] ?? '#667eea'),
            'secondary_color' => $request->input('secondary_color', $settings['branding']['secondary_color'] ?? '#764ba2'),
            'background_color' => $request->input('background_color', $settings['branding']['background_color'] ?? '#ffffff'),
            'logo_url' => $request->input('logo_url', $settings['branding']['logo_url'] ?? ''),
            'favicon_url' => $request->input('favicon_url', $settings['branding']['favicon_url'] ?? ''),
            'font_family' => $request->input('font_family', $settings['branding']['font_family'] ?? 'Inter'),
            'updated_at' => now()->toISOString()
        ]);
        $this->saveSettings($settings);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Branding settings updated successfully!']);
        }

        return back()->with('success', 'Branding settings updated successfully!');
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'preview_url' => 'nullable|url|max:500',
        ]);

        // Save to database using UiSetting model
        UiSetting::set('general', 'site_name', $request->input('site_name', 'SmartPrep Admin'), 'text');
        UiSetting::set('general', 'site_tagline', $request->input('site_tagline', 'Admin Management System'), 'text');
        UiSetting::set('general', 'contact_email', $request->input('contact_email', 'admin@smartprep.com'), 'text');
        UiSetting::set('general', 'contact_phone', $request->input('contact_phone', '+1 (555) 123-4567'), 'text');
        UiSetting::set('general', 'contact_address', $request->input('contact_address', '123 Admin Street, Admin City, AC 12345'), 'text');
        UiSetting::set('general', 'preview_url', $request->input('preview_url', 'http://127.0.0.1:8000/'), 'text');

        // Also save to JSON file for backward compatibility
        $settings = $this->getCurrentSettings();
        $settings['general'] = array_merge($settings['general'] ?? [], [
            'site_name' => $request->input('site_name', $settings['general']['site_name'] ?? 'SmartPrep Admin'),
            'site_tagline' => $request->input('site_tagline', $settings['general']['site_tagline'] ?? 'Admin Management System'),
            'contact_email' => $request->input('contact_email', $settings['general']['contact_email'] ?? 'admin@smartprep.com'),
            'contact_phone' => $request->input('contact_phone', $settings['general']['contact_phone'] ?? '+1 (555) 123-4567'),
            'contact_address' => $request->input('contact_address', $settings['general']['contact_address'] ?? '123 Admin Street, Admin City, AC 12345'),
            'preview_url' => $request->input('preview_url', $settings['general']['preview_url'] ?? 'http://127.0.0.1:8000/'),
            'updated_at' => now()->toISOString()
        ]);
        $this->saveSettings($settings);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'General settings updated successfully!']);
        }

        return back()->with('success', 'General settings updated successfully!');
    }

    private function getCurrentSettings()
    {
        // Use the main settings file that controls the main A.R.T.C homepage
        $settingsPath = storage_path('app/settings.json');
        
        // Get settings from database first
        $dbSettings = [
            'general' => UiSetting::getSection('general')->toArray(),
            'navbar' => UiSetting::getSection('navbar')->toArray(),
            'branding' => UiSetting::getSection('branding')->toArray(),
            'homepage' => UiSetting::getSection('homepage')->toArray(),
        ];

        // Get settings from JSON file for backward compatibility
        $jsonSettings = [];
        if (File::exists($settingsPath)) {
            $jsonSettings = json_decode(File::get($settingsPath), true);
        }

        // Merge database settings with JSON settings (database takes precedence)
        $mergedSettings = array_merge($jsonSettings, $dbSettings);

        // If no settings exist, return defaults
        if (empty($mergedSettings)) {
            return [
                'general' => [
                    'site_name' => 'SmartPrep Admin',
                    'site_tagline' => 'Admin Management System',
                    'contact_email' => 'admin@smartprep.com',
                    'contact_phone' => '+1 (555) 123-4567',
                    'contact_address' => '123 Admin Street, Admin City, AC 12345',
                ],
                'navbar' => [
                    'brand_name' => 'Ascendo Review and Training Center',
                    'brand_image' => '',
                    'style' => 'fixed-top',
                    'menu_items' => '[{"label":"Dashboard","link":"/dashboard"}, {"label":"Users","link":"/users"}, {"label":"Settings","link":"/settings"}]',
                    'show_login_button' => '1',
                ],
                'branding' => [
                    'primary_color' => '#667eea',
                    'secondary_color' => '#764ba2',
                    'background_color' => '#ffffff',
                    'logo_url' => '',
                    'favicon_url' => '',
                    'font_family' => 'Inter',
                ],
                'homepage' => [
                    'hero_title' => 'Review Smarter. Learn Better. Succeed Faster.',
                    'hero_subtitle' => 'At Ascendo Review and Training Center, we guide future licensed professionals toward exam success with expert-led reviews and flexible learning options.',
                    'cta_primary_text' => 'Get Started',
                    'cta_primary_link' => '/programs',
                    'cta_secondary_text' => 'Learn More',
                    'cta_secondary_link' => '/about',
                    'features_title' => 'Why Choose Us?',
                    'copyright' => '© Copyright Ascendo Review and Training Center. All Rights Reserved.',
                ],
            ];
        }

        return $mergedSettings;
    }

    private function saveSettings($settings)
    {
        // Save to the main settings file that controls the main A.R.T.C homepage
        $settingsPath = storage_path('app/settings.json');
        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
    }
}
