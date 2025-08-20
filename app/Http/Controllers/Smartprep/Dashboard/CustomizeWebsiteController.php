<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UiSetting;
use App\Models\Client;

class CustomizeWebsiteController extends Controller
{
    public function current()
    {
        // Gather all UI settings to drive the preview and sidebar defaults
        $settings = [
            'general' => UiSetting::getSection('general')->toArray(),
            'navbar' => UiSetting::getSection('navbar')->toArray(),
            'branding' => UiSetting::getSection('branding')->toArray(),
            'homepage' => UiSetting::getSection('homepage')->toArray(),
        ];

        // Compute preview URL: prefer admin-configured preview_url, else point to ARTC preview
        // Force SmartPrep dashboard preview to always show ARTC
        $previewUrl = url('/artc');

        // Fallback brand name for header
        $navbarBrandName = $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center';

        // Populate selectable websites for this user/admin
        $activeWebsitesQuery = Client::query()->where('archived', false)->orderByDesc('created_at');
        if (!Auth::guard('smartprep_admin')->check()) {
            $activeWebsitesQuery->where('user_id', Auth::guard('smartprep')->id());
        }
        $activeWebsites = $activeWebsitesQuery->get();

        return view('smartprep.dashboard.customize-website', compact('navbarBrandName', 'settings', 'previewUrl', 'activeWebsites'));
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
