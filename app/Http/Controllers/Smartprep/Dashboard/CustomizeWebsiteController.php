<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\UiSetting;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Tenant;
use App\Services\TenantService;

class CustomizeWebsiteController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    public function current()
    {
        $user = Auth::guard('smartprep')->user();
        
        // Default settings from main database
        $settings = [
            'general' => UiSetting::getSection('general')->toArray(),
            'navbar' => UiSetting::getSection('navbar')->toArray(),
            'branding' => UiSetting::getSection('branding')->toArray(),
            'homepage' => UiSetting::getSection('homepage')->toArray(),
            'student_portal' => UiSetting::getSection('student_portal')->toArray(),
            'professor_panel' => UiSetting::getSection('professor_panel')->toArray(),
            'admin_panel' => UiSetting::getSection('admin_panel')->toArray(),
            'student_sidebar' => UiSetting::getSection('student_sidebar')->toArray(),
            'professor_sidebar' => UiSetting::getSection('professor_sidebar')->toArray(),
            'admin_sidebar' => UiSetting::getSection('admin_sidebar')->toArray(),
            'advanced' => UiSetting::getSection('advanced')->toArray(),
        ];

        // Check if a specific website is selected
        $selectedWebsiteId = request()->query('website');
        $selectedWebsite = null;
        
        if ($selectedWebsiteId) {
            $selectedWebsite = Client::where('id', $selectedWebsiteId)->where('user_id', $user->id)->first();
            
            if ($selectedWebsite) {
                // Load settings from tenant database if website is selected
                $tenant = Tenant::where('slug', $selectedWebsite->slug)->first();
                
                if ($tenant) {
                    try {
                        // Switch to tenant database to get current settings
                        $this->tenantService->switchToTenant($tenant);
                        
                        // Override with tenant-specific settings
                        $settings = [
                            'general' => Setting::getGroup('general')->toArray(),
                            'navbar' => Setting::getGroup('navbar')->toArray(),
                            'branding' => Setting::getGroup('branding')->toArray(),
                            'homepage' => Setting::getGroup('homepage')->toArray(),
                            'student_portal' => Setting::getGroup('student_portal')->toArray(),
                            'professor_panel' => Setting::getGroup('professor_panel')->toArray(),
                            'admin_panel' => Setting::getGroup('admin_panel')->toArray(),
                            'student_sidebar' => Setting::getGroup('student_sidebar')->toArray(),
                            'professor_sidebar' => Setting::getGroup('professor_sidebar')->toArray(),
                            'admin_sidebar' => Setting::getGroup('admin_sidebar')->toArray(),
                            'advanced' => Setting::getGroup('advanced')->toArray(),
                        ];
                        
                        // Switch back to main database
                        $this->tenantService->switchToMain();
                        
                    } catch (\Exception $e) {
                        // Ensure we switch back to main database
                        $this->tenantService->switchToMain();
                        
                        Log::warning('Failed to load tenant settings, using defaults', [
                            'tenant' => $tenant->slug,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        // Compute preview URL: prefer selected website's preview, else point to ARTC preview
        $previewUrl = $selectedWebsite ? url('/t/' . $selectedWebsite->slug) : url('/artc');

        // Fallback brand name for header
        $navbarBrandName = $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center';

        // Populate selectable websites for this user
        $activeWebsitesQuery = Client::query()->where('archived', false)->orderByDesc('created_at');
        if (!Auth::guard('smartprep_admin')->check()) {
            $activeWebsitesQuery->where('user_id', $user->id);
        }
        $activeWebsites = $activeWebsitesQuery->get();

        return view('smartprep.dashboard.customize-website-complete', compact(
            'navbarBrandName', 
            'settings', 
            'previewUrl', 
            'activeWebsites',
            'selectedWebsite'
        ));
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

    public function store(Request $request)
    {
        $user = Auth::guard('smartprep')->user();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Generate database name first
            $slug = Str::slug($request->input('name'));
            
            // Ensure uniqueness by checking existing clients
            $baseSlug = $slug;
            $counter = 1;
            while (Client::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $databaseName = 'smartprep_' . $slug;
            $domain = $slug . '.smartprep.local';

            // Create a new client/website with database info
            $client = Client::create([
                'name' => $request->input('name'),
                'slug' => $slug,
                'domain' => $domain,
                'db_name' => $databaseName,
                'db_host' => 'localhost',
                'db_port' => 3306,
                'db_username' => 'root', // Default for development
                'db_password' => '', // Default for development
                'status' => 'active',
                'user_id' => $user->id,
                'archived' => false,
            ]);

            // Copy customization settings from admin to this client's tenant database
            $this->copyAdminCustomizationToClient($client);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Website created successfully with admin customizations!',
                    'client' => $client
                ]);
            }

            return redirect()->route('smartprep.dashboard.customize')
                ->with('success', 'Website created successfully with admin customizations!');
                
        } catch (\Exception $e) {
            Log::error('Failed to create client website', [
                'user_id' => $user->id,
                'name' => $request->input('name'),
                'error' => $e->getMessage()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create website. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to create website. Please try again.')
                ->withInput();
        }
    }

    /**
     * Copy admin customization settings to a client's tenant database
     */
    private function copyAdminCustomizationToClient(Client $client)
    {
        try {
            // Define clean default settings for new clients
            $defaultSettings = [
                'general' => [
                    'site_name' => 'Your Company Name',
                    'site_tagline' => 'Your Company Tagline',
                    'contact_email' => 'contact@yourcompany.com',
                    'contact_phone' => '+1 (555) 123-4567',
                    'contact_address' => 'Your Company Address',
                    'preview_url' => url('/t/' . $client->slug),
                ],
                'navbar' => [
                    'brand_name' => 'Your Company Name',
                    'brand_logo' => '',
                    'brand_image' => '',
                    'style' => 'fixed-top',
                    'menu_items' => '[]',
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
                    'hero_title' => 'Welcome to Your Company',
                    'hero_subtitle' => 'Your company description and value proposition',
                    'hero_background_image' => '',
                    'programs_title' => 'Our Services',
                    'programs_subtitle' => 'Discover our range of professional services',
                    'modalities_title' => 'How We Work',
                    'modalities_subtitle' => 'Flexible solutions designed for your needs',
                    'about_title' => 'About Us',
                    'about_subtitle' => 'Learn more about our company and mission',
                    'homepage_background_color' => '#ffffff',
                    'homepage_gradient_color' => '#764ba2',
                    'homepage_text_color' => '#333333',
                    'homepage_button_color' => '#667eea',
                    'homepage_primary_color' => '#667eea',
                    'homepage_secondary_color' => '#764ba2',
                    'homepage_overlay_color' => 'rgba(0,0,0,0.5)',
                    'homepage_hero_bg_color' => '#667eea',
                    'homepage_hero_title_color' => '#ffffff',
                    'homepage_programs_title_color' => '#667eea',
                    'homepage_programs_subtitle_color' => '#6c757d',
                    'homepage_programs_section_bg_color' => '#667eea',
                    'homepage_modalities_bg_color' => '#667eea',
                    'homepage_modalities_text_color' => '#ffffff',
                    'homepage_about_bg_color' => '#ffffff',
                    'homepage_about_title_color' => '#667eea',
                    'homepage_about_text_color' => '#6c757d',
                    'copyright' => '© Copyright Your Company Name. All Rights Reserved.',
                    'login_image' => '',
                ],
                'student_portal' => [
                    'dashboard_header_bg' => '#667eea',
                    'dashboard_header_text' => '#ffffff',
                    'sidebar_bg' => '#1a1a1a',
                    'active_menu_color' => '#3b82f6',
                    'course_card_bg' => '#ffffff',
                    'progress_bar_color' => '#28a745',
                    'course_title_color' => '#333333',
                    'due_date_color' => '#dc3545',
                    'primary_btn_bg' => '#667eea',
                    'primary_btn_text' => '#ffffff',
                    'secondary_btn_bg' => '#6c757d',
                    'link_color' => '#667eea',
                    'success_color' => '#28a745',
                    'warning_color' => '#ffc107',
                    'error_color' => '#dc3545',
                    'info_color' => '#17a2b8',
                ],
                'professor_panel' => [
                    'sidebar_bg' => '#1e293b',
                    'sidebar_text' => '#f1f5f9',
                    'active_menu_color' => '#10b981',
                    'menu_hover_color' => '#475569',
                    'header_bg' => '#ffffff',
                    'header_text' => '#333333',
                    'primary_btn' => '#10b981',
                    'secondary_btn' => '#6c757d',
                ],
                'admin_panel' => [
                    'sidebar_bg' => '#111827',
                    'sidebar_text' => '#f9fafb',
                    'active_menu_color' => '#f59e0b',
                    'menu_hover_color' => '#374151',
                    'header_bg' => '#ffffff',
                    'header_text' => '#333333',
                    'primary_btn' => '#f59e0b',
                    'secondary_btn' => '#6c757d',
                ],
                'student_sidebar' => [
                    'primary_color' => '#1a1a1a',
                    'secondary_color' => '#2d2d2d',
                    'accent_color' => '#3b82f6',
                    'text_color' => '#e0e0e0',
                    'hover_color' => '#374151',
                ],
                'professor_sidebar' => [
                    'primary_color' => '#1e293b',
                    'secondary_color' => '#334155',
                    'accent_color' => '#10b981',
                    'text_color' => '#f1f5f9',
                    'hover_color' => '#475569',
                ],
                'admin_sidebar' => [
                    'primary_color' => '#111827',
                    'secondary_color' => '#1f2937',
                    'accent_color' => '#f59e0b',
                    'text_color' => '#f9fafb',
                    'hover_color' => '#374151',
                ],
                'advanced' => [
                    'custom_css' => '',
                    'custom_js' => '',
                    'google_analytics' => '',
                    'facebook_pixel' => '',
                    'meta_tags' => '',
                    'maintenance_mode' => '0',
                    'debug_mode' => '0',
                    'cache_enabled' => '1',
                ],
            ];

            // Check if this client has an associated tenant
            $tenant = Tenant::where('slug', $client->slug)->first();
            
            if (!$tenant) {
                // Create a tenant for this client
                $tenant = $this->tenantService->createTenant(
                    $client->name, 
                    $client->domain
                );
            }

            // Switch to tenant database to save settings
            $this->tenantService->switchToTenant($tenant);
            
            // Copy all default settings to tenant database
            foreach ($defaultSettings as $section => $settings) {
                foreach ($settings as $key => $value) {
                    // Determine the setting type based on the key
                    $type = 'text';
                    if (str_contains($key, 'color')) {
                        $type = 'color';
                    } elseif (str_contains($key, '_bg') || str_contains($key, '_image') || str_contains($key, '_logo')) {
                        $type = 'file';
                    } elseif (in_array($key, ['maintenance_mode', 'debug_mode', 'cache_enabled', 'show_login_button'])) {
                        $type = 'boolean';
                    } elseif (in_array($key, ['menu_items', 'meta_tags'])) {
                        $type = 'json';
                    }
                    
                    Setting::set($section, $key, $value, $type);
                }
            }
            
            // Switch back to main database
            $this->tenantService->switchToMain();

            Log::info('Successfully initialized client with clean default settings', [
                'client_id' => $client->id,
                'client_name' => $client->name,
                'tenant_database' => $tenant->database_name,
                'sections_initialized' => array_keys($defaultSettings),
                'total_settings' => array_sum(array_map('count', $defaultSettings))
            ]);

        } catch (\Exception $e) {
            // Ensure we switch back to main database even if an error occurs
            $this->tenantService->switchToMain();
            
            Log::error('Failed to initialize client with default settings', [
                'client_id' => $client->id,
                'client_name' => $client->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    public function destroy($id)
    {
        $user = Auth::guard('smartprep')->user();
        
        $client = Client::where('id', $id)->where('user_id', $user->id)->first();
        
        if (!$client) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Website not found.'], 404);
            }
            return redirect()->back()->with('error', 'Website not found.');
        }

        // Archive the client instead of deleting
        $client->update(['archived' => true]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Website archived successfully.']);
        }

        return redirect()->route('smartprep.dashboard.customize')
            ->with('success', 'Website archived successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::guard('smartprep')->user();
        
        $client = Client::where('id', $id)->where('user_id', $user->id)->first();
        
        if (!$client) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Website not found.'], 404);
            }
            return redirect()->back()->with('error', 'Website not found.');
        }

        // Update client settings if provided
        if ($request->has('settings')) {
            try {
                // Get the tenant for this client
                $tenant = Tenant::where('slug', $client->slug)->first();
                
                if ($tenant) {
                    // Switch to tenant database
                    $this->tenantService->switchToTenant($tenant);
                    
                    // Update settings in tenant database
                    $settings = $request->input('settings', []);
                    foreach ($settings as $section => $sectionSettings) {
                        foreach ($sectionSettings as $key => $value) {
                            Setting::set($section, $key, $value);
                        }
                    }
                    
                    // Switch back to main database
                    $this->tenantService->switchToMain();
                }
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => 'Website settings updated successfully.']);
                }
                
                return redirect()->back()->with('success', 'Website settings updated successfully.');
                
            } catch (\Exception $e) {
                // Ensure we switch back to main database
                $this->tenantService->switchToMain();
                
                Log::error('Failed to update client settings', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage()
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Failed to update settings.'], 500);
                }
                
                return redirect()->back()->with('error', 'Failed to update settings.');
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'No changes made.']);
        }

        return redirect()->back();
    }

    /**
     * Update general settings in tenant database
     */
    public function updateGeneral(Request $request)
    {
        return $this->updateTenantSettings($request, 'general', [
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'preview_url' => 'nullable|url|max:500',
        ]);
    }

    /**
     * Ensure settings table exists in the current tenant database
     */
    private function ensureSettingsTableExists()
    {
        try {
            // Check if settings table exists
            $tables = DB::select("SHOW TABLES LIKE 'settings'");
            
            if (empty($tables)) {
                // Create the settings table
                DB::statement("CREATE TABLE IF NOT EXISTS settings (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    `group` varchar(100) NOT NULL,
                    `key` varchar(100) NOT NULL,
                    `value` text,
                    `type` varchar(50) DEFAULT 'text',
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY settings_group_key_unique (`group`,`key`),
                    KEY settings_group_index (`group`),
                    KEY settings_key_index (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                
                Log::info('Settings table created in tenant database');
            }
        } catch (\Exception $e) {
            Log::error('Failed to ensure settings table exists: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update navbar settings in tenant database
     */
    public function updateNavbar(Request $request)
    {
        try {
            $request->validate([
                'brand_name' => 'nullable|string|max:255',
                'navbar_brand_name' => 'nullable|string|max:255',
                'navbar_brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'navbar_brand_image' => 'nullable|string|max:500',
                'navbar_style' => 'nullable|string|in:fixed-top,sticky-top,static',
                'navbar_menu_items' => 'nullable|string',
                'show_login_button' => 'nullable|boolean',
            ]);

            $user = Auth::guard('smartprep')->user();
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
                }
                return redirect()->back()->with('error', 'User not authenticated.');
            }

            // Get selected website/client from route parameters
            $websiteId = $request->route('website');
            $client = Client::where('id', $websiteId)->where('user_id', $user->id)->first();
            
            if (!$client) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Website not found.'], 404);
                }
                return redirect()->back()->with('error', 'Website not found.');
            }

            $tenant = Tenant::where('slug', $client->slug)->first();
            if (!$tenant) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Tenant not found.'], 404);
                }
                return redirect()->back()->with('error', 'Tenant not found.');
            }

            // Switch to tenant database
            $this->tenantService->switchToTenant($tenant);

            // Ensure settings table exists
            $this->ensureSettingsTableExists();

            // Handle brand name (accept both field names for compatibility)
            $brandName = $request->input('brand_name') ?? $request->input('navbar_brand_name', 'Your Company Name');
            Setting::set('navbar', 'brand_name', $brandName, 'text');

            // Handle brand logo upload
            if ($request->hasFile('navbar_brand_logo')) {
                $file = $request->file('navbar_brand_logo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('brand-logos', $filename, 'public');
                Setting::set('navbar', 'brand_logo', 'storage/' . $path, 'file');
            }

            // Save other navbar settings
            Setting::set('navbar', 'brand_image', $request->input('navbar_brand_image', ''), 'file');
            Setting::set('navbar', 'style', $request->input('navbar_style', 'fixed-top'), 'text');
            Setting::set('navbar', 'menu_items', $request->input('navbar_menu_items', '[]'), 'json');
            Setting::set('navbar', 'show_login_button', $request->has('show_login_button') ? '1' : '0', 'boolean');

            // Switch back to main database
            $this->tenantService->switchToMain();

            Log::info("Navbar settings updated for tenant {$tenant->slug}", [
                'brand_name' => $brandName,
                'logo_uploaded' => $request->hasFile('navbar_brand_logo')
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Navbar settings updated successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Navbar settings updated successfully!');

        } catch (\Exception $e) {
            // Ensure we switch back to main database
            $this->tenantService->switchToMain();
            
            Log::error('Error updating navbar settings: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating navbar settings.'], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating navbar settings.');
        }
    }

    /**
     * Update homepage settings in tenant database
     */
    public function updateHomepage(Request $request)
    {
        try {
            $request->validate([
                'hero_title' => 'nullable|string|max:255',
                'hero_subtitle' => 'nullable|string|max:1000',
                'hero_background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'programs_title' => 'nullable|string|max:255',
                'programs_subtitle' => 'nullable|string|max:500',
                'modalities_title' => 'nullable|string|max:255',
                'modalities_subtitle' => 'nullable|string|max:500',
                'about_title' => 'nullable|string|max:255',
                'about_subtitle' => 'nullable|string|max:500',
                'homepage_background_color' => 'nullable|string|max:7',
                'homepage_gradient_color' => 'nullable|string|max:7',
                'homepage_text_color' => 'nullable|string|max:7',
                'homepage_button_color' => 'nullable|string|max:7',
                'homepage_primary_color' => 'nullable|string|max:7',
                'homepage_secondary_color' => 'nullable|string|max:7',
                'homepage_overlay_color' => 'nullable|string|max:7',
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
                'login_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = Auth::guard('smartprep')->user();
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
                }
                return redirect()->back()->with('error', 'User not authenticated.');
            }

            // Get selected website/client from route parameters
            $websiteId = $request->route('website');
            $client = Client::where('id', $websiteId)->where('user_id', $user->id)->first();
            
            if (!$client) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Website not found.'], 404);
                }
                return redirect()->back()->with('error', 'Website not found.');
            }

            $tenant = Tenant::where('slug', $client->slug)->first();
            if (!$tenant) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Tenant not found.'], 404);
                }
                return redirect()->back()->with('error', 'Tenant not found.');
            }

            // Switch to tenant database
            $this->tenantService->switchToTenant($tenant);

            // Handle text fields
            $textFields = [
                'hero_title', 'hero_subtitle', 'programs_title', 'programs_subtitle',
                'modalities_title', 'modalities_subtitle', 'about_title', 'about_subtitle',
                'homepage_background_color', 'homepage_gradient_color', 'homepage_text_color',
                'homepage_button_color', 'homepage_primary_color', 'homepage_secondary_color',
                'homepage_overlay_color', 'homepage_hero_bg_color', 'homepage_hero_title_color',
                'homepage_programs_title_color', 'homepage_programs_subtitle_color',
                'homepage_programs_section_bg_color', 'homepage_modalities_bg_color',
                'homepage_modalities_text_color', 'homepage_about_bg_color',
                'homepage_about_title_color', 'homepage_about_text_color', 'copyright'
            ];

            foreach ($textFields as $field) {
                if ($request->has($field) && $request->input($field) !== null) {
                    Setting::set('homepage', $field, $request->input($field), 'text');
                }
            }

            // Handle hero background image upload
            if ($request->hasFile('hero_background')) {
                $file = $request->file('hero_background');
                $filename = time() . '_hero_' . $file->getClientOriginalName();
                $path = $file->storeAs('homepage-images', $filename, 'public');
                Setting::set('homepage', 'hero_background_image', 'storage/' . $path, 'file');
            }

            // Handle login image upload
            if ($request->hasFile('login_image')) {
                $file = $request->file('login_image');
                $filename = time() . '_login_' . $file->getClientOriginalName();
                $path = $file->storeAs('homepage-images', $filename, 'public');
                Setting::set('homepage', 'login_image', 'storage/' . $path, 'file');
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            Log::info("Homepage settings updated for tenant {$tenant->slug}", [
                'hero_title' => $request->input('hero_title'),
                'hero_image_uploaded' => $request->hasFile('hero_background'),
                'login_image_uploaded' => $request->hasFile('login_image')
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Homepage settings updated successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Homepage settings updated successfully!');

        } catch (\Exception $e) {
            // Ensure we switch back to main database
            $this->tenantService->switchToMain();
            
            Log::error('Error updating homepage settings: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating homepage settings.'], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating homepage settings.');
        }
    }

    /**
     * Update branding settings in tenant database
     */
    public function updateBranding(Request $request)
    {
        return $this->updateTenantSettings($request, 'branding', [
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'logo_url' => 'nullable|string|max:500',
            'favicon_url' => 'nullable|string|max:500',
            'font_family' => 'nullable|string|max:100',
        ]);
    }

    /**
     * Update student portal settings in tenant database
     */
    public function updateStudent(Request $request)
    {
        return $this->updateTenantSettings($request, 'student_portal', [
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
    }

    /**
     * Update professor panel settings in tenant database
     */
    public function updateProfessor(Request $request)
    {
        return $this->updateTenantSettings($request, 'professor_panel', [
            'sidebar_bg' => 'nullable|string',
            'sidebar_text' => 'nullable|string',
            'active_menu_color' => 'nullable|string',
            'menu_hover_color' => 'nullable|string',
            'header_bg' => 'nullable|string',
            'header_text' => 'nullable|string',
            'primary_btn' => 'nullable|string',
            'secondary_btn' => 'nullable|string',
        ]);
    }

    /**
     * Update admin panel settings in tenant database
     */
    public function updateAdmin(Request $request)
    {
        return $this->updateTenantSettings($request, 'admin_panel', [
            'sidebar_bg' => 'nullable|string',
            'sidebar_text' => 'nullable|string',
            'active_menu_color' => 'nullable|string',
            'menu_hover_color' => 'nullable|string',
            'header_bg' => 'nullable|string',
            'header_text' => 'nullable|string',
            'primary_btn' => 'nullable|string',
            'secondary_btn' => 'nullable|string',
        ]);
    }

    /**
     * Update advanced settings in tenant database
     */
    public function updateAdvanced(Request $request)
    {
        return $this->updateTenantSettings($request, 'advanced', [
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'google_analytics' => 'nullable|string|max:50',
            'facebook_pixel' => 'nullable|string|max:100',
            'meta_tags' => 'nullable|string',
            'maintenance_mode' => 'nullable|boolean',
            'debug_mode' => 'nullable|boolean',
            'cache_enabled' => 'nullable|boolean',
        ]);
    }

    /**
     * Update sidebar customization settings in tenant database
     */
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

            $user = Auth::guard('smartprep')->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            }

            // Get selected website/client from route parameters
            $websiteId = $request->route('website');
            $client = Client::where('id', $websiteId)->where('user_id', $user->id)->first();
            
            if (!$client) {
                return response()->json(['success' => false, 'message' => 'Website not found.'], 404);
            }

            $tenant = Tenant::where('slug', $client->slug)->first();
            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Tenant not found.'], 404);
            }

            $role = $request->input('role');
            $colors = $request->input('colors');
            $section = $role . '_sidebar';

            // Switch to tenant database
            $this->tenantService->switchToTenant($tenant);

            // Ensure settings table exists
            $this->ensureSettingsTableExists();

            // Save each color setting to the tenant database
            foreach ($colors as $key => $value) {
                Setting::set($section, $key, $value);
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            Log::info("Sidebar colors updated for {$role} in tenant {$tenant->slug}", $colors);

            return response()->json([
                'success' => true,
                'message' => "Sidebar colors updated successfully for {$role}",
                'role' => $role,
                'colors' => $colors
            ]);

        } catch (\Exception $e) {
            // Ensure we switch back to main database
            $this->tenantService->switchToMain();
            
            Log::error('Error updating client sidebar settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating sidebar settings'
            ], 500);
        }
    }

    /**
     * Generic method to update tenant settings
     */
    private function updateTenantSettings(Request $request, $section, $validationRules)
    {
        try {
            $request->validate($validationRules);

            $user = Auth::guard('smartprep')->user();
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
                }
                return redirect()->back()->with('error', 'User not authenticated.');
            }

            // Get selected website/client from route parameters
            $websiteId = $request->route('website');
            $client = Client::where('id', $websiteId)->where('user_id', $user->id)->first();
            
            if (!$client) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Website not found.'], 404);
                }
                return redirect()->back()->with('error', 'Website not found.');
            }

            $tenant = Tenant::where('slug', $client->slug)->first();
            if (!$tenant) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Tenant not found.'], 404);
                }
                return redirect()->back()->with('error', 'Tenant not found.');
            }

            // Switch to tenant database
            $this->tenantService->switchToTenant($tenant);

            // Ensure settings table exists
            $this->ensureSettingsTableExists();

            // Save all submitted settings to tenant database
            foreach ($request->only(array_keys($validationRules)) as $key => $value) {
                if ($value !== null) {
                    Setting::set($section, $key, $value);
                }
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            Log::info("Settings updated for section {$section} in tenant {$tenant->slug}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst(str_replace('_', ' ', $section)) . ' settings updated successfully!'
                ]);
            }

            return redirect()->back()->with('success', ucfirst(str_replace('_', ' ', $section)) . ' settings updated successfully!');

        } catch (\Exception $e) {
            // Ensure we switch back to main database
            $this->tenantService->switchToMain();
            
            Log::error("Error updating {$section} settings: " . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating settings.'], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating settings.');
        }
    }

    public function submitCustomization(Request $request)
    {
        $user = Auth::guard('smartprep')->user();

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'domain_preference' => 'nullable|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        // Persist request via Eloquent for events/casting
        \App\Models\WebsiteRequest::create([
            'user_id' => $user->id,
            'business_name' => $request->input('business_name'),
            'business_type' => $request->input('business_type'),
            'description' => $request->input('description', 'Website customization request'),
            'domain_preference' => $request->input('domain_preference'),
            'contact_email' => $request->input('contact_email', $user->email),
            'contact_phone' => $request->input('contact_phone'),
            'template_data' => json_decode($request->input('customization_data', '{}'), true),
            'status' => 'pending',
        ]);

        return redirect()->route('smartprep.dashboard')
            ->with('success', 'Your website request has been submitted! Our team will review it shortly.');
    }
}
