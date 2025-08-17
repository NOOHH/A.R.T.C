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
