<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;

class TenantMiddleware
{
    protected $tenantService;
    
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip tenant switching for SmartPrep routes - they should always use main DB
        if ($request->is('smartprep/*')) {
            $this->tenantService->switchToMain();
            return $next($request);
        }

        // For path-based tenant routes (/t/{slug}), defer DB switching to the controller
        // and keep the default connection on the main database so models like Client/Tenant
        // query the correct schema.
        if ($request->is('t/*')) {
            $this->tenantService->switchToMain();
            return $next($request);
        }
        
        // Check for preview mode with website parameter - handle this before domain-based switching
        if ($request->has('preview') && $request->get('preview') === 'true' && $request->has('website')) {
            $websiteId = $request->get('website');
            $tenant = \App\Models\Tenant::find($websiteId);
            if ($tenant) {
                $this->tenantService->switchToTenant($tenant);
                $request->attributes->set('tenant', $tenant);
                return $next($request);
            }
        }
        
        $domain = $request->getHost();
        
        // For development, handle localhost/127.0.0.1
        if (in_array($domain, ['localhost', '127.0.0.1', 'artc.test'])) {
            $domain = 'artc.smartprep.local'; // Default to ARTC for development
        }
        
        // Try to find tenant by domain
        $tenant = $this->tenantService->getTenantByDomain($domain);
        
        if ($tenant) {
            // Switch to tenant database
            $this->tenantService->switchToTenant($tenant);
            
            // Store tenant in request for later use
            $request->attributes->set('tenant', $tenant);
        } else {
            // If no tenant found, use main database (for admin panel, etc.)
            $this->tenantService->switchToMain();
        }

        return $next($request);
    }
}
