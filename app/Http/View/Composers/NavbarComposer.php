<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\UiSetting;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\Client;
use App\Helpers\SettingsHelper;
use App\Services\TenantService;
use Illuminate\Support\Facades\Log;

class NavbarComposer
{
    protected $tenantService;
    
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    
    /**
     * Bind navbar data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $navbar = [];
        
        try {
            // Check if we're in a tenant context
            $tenant = $this->getCurrentTenant();
            
            if ($tenant) {
                // We're in a tenant context, load settings from tenant database
                $this->tenantService->switchToTenant($tenant);
                
                $navbarSettings = Setting::getGroup('navbar');
                if ($navbarSettings) {
                    $navbar = $navbarSettings->toArray();
                }
                
                // Switch back to main database
                $this->tenantService->switchToMain();
            } else {
                // We're in main context, load settings from main database
                $navbarSettings = UiSetting::getSection('navbar');
                
                // Convert to array if it's a collection
                if ($navbarSettings && method_exists($navbarSettings, 'toArray')) {
                    $navbar = $navbarSettings->toArray();
                } else {
                    $navbar = $navbarSettings ?: [];
                }
            }
            
            // Ensure brand_name is always available with appropriate fallbacks
            if (empty($navbar['brand_name'])) {
                if ($tenant) {
                    // For tenants, use a generic fallback
                    $navbar['brand_name'] = 'Your Company Name';
                } else {
                    // For main context, use the default
                    $fallbackSettings = SettingsHelper::getSettings();
                    $navbar['brand_name'] = $fallbackSettings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center';
                }
            }
            
        } catch (\Exception $e) {
            // Fallback to SettingsHelper if database fails
            $fallbackSettings = SettingsHelper::getSettings();
            $navbar = $fallbackSettings['navbar'] ?? [
                'brand_name' => 'Your Company Name',
                'background_color' => '#f1f1f1',
                'text_color' => '#222222'
            ];
        }
        
        // Always ensure navbar data is available
        $view->with('navbar', $navbar);
        
        // Also provide settings in the format expected by login pages
        $settings = [
            'navbar' => $navbar
        ];
        $view->with('settings', $settings);
        
        // Also provide uiSettings in the format expected by SmartPrep views
        $uiSettings = [
            'navbar' => $navbar
        ];
        $view->with('uiSettings', $uiSettings);
    }
    
    /**
     * Get current tenant from request context
     */
    private function getCurrentTenant()
    {
        $request = request();
        
        // First priority: Check for website parameter (used in preview mode)
        if ($request->has('website')) {
            $websiteId = $request->get('website');
            
            // IMPORTANT: Use main database connection explicitly for Client lookup
            // because TenantMiddleware may have already switched the default connection
            $client = \App\Models\Client::on('mysql')->find($websiteId);
            if ($client) {
                // For navbar composer, we want to use client's database, not tenant's
                // Create a pseudo-tenant with the client's database name
                $tenant = new \App\Models\Tenant();
                $tenant->slug = $client->slug;
                $tenant->database_name = $client->db_name;
                return $tenant;
            }
        }
        
        // Second priority: Check for tenant in path-based routing (/t/{slug})
        if ($request->is('t/*')) {
            $segments = $request->segments();
            if (count($segments) >= 2 && $segments[0] === 't') {
                $tenantSlug = $segments[1];
                $tenant = Tenant::where('slug', $tenantSlug)->first();
                if ($tenant) {
                    // Check if there's a matching client for this tenant
                    $client = \App\Models\Client::on('mysql')->where('slug', $tenant->slug)->first();
                    if ($client) {
                        // Use client's database instead of tenant's
                        $tenant->database_name = $client->db_name;
                    }
                }
                return $tenant;
            }
        }
        
        // Third priority: Check for tenant in subdomain routing
        $domain = $request->getHost();
        if (!in_array($domain, ['localhost', '127.0.0.1', 'artc.test'])) {
            $tenant = Tenant::where('domain', $domain)->first();
            if ($tenant) {
                // Check if there's a matching client for this tenant
                $client = \App\Models\Client::on('mysql')->where('slug', $tenant->slug)->first();
                if ($client) {
                    // Use client's database instead of tenant's
                    $tenant->database_name = $client->db_name;
                }
            }
            return $tenant;
        }
        
        return null;
    }
}
