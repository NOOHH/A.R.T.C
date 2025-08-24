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
            'auth' => [
                'login_title' => 'Welcome Back',
                'login_subtitle' => 'Sign in to your account to continue',
                'login_button_text' => 'Sign In',
                'login_bg_color' => '#f8f9fa',
                'login_card_bg' => '#ffffff',
                'register_title' => 'Create Account',
                'register_subtitle' => 'Join us to start your learning journey',
                'register_button_text' => 'Create Account',
                'registration_enabled' => true,
                'enrollment_title' => 'Enroll in Our Programs',
                'enrollment_subtitle' => 'Choose your program and start learning today',
                'enrollment_button_text' => 'Enroll Now',
                'show_pricing' => true,
                'auth_primary_btn' => '#007bff',
                'auth_secondary_btn' => '#6c757d',
                'auth_input_border' => '#ced4da',
                'auth_input_focus' => '#007bff',
            ],
            'director_features' => [
                'view_students' => true,
                'manage_programs' => true,
                'manage_modules' => true,
                'manage_enrollments' => true,
                'view_analytics' => true,
                'manage_professors' => true,
                'manage_announcements' => true,
                'manage_batches' => true,
            ],
            'professor_features' => [
                'ai_quiz_enabled' => true,
                'grading_enabled' => true,
                'progress_tracking' => true,
                'communication_tools' => true,
                'content_management' => true,
                'analytics_access' => true,
                'assignment_creation' => true,
                'student_management' => true,
            ],
        ];

        // Check if a specific website is selected
        $selectedWebsiteId = request()->query('website');
        $selectedWebsite = null;
        
        if ($selectedWebsiteId) {
            if (Auth::guard('smartprep_admin')->check()) {
                $selectedWebsite = Client::find($selectedWebsiteId);
            } else {
                $selectedWebsite = Client::where('id', $selectedWebsiteId)->where('user_id', $user->id)->first();
            }
            
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
                            'student_sidebar' => \App\Models\UiSetting::getSection('student_sidebar')->toArray(),
                            'professor_sidebar' => \App\Models\UiSetting::getSection('professor_sidebar')->toArray(),
                            'admin_sidebar' => \App\Models\UiSetting::getSection('admin_sidebar')->toArray(),
                            'advanced' => Setting::getGroup('advanced')->toArray(),
                            'auth' => Setting::getGroup('auth')->toArray(),
                            'director_features' => Setting::getGroup('director_features')->toArray(),
                            'professor_features' => Setting::getGroup('professor_features')->toArray(),
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

        // Compute preview URL based on website status
        if ($selectedWebsite) {
            $status = strtolower($selectedWebsite->status);
            if ($status === \App\Models\Client::STATUS_DRAFT) {
                $previewUrl = url('/t/draft/' . $selectedWebsite->slug);
            } else {
                $previewUrl = url('/t/' . $selectedWebsite->slug);
            }
            // If navbar brand still template default (ARTC) after provisioning, auto-adjust to website name (first-load convenience)
            if (($settings['navbar']['brand_name'] ?? '') === 'Ascendo Review and Training Center' || ($settings['navbar']['brand_name'] ?? '') === 'ARTC') {
                $settings['navbar']['brand_name'] = $selectedWebsite->name;
            }
        } else {
            $previewUrl = url('/artc');
        }

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

            // Create a new client/website with database info (initially draft status)
            $client = Client::create([
                'name' => $request->input('name'),
                'slug' => $slug,
                'domain' => $domain,
                'db_name' => $databaseName,
                'db_host' => 'localhost',
                'db_port' => 3306,
                'db_username' => 'root', // Default for development
                'db_password' => '', // Default for development
                'status' => Client::STATUS_DRAFT, // start as draft until publication is requested
                'user_id' => $user->id,
                'archived' => false,
            ]);

            // Copy customization settings from admin to this client's tenant database
            $this->copyAdminCustomizationToClient($client);

            // Override brand/site name in tenant to match newly created website name
            try {
                $tenant = Tenant::where('slug', $client->slug)->first();
                if ($tenant) {
                    $this->tenantService->switchToTenant($tenant);
                    Setting::set('navbar', 'brand_name', $client->name, 'text');
                    Setting::set('navbar', 'navbar_brand_name', $client->name, 'text');
                    Setting::set('general', 'site_name', $client->name, 'text');
                    $this->tenantService->switchToMain();
                }
            } catch (\Throwable $e) {
                $this->tenantService->switchToMain();
                Log::warning('Failed to set initial brand/site name for new tenant', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage()
                ]);
            }

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
            // Get all admin settings from main database including panel settings
            $adminSettings = [
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
            
            // Copy all customization settings to tenant database
            foreach ($adminSettings as $section => $settings) {
                foreach ($settings as $key => $value) {
                    // Copy all settings for these sections
                    Setting::set($section, $key, $value);
                }
            }
            
            // Switch back to main database
            $this->tenantService->switchToMain();

            Log::info('Successfully copied complete admin customization settings to client', [
                'client_id' => $client->id,
                'client_name' => $client->name,
                'tenant_database' => $tenant->database_name,
                'sections_copied' => array_keys($adminSettings),
                'total_settings' => array_sum(array_map('count', $adminSettings))
            ]);

        } catch (\Exception $e) {
            // Ensure we switch back to main database even if an error occurs
            $this->tenantService->switchToMain();
            
            Log::error('Failed to copy admin customization settings to client', [
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
     * Update navbar settings in tenant database
     */
    public function updateNavbar(Request $request)
    {
        return $this->updateTenantSettings($request, 'navbar', [
            'brand_name' => 'nullable|string|max:255',
            'navbar_brand_name' => 'nullable|string|max:255',
            'navbar_brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'navbar_brand_image' => 'nullable|string|max:500',
            'navbar_style' => 'nullable|string|in:fixed-top,sticky-top,static',
            'navbar_menu_items' => 'nullable|string',
            'show_login_button' => 'nullable|boolean',
        ]);
    }

    /**
     * Update homepage settings in tenant database
     */
    public function updateHomepage(Request $request)
    {
        return $this->updateTenantSettings($request, 'homepage', [
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
        ]);
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

            // Get selected website/client - accept query param, route param, or body input
            $websiteId = $request->query('website') ?: $request->route('website') ?: $request->input('website');
            // Allow smartprep admins to act on any client; regular users only their own
            if (Auth::guard('smartprep_admin')->check()) {
                $client = Client::find($websiteId);
            } else {
                $client = Client::where('id', $websiteId)->where('user_id', $user->id)->first();
            }
            
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
            Log::debug('[SidebarUpdate] Switched to tenant DB', [
                'tenant_slug' => $tenant->slug,
                'tenant_db' => $tenant->database_name,
                'connection' => config('database.default'),
                'section' => $section,
            ]);

            // Save each color setting to the tenant database (ui_settings table)
            foreach ($colors as $key => $value) {
                \App\Models\UiSetting::set($section, $key, $value, 'color');
            }

            // Switch back to main database
            $this->tenantService->switchToMain();
            Log::debug('[SidebarUpdate] Switched back to main DB', [
                'connection' => config('database.default'),
            ]);

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


            // Allow smartprep admins to act on any client; regular users only their own
            $isAdmin = Auth::guard('smartprep_admin')->check();
            $user = $isAdmin ? Auth::guard('smartprep_admin')->user() : Auth::guard('smartprep')->user();
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
                }
                return redirect()->back()->with('error', 'User not authenticated.');
            }

            // Get selected website/client - accept query param, route param, or body input
            $websiteId = $request->query('website') ?: $request->route('website') ?: $request->input('website');
            if ($isAdmin) {
                $client = Client::find($websiteId);
            } else {
                $client = Client::where('id', $websiteId)->where('user_id', $user->id)->first();
            }

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
            Log::debug('[TenantSettingsUpdate] Switched to tenant DB', [
                'section' => $section,
                'tenant_slug' => $tenant->slug,
                'tenant_db' => $tenant->database_name,
                'connection' => config('database.default'),
            ]);

            // Save all submitted settings to tenant database
            // Handle file uploads: if a field has an uploaded file, store to 'public' disk and persist disk-relative path
            foreach ($validationRules as $field => $rule) {
                // File uploads
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    if ($file && $file->isValid()) {
                        // Determine storage directory
                        $dir = $section;
                        // Normalize logo-related uploads into brand-logos for consistency
                        if (in_array($field, ['navbar_brand_logo','brand_logo','logo_url'])) {
                            $dir = 'brand-logos';
                        }
                        $dest = $file->store($dir, 'public');
                        Setting::set($section, $field, $dest, 'file');
                        // Backward compatibility: if navbar_brand_logo uploaded also set brand_logo
                        if ($section === 'navbar' && $field === 'navbar_brand_logo') {
                            Setting::set($section, 'brand_logo', $dest, 'file');
                        }
                        continue;
                    }
                }
                // Non-file fields
                if ($request->has($field)) {
                    $val = $request->input($field);
                    // Coerce checkbox boolean to 1/0 strings if needed
                    if (is_bool($val)) {
                        $val = $val ? '1' : '0';
                    }
                    Setting::set($section, $field, $val, 'text');
                    if ($section === 'navbar' && $field === 'brand_name') {
                        // Also persist alternate key navbar_brand_name for compatibility if used elsewhere
                        Setting::set($section, 'navbar_brand_name', $val, 'text');
                    }
                }
            }

            // Switch back to main database
            $this->tenantService->switchToMain();
            Log::debug('[TenantSettingsUpdate] Switched back to main DB', [
                'section' => $section,
                'connection' => config('database.default'),
            ]);

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

    /**
     * Update director features settings
     */
    public function updateDirector(Request $request, $website)
    {
        try {
            $website = Client::findOrFail($website);
            $tenant = Tenant::where('slug', $website->slug)->firstOrFail();
            
            $this->tenantService->switchToTenant($tenant);
            
            $directorFeatures = [
                'view_students' => $request->has('view_students'),
                'manage_programs' => $request->has('manage_programs'),
                'manage_modules' => $request->has('manage_modules'),
                'manage_enrollments' => $request->has('manage_enrollments'),
                'view_analytics' => $request->has('view_analytics'),
                'manage_professors' => $request->has('manage_professors'),
                'manage_announcements' => $request->has('manage_announcements'),
                'manage_batches' => $request->has('manage_batches'),
            ];
            
            // Save to tenant database
            Setting::setGroup('director_features', $directorFeatures);
            
            $this->tenantService->switchToMain();
            
            return response()->json([
                'success' => true,
                'message' => 'Director features updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            $this->tenantService->switchToMain();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update director features: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update professor features settings
     */
    public function updateProfessorFeatures(Request $request, $website)
    {
        try {
            $website = Client::findOrFail($website);
            $tenant = Tenant::where('slug', $website->slug)->firstOrFail();
            
            $this->tenantService->switchToTenant($tenant);
            
            $professorFeatures = [
                'ai_quiz_enabled' => $request->has('ai_quiz_enabled'),
                'grading_enabled' => $request->has('grading_enabled'),
                'progress_tracking' => $request->has('progress_tracking'),
                'communication_tools' => $request->has('communication_tools'),
                'content_management' => $request->has('content_management'),
                'analytics_access' => $request->has('analytics_access'),
                'assignment_creation' => $request->has('assignment_creation'),
                'student_management' => $request->has('student_management'),
            ];
            
            // Save to tenant database
            Setting::setGroup('professor_features', $professorFeatures);
            
            $this->tenantService->switchToMain();
            
            return response()->json([
                'success' => true,
                'message' => 'Professor features updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            $this->tenantService->switchToMain();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update professor features: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update auth settings in tenant database
     */
    public function updateAuth(Request $request, $website)
    {
        return $this->updateTenantSettings($request, 'auth', [
            'login_title' => 'nullable|string',
            'login_subtitle' => 'nullable|string',
            'login_button_text' => 'nullable|string',
            'login_bg_color' => 'nullable|string',
            'login_card_bg' => 'nullable|string',
            'register_title' => 'nullable|string',
            'register_subtitle' => 'nullable|string',
            'register_button_text' => 'nullable|string',
            'registration_enabled' => 'boolean',
            'enrollment_title' => 'nullable|string',
            'enrollment_subtitle' => 'nullable|string',
            'enrollment_button_text' => 'nullable|string',
            'show_pricing' => 'boolean',
            'auth_primary_btn' => 'nullable|string',
            'auth_secondary_btn' => 'nullable|string',
            'auth_input_border' => 'nullable|string',
            'auth_input_focus' => 'nullable|string',
        ]);
    }

    public function updateRegistration(Request $request, $website)
    {
        return $this->updateTenantSettings($request, 'auth', [
            // System/Predefined Fields
            'system_fields.education_level.active' => 'boolean',
            'system_fields.education_level.required' => 'boolean',
            'system_fields.program_id.active' => 'boolean',
            'system_fields.program_id.required' => 'boolean',
            'system_fields.start_date.active' => 'boolean',
            'system_fields.start_date.required' => 'boolean',
            
            // Custom Fields
            'custom_section_name' => 'nullable|string',
            'custom_field_label' => 'nullable|string',
            'custom_field_type' => 'nullable|string',
            'custom_field_program' => 'nullable|string',
            'custom_field_required' => 'boolean',
            'custom_field_active' => 'boolean',
            
            // Registration Settings
            'register_title' => 'nullable|string',
            'register_subtitle' => 'nullable|string',
            'register_button_text' => 'nullable|string',
            'registration_enabled' => 'boolean',
            
            // Login Settings (allow updates from registration form)
            'login_title' => 'nullable|string',
            'login_subtitle' => 'nullable|string',
            'login_button_text' => 'nullable|string',
            'login_bg_top_color' => 'nullable|string',
            'login_bg_bottom_color' => 'nullable|string',
            'login_accent_color' => 'nullable|string',
            'login_text_color' => 'nullable|string',
            'login_card_bg' => 'nullable|string',
            'login_input_border' => 'nullable|string',
            'login_input_focus' => 'nullable|string',
        ]);
    }
}

