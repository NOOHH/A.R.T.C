<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\TenantService;
use App\Models\Tenant;

class PreviewController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    private function withTenant(string $slug, \Closure $callback)
    {
        $tenant = Tenant::where('slug', $slug)->first();
        if (!$tenant) {
            abort(404);
        }
        try {
            $this->tenantService->switchToTenant($tenant);
            return $callback($tenant);
        } finally {
            $this->tenantService->switchToMain();
        }
    }

    public function homepage(string $slug)
    {
        return $this->withTenant($slug, function($tenant){
            // Load homepage settings
            $settings = [
                'homepage' => \App\Models\Setting::getGroup('homepage')->toArray(),
                'navbar' => \App\Models\Setting::getGroup('navbar')->toArray(),
            ];
            $programs = DB::table('programs')->limit(6)->get();
            return view('welcome.homepage', compact('settings','programs'));
        });
    }

    // Dashboard preview methods removed; tenant dashboard preview now reuses original controllers for full ARTC format
}
