<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UiSetting;
use App\Models\Client;
use Illuminate\Support\Str;

class CustomizeWebsiteController extends Controller
{
    public function current()
    {
        // Gather UI settings (global preview overrides still come from ARTC base for now)
        $settings = [
            'general' => UiSetting::getSection('general')->toArray(),
            'navbar' => UiSetting::getSection('navbar')->toArray(),
            'branding' => UiSetting::getSection('branding')->toArray(),
            'homepage' => UiSetting::getSection('homepage')->toArray(),
        ];

        $requestedWebsiteId = request('website');
        $selectedWebsite = null;

        // Build list of websites user can manage
        $query = Client::query()->where('archived', false)->orderByDesc('created_at');
        $isAdmin = Auth::guard('smartprep_admin')->check();
        if (!$isAdmin) {
            $query->where('user_id', Auth::guard('smartprep')->id());
        }
        $activeWebsites = $query->get();

        if ($requestedWebsiteId) {
            $selectedWebsite = $activeWebsites->firstWhere('id', (int)$requestedWebsiteId);
        }
        if (!$selectedWebsite && $activeWebsites->count() === 1) {
            $selectedWebsite = $activeWebsites->first();
        }

        // Dynamic preview: if a specific website is selected and corresponding tenant exists use /t/{slug}
        $previewUrl = url('/artc');
        if ($selectedWebsite) {
            // Ensure a tenant row exists for this client (auto-create if missing)
            $tenant = $this->ensureTenantForClient($selectedWebsite);
            if ($tenant) {
                $previewUrl = url('/t/' . $tenant->slug);
            }
        }

        $navbarBrandName = $settings['navbar']['brand_name'] ?? ($selectedWebsite->name ?? 'Ascendo Review and Training Center');

        return view('smartprep.dashboard.customize-website', compact('navbarBrandName', 'settings', 'previewUrl', 'activeWebsites', 'selectedWebsite'));
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

    /**
     * Delete a user website (soft delete archiving + drop tenant db if draft) then redirect back.
     */
    public function destroy($id)
    {
        $isAdmin = Auth::guard('smartprep_admin')->check();
        $client = Client::findOrFail($id);
        if (!$isAdmin && $client->user_id !== Auth::guard('smartprep')->id()) {
            abort(403, 'Not authorized');
        }
        // Always attempt to drop associated database if present
        try {
            if ($client->db_name) { \App\Services\TenantProvisioner::dropDatabase($client->db_name); }
        } catch (\Throwable $e) { /* ignore */ }
        // Remove tenant registry if exists
        \App\Models\Tenant::where('slug', $client->slug)->delete();
        $client->delete();
        return back()->with('success', 'Website deleted');
    }

    /**
     * Create a new website draft for the current user (or admin assigns owner).
     */
    public function store(Request $request)
    {
        $isAdmin = Auth::guard('smartprep_admin')->check();
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:draft,active,inactive'
        ]);
        $userId = $isAdmin ? ($request->input('user_id') ?: Auth::guard('smartprep_admin')->id()) : Auth::guard('smartprep')->id();
        $slugBase = 'smartprep-' . Str::slug($request->input('name'));
        $slug = $slugBase;
        $i = 1;
        while (Client::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $i++;
        }
        $conn = null;
        try {
            // Attempt to provision an empty (template) DB for the draft using SQL dump
            $conn = \App\Services\TenantProvisioner::createDatabaseFromSqlDump($request->input('name'));
        } catch (\Throwable $e) {
            // Continue; database can be provisioned later
        }
        $client = Client::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'status' => $request->input('status','draft'),
            'user_id' => $userId,
            'archived' => false,
            'db_name' => $conn['db_name'] ?? null,
            'db_host' => $conn['db_host'] ?? null,
            'db_port' => $conn['db_port'] ?? null,
            'db_username' => $conn['db_username'] ?? null,
            'db_password' => $conn['db_password'] ?? null,
        ]);
        // Ensure tenant row exists
        $this->ensureTenantForClient($client, $conn['db_name'] ?? null);
        return redirect()->route('smartprep.dashboard.customize', ['website' => $client->id])
            ->with('success', 'Draft website created');
    }

    /**
     * Update website meta (name/status) and optionally save draft settings.
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $isAdmin = Auth::guard('smartprep_admin')->check();
        if (!$isAdmin && $client->user_id !== Auth::guard('smartprep')->id()) {
            abort(403, 'Not authorized');
        }
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:draft,active,inactive'
        ]);
        $dirty = [];
        if ($request->filled('name')) { $dirty['name'] = $request->input('name'); }
        if ($request->filled('status')) { $dirty['status'] = $request->input('status'); }
        if ($dirty) { $client->update($dirty); }
        // Save draft UI settings snapshot (basic fields) to ui_settings under a composite key (scoped by client id)
        if ($request->has('settings') && is_array($request->input('settings'))) {
            foreach ($request->input('settings') as $section => $pairs) {
                if (is_array($pairs)) {
                    foreach ($pairs as $k => $v) {
                        UiSetting::set('client_'.$client->id.'_'.$section, $k, (string)$v, 'text');
                    }
                }
            }
        }
        return back()->with('success', 'Website updated');
    }

    /**
     * Ensure a tenant record exists for the client; create or update.
     */
    private function ensureTenantForClient(Client $client, $dbNameOverride = null)
    {
        $payload = [
            'name' => $client->name,
            'database_name' => $dbNameOverride ?: $client->db_name,
            'domain' => $client->domain,
            'status' => $client->status ?? 'draft',
            'settings' => json_encode(['client_id' => $client->id])
        ];
        if (!$payload['database_name']) {
            // If database still missing try to derive a predictable name from slug second segment (smartprep-<name>)
            // Previous implementation used index [2] which was often undefined and defaulted to 'draft'.
            $parts = explode('-', $client->slug);
            $keyword = $parts[1] ?? $client->slug; // e.g. smartprep-tening => 'tening'
            $payload['database_name'] = 'smartprep_' . Str::slug($keyword, '_');
        }
        return \App\Models\Tenant::updateOrCreate(['slug' => $client->slug], $payload);
    }
}
