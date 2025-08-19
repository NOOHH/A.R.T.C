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
        // Ensure preview_url always targets ARTC preview route for admin panel
        $settings['general']['preview_url'] = url('/artc');
        
        // Get sidebar customization settings for all roles
        $sidebarSettings = $this->getSidebarSettings();
        
        // Use the admin settings interface
        return view('smartprep.admin.admin-settings.index', compact('settings', 'sidebarSettings'));
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
            'navbar_brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'navbar_brand_image' => 'nullable|string|max:500',
            'navbar_style' => 'nullable|string|in:fixed-top,sticky-top,static',
            'navbar_menu_items' => 'nullable|string',
            'show_login_button' => 'nullable|boolean',
        ]);

        // Accept both field names for backward compatibility
        $brandName = $request->input('brand_name') ?? $request->input('navbar_brand_name', 'SmartPrep Admin');

        // Handle brand logo upload
        if ($request->hasFile('navbar_brand_logo')) {
            $file = $request->file('navbar_brand_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('brand-logos', $filename, 'public');
            UiSetting::set('navbar', 'brand_logo', 'storage/' . $path, 'file');
        }

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
            'hero_background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Section content fields
            'programs_title' => 'nullable|string|max:255',
            'programs_subtitle' => 'nullable|string|max:500',
            'modalities_title' => 'nullable|string|max:255',
            'modalities_subtitle' => 'nullable|string|max:500',
            'about_title' => 'nullable|string|max:255',
            'about_subtitle' => 'nullable|string|max:500',
            // Colors
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
            'homepage_programs_section_bg_color' => 'nullable|string|max:7',
            'homepage_modalities_bg_color' => 'nullable|string|max:7',
            'homepage_modalities_text_color' => 'nullable|string|max:7',
            'homepage_about_bg_color' => 'nullable|string|max:7',
            'homepage_about_title_color' => 'nullable|string|max:7',
            'homepage_about_text_color' => 'nullable|string|max:7',
            'copyright' => 'nullable|string|max:500',
        ]);

        // Save to database using UiSetting model
        UiSetting::set('homepage', 'hero_title', $request->input('hero_title', 'Review Smarter. Learn Better. Succeed Faster.'), 'text');
        UiSetting::set('homepage', 'hero_subtitle', $request->input('hero_subtitle', 'Your premier destination for comprehensive review programs and professional training.'), 'text');
        
        // Handle hero background image upload
        if ($request->hasFile('hero_background')) {
            $file = $request->file('hero_background');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('hero-images', $filename, 'public');
            UiSetting::set('homepage', 'hero_background_image', 'storage/' . $path, 'file');
        }
        
        // Section content
        UiSetting::set('homepage', 'programs_title', $request->input('programs_title', 'Our Programs'), 'text');
        UiSetting::set('homepage', 'programs_subtitle', $request->input('programs_subtitle', 'Choose from our comprehensive range of review and training programs'), 'text');
        UiSetting::set('homepage', 'modalities_title', $request->input('modalities_title', 'Learning Modalities'), 'text');
        UiSetting::set('homepage', 'modalities_subtitle', $request->input('modalities_subtitle', 'Flexible learning options designed to fit your schedule and learning style'), 'text');
        UiSetting::set('homepage', 'about_title', $request->input('about_title', 'About Us'), 'text');
        UiSetting::set('homepage', 'about_subtitle', $request->input('about_subtitle', 'We are committed to providing high-quality education and training'), 'text');
        
        // Colors
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
        UiSetting::set('homepage', 'programs_section_bg_color', $request->input('homepage_programs_section_bg_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'modalities_bg_color', $request->input('homepage_modalities_bg_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'modalities_text_color', $request->input('homepage_modalities_text_color', '#ffffff'), 'color');
        UiSetting::set('homepage', 'about_bg_color', $request->input('homepage_about_bg_color', '#ffffff'), 'color');
        UiSetting::set('homepage', 'about_title_color', $request->input('homepage_about_title_color', '#667eea'), 'color');
        UiSetting::set('homepage', 'about_text_color', $request->input('homepage_about_text_color', '#6c757d'), 'color');
        
        UiSetting::set('homepage', 'copyright', $request->input('copyright', '© Copyright Ascendo Review and Training Center. All Rights Reserved.'), 'text');

        // Also save to JSON file for backward compatibility
        $settings = $this->getCurrentSettings();
        $settings['homepage'] = array_merge($settings['homepage'] ?? [], [
            'hero_title' => $request->input('hero_title', $settings['homepage']['hero_title'] ?? 'Review Smarter. Learn Better. Succeed Faster.'),
            'hero_subtitle' => $request->input('hero_subtitle', $settings['homepage']['hero_subtitle'] ?? 'Your premier destination for comprehensive review programs and professional training.'),
            // Section content
            'programs_title' => $request->input('programs_title', $settings['homepage']['programs_title'] ?? 'Our Programs'),
            'programs_subtitle' => $request->input('programs_subtitle', $settings['homepage']['programs_subtitle'] ?? 'Choose from our comprehensive range of review and training programs'),
            'modalities_title' => $request->input('modalities_title', $settings['homepage']['modalities_title'] ?? 'Learning Modalities'),
            'modalities_subtitle' => $request->input('modalities_subtitle', $settings['homepage']['modalities_subtitle'] ?? 'Flexible learning options designed to fit your schedule and learning style'),
            'about_title' => $request->input('about_title', $settings['homepage']['about_title'] ?? 'About Us'),
            'about_subtitle' => $request->input('about_subtitle', $settings['homepage']['about_subtitle'] ?? 'We are committed to providing high-quality education and training'),
            // Colors
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
            'programs_section_bg_color' => $request->input('homepage_programs_section_bg_color', $settings['homepage']['programs_section_bg_color'] ?? '#667eea'),
            'modalities_bg_color' => $request->input('homepage_modalities_bg_color', $settings['homepage']['modalities_bg_color'] ?? '#667eea'),
            'modalities_text_color' => $request->input('homepage_modalities_text_color', $settings['homepage']['modalities_text_color'] ?? '#ffffff'),
            'about_bg_color' => $request->input('homepage_about_bg_color', $settings['homepage']['about_bg_color'] ?? '#ffffff'),
            'about_title_color' => $request->input('homepage_about_title_color', $settings['homepage']['about_title_color'] ?? '#667eea'),
            'about_text_color' => $request->input('homepage_about_text_color', $settings['homepage']['about_text_color'] ?? '#6c757d'),
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
            'website_mode' => 'nullable|in:customize_current,create_new',
            'selected_website' => 'nullable|string|max:255',
        ]);

        // Save to database using UiSetting model
        UiSetting::set('general', 'site_name', $request->input('site_name', 'SmartPrep Admin'), 'text');
        UiSetting::set('general', 'site_tagline', $request->input('site_tagline', 'Admin Management System'), 'text');
        UiSetting::set('general', 'contact_email', $request->input('contact_email', 'admin@smartprep.com'), 'text');
        UiSetting::set('general', 'contact_phone', $request->input('contact_phone', '+1 (555) 123-4567'), 'text');
        UiSetting::set('general', 'contact_address', $request->input('contact_address', '123 Admin Street, Admin City, AC 12345'), 'text');
        // Default preview should point to the ARTC preview route
        UiSetting::set('general', 'preview_url', $request->input('preview_url', url('/artc')), 'text');
        // Persist tenant mode selection and target website
        if ($request->filled('website_mode')) {
            UiSetting::set('general', 'website_mode', $request->input('website_mode'), 'text');
        }
        if ($request->filled('selected_website')) {
            UiSetting::set('general', 'selected_website', $request->input('selected_website'), 'text');
        }

        // Also save to JSON file for backward compatibility (but enforce ARTC preview for UI)
        $settings = $this->getCurrentSettings();
        $settings['general'] = array_merge($settings['general'] ?? [], [
            'site_name' => $request->input('site_name', $settings['general']['site_name'] ?? 'SmartPrep Admin'),
            'site_tagline' => $request->input('site_tagline', $settings['general']['site_tagline'] ?? 'Admin Management System'),
            'contact_email' => $request->input('contact_email', $settings['general']['contact_email'] ?? 'admin@smartprep.com'),
            'contact_phone' => $request->input('contact_phone', $settings['general']['contact_phone'] ?? '+1 (555) 123-4567'),
            'contact_address' => $request->input('contact_address', $settings['general']['contact_address'] ?? '123 Admin Street, Admin City, AC 12345'),
            'preview_url' => url('/artc'),
            'website_mode' => $request->input('website_mode', $settings['general']['website_mode'] ?? 'customize_current'),
            'selected_website' => $request->input('selected_website', $settings['general']['selected_website'] ?? 'current'),
            'updated_at' => now()->toISOString()
        ]);
        $this->saveSettings($settings);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'General settings updated successfully!']);
        }

        return back()->with('success', 'General settings updated successfully!');
    }

    public function updateStudent(Request $request)
    {
        $request->validate([
            'dashboard_header_bg' => 'nullable|string',
            'dashboard_header_text' => 'nullable|string',
            'sidebar_bg' => 'nullable|string',
            'active_menu_color' => 'nullable|string',
            'course_card_bg' => 'nullable|string',
            'progress_bar_color' => 'nullable|string',
            'course_title_color' => 'nullable|string',
            'due_date_color' => 'nullable|string',
            'primary_btn_bg' => 'nullable|string',
            'primary_btn_text' => 'nullable|string',
            'secondary_btn_bg' => 'nullable|string',
            'link_color' => 'nullable|string',
            'success_color' => 'nullable|string',
            'warning_color' => 'nullable|string',
            'error_color' => 'nullable|string',
            'info_color' => 'nullable|string',
        ]);

        // Dashboard Colors
        UiSetting::set('student_portal', 'dashboard_header_bg', $request->input('dashboard_header_bg', '#0d6efd'), 'color');
        UiSetting::set('student_portal', 'dashboard_header_text', $request->input('dashboard_header_text', '#ffffff'), 'color');
        UiSetting::set('student_portal', 'sidebar_bg', $request->input('sidebar_bg', '#f8f9fa'), 'color');
        UiSetting::set('student_portal', 'active_menu_color', $request->input('active_menu_color', '#0d6efd'), 'color');
        
        // Course Interface Colors
        UiSetting::set('student_portal', 'course_card_bg', $request->input('course_card_bg', '#ffffff'), 'color');
        UiSetting::set('student_portal', 'progress_bar_color', $request->input('progress_bar_color', '#28a745'), 'color');
        UiSetting::set('student_portal', 'course_title_color', $request->input('course_title_color', '#212529'), 'color');
        UiSetting::set('student_portal', 'due_date_color', $request->input('due_date_color', '#dc3545'), 'color');
        
        // Button Colors
        UiSetting::set('student_portal', 'primary_btn_bg', $request->input('primary_btn_bg', '#0d6efd'), 'color');
        UiSetting::set('student_portal', 'primary_btn_text', $request->input('primary_btn_text', '#ffffff'), 'color');
        UiSetting::set('student_portal', 'secondary_btn_bg', $request->input('secondary_btn_bg', '#6c757d'), 'color');
        UiSetting::set('student_portal', 'link_color', $request->input('link_color', '#0d6efd'), 'color');
        
        // Status Colors
        UiSetting::set('student_portal', 'success_color', $request->input('success_color', '#28a745'), 'color');
        UiSetting::set('student_portal', 'warning_color', $request->input('warning_color', '#ffc107'), 'color');
        UiSetting::set('student_portal', 'error_color', $request->input('error_color', '#dc3545'), 'color');
        UiSetting::set('student_portal', 'info_color', $request->input('info_color', '#17a2b8'), 'color');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Student portal settings updated successfully!']);
        }

        return back()->with('student_success', 'Student portal settings updated successfully!');
    }

    public function updateProfessor(Request $request)
    {
        $request->validate([
            'sidebar_bg' => 'nullable|string',
            'sidebar_text' => 'nullable|string',
            'active_menu_color' => 'nullable|string',
            'menu_hover_color' => 'nullable|string',
            'header_bg' => 'nullable|string',
            'header_text' => 'nullable|string',
            'primary_btn' => 'nullable|string',
            'secondary_btn' => 'nullable|string',
        ]);

        // Sidebar Colors
        UiSetting::set('professor_panel', 'sidebar_bg', $request->input('sidebar_bg', '#f8f9fa'), 'color');
        UiSetting::set('professor_panel', 'sidebar_text', $request->input('sidebar_text', '#212529'), 'color');
        UiSetting::set('professor_panel', 'active_menu_color', $request->input('active_menu_color', '#0d6efd'), 'color');
        UiSetting::set('professor_panel', 'menu_hover_color', $request->input('menu_hover_color', '#e9ecef'), 'color');
        
        // Dashboard Colors
        UiSetting::set('professor_panel', 'header_bg', $request->input('header_bg', '#0d6efd'), 'color');
        UiSetting::set('professor_panel', 'header_text', $request->input('header_text', '#ffffff'), 'color');
        UiSetting::set('professor_panel', 'primary_btn', $request->input('primary_btn', '#28a745'), 'color');
        UiSetting::set('professor_panel', 'secondary_btn', $request->input('secondary_btn', '#6c757d'), 'color');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Professor panel settings updated successfully!']);
        }

        return back()->with('professor_success', 'Professor panel settings updated successfully!');
    }

    public function updateAdmin(Request $request)
    {
        $request->validate([
            'sidebar_bg' => 'nullable|string',
            'sidebar_text' => 'nullable|string',
            'active_menu_color' => 'nullable|string',
            'menu_hover_color' => 'nullable|string',
            'header_bg' => 'nullable|string',
            'header_text' => 'nullable|string',
            'primary_btn' => 'nullable|string',
            'secondary_btn' => 'nullable|string',
        ]);

        // Sidebar Colors
        UiSetting::set('admin_panel', 'sidebar_bg', $request->input('sidebar_bg', '#343a40'), 'color');
        UiSetting::set('admin_panel', 'sidebar_text', $request->input('sidebar_text', '#ffffff'), 'color');
        UiSetting::set('admin_panel', 'active_menu_color', $request->input('active_menu_color', '#0d6efd'), 'color');
        UiSetting::set('admin_panel', 'menu_hover_color', $request->input('menu_hover_color', '#495057'), 'color');
        
        // Dashboard Colors
        UiSetting::set('admin_panel', 'header_bg', $request->input('header_bg', '#dc3545'), 'color');
        UiSetting::set('admin_panel', 'header_text', $request->input('header_text', '#ffffff'), 'color');
        UiSetting::set('admin_panel', 'primary_btn', $request->input('primary_btn', '#dc3545'), 'color');
        UiSetting::set('admin_panel', 'secondary_btn', $request->input('secondary_btn', '#6c757d'), 'color');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Admin panel settings updated successfully!']);
        }

        return back()->with('admin_success', 'Admin panel settings updated successfully!');
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

    /**
     * Update sidebar customization settings
     */
    private function getSidebarSettings()
    {
        $roles = ['student', 'professor', 'admin'];
        $sidebarSettings = [];
        
        foreach ($roles as $role) {
            $section = $role . '_sidebar';
            $settings = UiSetting::getSection($section);
            
            if (!empty($settings)) {
                $sidebarSettings[$role] = $settings;
            } else {
                // Default colors for each role
                $sidebarSettings[$role] = [
                    'primary_color' => '#001F3F',
                    'secondary_color' => '#2d2d2d',
                    'accent_color' => '#3b82f6',
                    'text_color' => '#ffffff',
                    'hover_color' => '#004080'
                ];
            }
        }
        
        return $sidebarSettings;
    }

    public function updateSidebar(Request $request)
    {
        try {
            $request->validate([
                'role' => 'required|string|in:student,professor,admin',
                'colors' => 'required|array',
                'colors.primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'colors.secondary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'colors.accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'colors.text_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'colors.hover_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $role = $request->input('role');
            $colors = $request->input('colors');
            $section = $role . '_sidebar';

            // Save each color setting to the database
            foreach ($colors as $key => $value) {
                UiSetting::updateOrCreate(
                    ['section' => $section, 'setting_key' => $key],
                    ['setting_value' => $value, 'setting_type' => 'color']
                );
            }

            Log::info("Sidebar colors updated for {$role}", $colors);

            return response()->json([
                'success' => true,
                'message' => "Sidebar colors updated successfully for {$role}",
                'role' => $role,
                'colors' => $colors
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid color format. Please use valid hex colors.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating sidebar settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating sidebar settings'
            ], 500);
        }
    }
}
