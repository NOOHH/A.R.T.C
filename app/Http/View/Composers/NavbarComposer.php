<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\UiSetting;
use App\Models\Setting;
use App\Models\Tenant;
use App\Helpers\SettingsHelper;
use App\Services\TenantService;

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
                    $navbar['brand_name'] = $tenant->name ?? $tenant->slug ?? 'Your Company Name';
                } else {
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
        
        // Check for tenant in path-based routing (/t/{slug})
        if ($request->is('t/*')) {
            $segments = $request->segments();
            if ($segments[0] === 't') {
                // Patterns:
                //  /t/{slug}
                //  /t/{slug}/... (additional paths)
                //  /t/draft/{slug}
                //  /t/draft/{slug}/... (additional paths)
                if (isset($segments[1]) && $segments[1] === 'draft') {
                    $tenantSlug = $segments[2] ?? null; // draft preview path
                } else {
                    $tenantSlug = $segments[1] ?? null;
                }
                if ($tenantSlug) {
                    return Tenant::where('slug', $tenantSlug)->first();
                }
            }
        }
        
        // Check for tenant in subdomain routing
        $domain = $request->getHost();
        if (!in_array($domain, ['localhost', '127.0.0.1', 'artc.test'])) {
            return Tenant::where('domain', $domain)->first();
        }
        
        return null;
    }
}
