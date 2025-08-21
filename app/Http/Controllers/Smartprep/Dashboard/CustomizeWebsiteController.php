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
use App\Models\TenantUiSetting;
use App\Models\Tenant;
use App\Models\Admin;
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

        // Check if a specific website is selected
        $selectedWebsiteId = request()->query('website');
        $selectedWebsite = null;
        
        // Default settings - start with sample website settings (smartprep_artc)
        $settings = [];
        
        if ($selectedWebsiteId) {
            // Load settings for specific selected website
            $selectedWebsite = Client::where('id', $selectedWebsiteId)->where('user_id', $user->id)->first();
            
            if ($selectedWebsite) {
                // Load settings from tenant database for the selected website
                $tenant = Tenant::where('slug', $selectedWebsite->slug)->first();
                
                                // Switch to the website's database
                // Convert slug to database name format
                $slug = $selectedWebsite->slug;
                // Remove 'smartprep-' prefix if present and replace hyphens with underscores
                if (strpos($slug, 'smartprep-') === 0) {
                    $slug = substr($slug, 10); // Remove 'smartprep-' prefix
                }
                $databaseName = 'smartprep_' . str_replace('-', '_', $slug);
                config(['database.connections.tenant.database' => $databaseName]);
                DB::purge('tenant');
                DB::connection('tenant');
                
                // Load tenant-specific settings from the website's database
                        $settings = [
                    'general' => TenantUiSetting::getSection('general')->toArray(),
                    'navbar' => TenantUiSetting::getSection('navbar')->toArray(),
                    'branding' => TenantUiSetting::getSection('branding')->toArray(),
                    'homepage' => TenantUiSetting::getSection('homepage')->toArray(),
                    'student_portal' => TenantUiSetting::getSection('student_portal')->toArray(),
                    'professor_panel' => TenantUiSetting::getSection('professor_panel')->toArray(),
                    'admin_panel' => TenantUiSetting::getSection('admin_panel')->toArray(),
                    'student_sidebar' => TenantUiSetting::getSection('student_sidebar')->toArray(),
                    'professor_sidebar' => TenantUiSetting::getSection('professor_sidebar')->toArray(),
                    'admin_sidebar' => TenantUiSetting::getSection('admin_sidebar')->toArray(),
                    'advanced' => TenantUiSetting::getSection('advanced')->toArray(),
                ];
                
                // Load admin credentials from this specific website's database
                $adminCredentials = $this->getWebsiteAdminCredentials($selectedWebsite->slug);
                if ($adminCredentials) {
                    $settings['general']['admin_email'] = $adminCredentials['email'];
                    $settings['general']['admin_name'] = $adminCredentials['admin_name'];
                }
                        
                        // Switch back to main database
                config(['database.default' => 'mysql']);
                DB::purge('tenant');
                DB::connection('mysql');
                    }
                } else {
            // No specific website selected - load sample website settings (smartprep_artc)
            $settings = $this->loadSampleWebsiteSettings();
        }
        
        // Ensure we have all required settings sections
        $requiredSections = ['general', 'navbar', 'branding', 'homepage', 'student_portal', 'professor_panel', 'admin_panel', 'student_sidebar', 'professor_sidebar', 'admin_sidebar', 'advanced'];
        foreach ($requiredSections as $section) {
            if (!isset($settings[$section])) {
                $settings[$section] = [];
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

    /**
     * Load settings from the sample website database (smartprep_artc)
     */
    private function loadSampleWebsiteSettings()
    {
        try {
            // Switch to sample website database
            config(['database.connections.tenant.database' => 'smartprep_artc']);
            DB::purge('tenant');
            DB::connection('tenant');
            
            // Load settings from sample website database
            $settings = [
                'general' => TenantUiSetting::getSection('general')->toArray(),
                'navbar' => TenantUiSetting::getSection('navbar')->toArray(),
                'branding' => TenantUiSetting::getSection('branding')->toArray(),
                'homepage' => TenantUiSetting::getSection('homepage')->toArray(),
                'student_portal' => TenantUiSetting::getSection('student_portal')->toArray(),
                'professor_panel' => TenantUiSetting::getSection('professor_panel')->toArray(),
                'admin_panel' => TenantUiSetting::getSection('admin_panel')->toArray(),
                'student_sidebar' => TenantUiSetting::getSection('student_sidebar')->toArray(),
                'professor_sidebar' => TenantUiSetting::getSection('professor_sidebar')->toArray(),
                'admin_sidebar' => TenantUiSetting::getSection('admin_sidebar')->toArray(),
                'advanced' => TenantUiSetting::getSection('advanced')->toArray(),
            ];
            
            // Load admin credentials from the sample website database
            $adminCredentials = $this->getSampleWebsiteAdminCredentials();
            if ($adminCredentials) {
                $settings['general']['admin_email'] = $adminCredentials['email'];
                $settings['general']['admin_name'] = $adminCredentials['admin_name'];
            }
            
            // Switch back to main database
            config(['database.default' => 'mysql']);
            DB::purge('tenant');
            DB::connection('mysql');
            
            return $settings;
            
        } catch (\Exception $e) {
            // Ensure we switch back to main database
            config(['database.default' => 'mysql']);
            DB::purge('tenant');
            DB::connection('mysql');
            
            Log::warning('Failed to load sample website settings', [
                'error' => $e->getMessage()
            ]);
            
            // Return empty settings as fallback
            return [
                'general' => [],
                'navbar' => [],
                'branding' => [],
                'homepage' => [],
                'student_portal' => [],
                'professor_panel' => [],
                'admin_panel' => [],
                'student_sidebar' => [],
                'professor_sidebar' => [],
                'admin_sidebar' => [],
                'advanced' => [],
            ];
        }
    }
    
    /**
     * Get admin credentials from a specific website's database
     */
    private function getWebsiteAdminCredentials($websiteSlug)
    {
        try {
            // Switch to the website's database
            // Convert slug to database name format
            $slug = $websiteSlug;
            // Remove 'smartprep-' prefix if present and replace hyphens with underscores
            if (strpos($slug, 'smartprep-') === 0) {
                $slug = substr($slug, 10); // Remove 'smartprep-' prefix
            }
            $databaseName = 'smartprep_' . str_replace('-', '_', $slug);
            config(['database.connections.tenant.database' => $databaseName]);
            DB::purge('tenant');
            DB::connection('tenant');
            
            $admin = Admin::where('admin_id', 1)->first();
            
            // Switch back to main database
            config(['database.default' => 'mysql']);
            DB::purge('tenant');
            DB::connection('mysql');
            
            if ($admin) {
                return [
                    'email' => $admin->email,
                    'admin_name' => $admin->admin_name,
                ];
            }
        } catch (\Exception $e) {
            // Ensure we switch back to main database
            config(['database.default' => 'mysql']);
            DB::purge('tenant');
            DB::connection('mysql');
            
            Log::warning('Failed to get admin credentials from website database', [
                'website' => $websiteSlug,
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }
    
    /**
     * Get admin credentials from the sample website database (smartprep_artc)
     */
    private function getSampleWebsiteAdminCredentials()
    {
        try {
            // Switch to sample website database
            config(['database.connections.tenant.database' => 'smartprep_artc']);
            DB::purge('tenant');
            DB::connection('tenant');
            
            $admin = Admin::where('admin_id', 1)->first();
            
            // Switch back to main database
            config(['database.default' => 'mysql']);
            DB::purge('tenant');
            DB::connection('mysql');
            
            if ($admin) {
                return [
                    'email' => $admin->email,
                    'admin_name' => $admin->admin_name,
                ];
            }
        } catch (\Exception $e) {
            // Ensure we switch back to main database
            config(['database.default' => 'mysql']);
            DB::purge('tenant');
            DB::connection('mysql');
            
            Log::warning('Failed to get admin credentials from sample website database', [
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }
    

    
    /**
     * Update admin password in the tenant database
     */
    private function updateAdminInTenantDatabase($password)
    {
        try {
            $admin = Admin::where('admin_id', 1)->first();
            if ($admin) {
                $admin->password = $password; // This will be hashed by the model
                $admin->save();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update admin password in tenant database', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update admin email in the tenant database
     */
    private function updateAdminEmailInTenantDatabase($email)
    {
        try {
            $admin = Admin::where('admin_id', 1)->first();
            if ($admin) {
                $admin->email = $email;
                $admin->save();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update admin email in tenant database', [
                'error' => $e->getMessage()
            ]);
        }
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

            return redirect()->route('smartprep.dashboard.customize', ['website' => $client->id])
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
                    $client->domain,
                    $client->db_name // pass explicit db name to avoid suffix mismatch
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
            'admin_email' => 'nullable|email|max:255',
            'admin_password' => 'nullable|string|min:8|max:255',
            'brand_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'terms_conditions' => 'nullable|string|max:2000',
            'social_links' => 'nullable|string',
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

            // Get selected website/client
            $websiteId = $request->query('website');
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
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'User not authenticated.'], 401)
                    : redirect()->back()->with('error', 'User not authenticated.');
            }

            $websiteId = $request->query('website');
            $client = Client::where('id', $websiteId)->where('user_id', $user->id)->first();
            if (!$client) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Website not found.'], 404)
                    : redirect()->back()->with('error', 'Website not found.');
            }

            $tenant = Tenant::where('slug', $client->slug)->first();
            if (!$tenant) {
                // Auto-create tenant if missing (ensures DB name consistency)
                $tenant = $this->tenantService->createTenant($client->name, $client->domain, $client->db_name);
            }

            $this->tenantService->switchToTenant($tenant);

            foreach ($request->only(array_keys($validationRules)) as $key => $value) {
                if ($value === null) {
                    continue;
                }
                if ($key === 'social_links' && is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        TenantUiSetting::set($section, $key, $value, 'text');
                    }
                } elseif ($key === 'admin_password' && !empty($value)) {
                    TenantUiSetting::set($section, 'admin_password_hash', bcrypt($value), 'text');
                    TenantUiSetting::set($section, 'admin_password_set_at', now()->toDateTimeString(), 'text');
                    $this->updateAdminInTenantDatabase($value);
                } elseif ($key === 'admin_email' && !empty($value)) {
                    TenantUiSetting::set($section, $key, $value, 'text');
                    $this->updateAdminEmailInTenantDatabase($value);
                } else {
                    TenantUiSetting::set($section, $key, $value, 'text');
                }
            }

            $this->tenantService->switchToMain();

            Log::info("Settings updated for section {$section} in client {$client->id}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst(str_replace('_', ' ', $section)) . ' settings updated successfully!'
                ]);
            }
            return redirect()->back()->with('success', ucfirst(str_replace('_', ' ', $section)) . ' settings updated successfully!');

        } catch (\Exception $e) {
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

