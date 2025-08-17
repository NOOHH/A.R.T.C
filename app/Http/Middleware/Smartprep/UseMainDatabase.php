<?php

namespace App\Http\Middleware\Smartprep;

use Closure;
use Illuminate\Http\Request;
use App\Services\TenantService;

class UseMainDatabase
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Force the main database (smartprep) for SmartPrep routes
        $this->tenantService->switchToMain();
        return $next($request);
    }
}
