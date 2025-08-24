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
        return $this->withTenant($slug, function($tenant) use ($slug) {
            // Load homepage settings
            $settings = [
                'homepage' => \App\Models\Setting::getGroup('homepage')->toArray(),
                'navbar' => \App\Models\Setting::getGroup('navbar')->toArray(),
            ];
            $programs = DB::table('programs')->limit(6)->get();
            
            // Get tenant-specific homepage settings
            $homepageSettings = \App\Models\Setting::getGroup('homepage')->toArray();
            
            // Build homepageContent array similar to HomepageController
            $homepageContent = array_merge([
                'hero_title' => 'Welcome to Ascendo Review and Training Center',
                'hero_subtitle' => 'Your premier destination for comprehensive review programs and professional training.',
                'hero_button_text' => 'ENROLL NOW',
                'programs_title' => 'Our Programs',
                'programs_subtitle' => 'Choose from our comprehensive range of review and training programs',
                'modalities_title' => 'Learning Modalities',
                'modalities_subtitle' => 'Flexible learning options designed to fit your schedule and learning style',
                'about_title' => 'About Us',
                'about_subtitle' => 'We are committed to providing high-quality education and training'
            ], $homepageSettings);
            
            // For backward compatibility
            $homepageTitle = $homepageContent['hero_button_text'] ?? 'ENROLL NOW';
            
            // Pass tenant slug for proper enrollment routing
            $tenantSlug = $slug;
            
            return view('welcome.homepage', compact('settings','programs', 'tenantSlug', 'homepageContent', 'homepageTitle'));
        });
    }

    public function enrollment(string $slug)
    {
        return $this->withTenant($slug, function($tenant){
            // Load tenant-specific settings for enrollment page
            $settings = [
                'auth' => \App\Models\Setting::getGroup('auth')->toArray(),
                'navbar' => \App\Models\Setting::getGroup('navbar')->toArray(),
                'branding' => \App\Models\Setting::getGroup('branding')->toArray(),
            ];
            
            // Get programs for enrollment
            $programs = DB::table('programs')->get();
            
            return view('welcome.enrollment', compact('settings', 'programs'));
        });
    }

    // Dashboard preview methods removed; tenant dashboard preview now reuses original controllers for full ARTC format
}
