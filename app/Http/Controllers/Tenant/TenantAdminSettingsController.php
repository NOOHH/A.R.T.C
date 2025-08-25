<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\TenantService;

class TenantAdminSettingsController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display the settings page for the tenant.
     */
    public function index($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get all settings from ui_settings table
            $settings = DB::table('ui_settings')->get()->keyBy('setting_key');

            // Get admin settings from main database (these are global settings)
            $adminSettings = [];
            try {
                $this->tenantService->switchToMain();
                $adminSettingsData = DB::table('admin_settings')->get();
                foreach ($adminSettingsData as $setting) {
                    $adminSettings[$setting->setting_key] = $setting->setting_value;
                }
                $this->tenantService->switchToTenant($tenantModel);
            } catch (\Exception $e) {
                Log::warning('Admin settings not accessible: ' . $e->getMessage());
            }

            // Get professors from tenant database
            $professors = collect();
            try {
                $professors = DB::table('professors')->where('professor_archived', 0)->get();
            } catch (\Exception $e) {
                Log::warning('Professors table not found in tenant database: ' . $e->getMessage());
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-settings.admin-settings', compact('settings', 'tenantModel', 'professors', 'adminSettings'));

        } catch (\Exception $e) {
            Log::error('Tenant settings index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-settings.admin-settings', [
                'settings' => collect(),
                'tenantModel' => null,
                'professors' => collect(),
                'adminSettings' => []
            ]);
        }
    }

    /**
     * Update homepage settings for the tenant.
     */
    public function updateHomepage(Request $request, $tenant)
    {
        try {
            $request->validate([
                'homepage_title' => 'nullable|string|max:255',
                'homepage_subtitle' => 'nullable|string|max:500',
                'homepage_description' => 'nullable|string',
                'hero_image' => 'nullable|string',
                'show_programs_section' => 'boolean',
                'show_testimonials_section' => 'boolean',
                'show_stats_section' => 'boolean',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update homepage settings
            $this->updateSetting('homepage_title', $request->homepage_title);
            $this->updateSetting('homepage_subtitle', $request->homepage_subtitle);
            $this->updateSetting('homepage_description', $request->homepage_description);
            $this->updateSetting('hero_image', $request->hero_image);
            $this->updateSetting('show_programs_section', $request->has('show_programs_section') ? '1' : '0');
            $this->updateSetting('show_testimonials_section', $request->has('show_testimonials_section') ? '1' : '0');
            $this->updateSetting('show_stats_section', $request->has('show_stats_section') ? '1' : '0');

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.settings.index', ['tenant' => $tenant])
                ->with('success', 'Homepage settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant homepage settings update error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error updating homepage settings. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update navbar settings for the tenant.
     */
    public function updateNavbar(Request $request, $tenant)
    {
        try {
            $request->validate([
                'navbar_logo' => 'nullable|string',
                'navbar_style' => 'nullable|in:light,dark,transparent',
                'show_search' => 'boolean',
                'show_login_button' => 'boolean',
                'show_register_button' => 'boolean',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update navbar settings
            $this->updateSetting('navbar_logo', $request->navbar_logo);
            $this->updateSetting('navbar_style', $request->navbar_style);
            $this->updateSetting('show_search', $request->has('show_search') ? '1' : '0');
            $this->updateSetting('show_login_button', $request->has('show_login_button') ? '1' : '0');
            $this->updateSetting('show_register_button', $request->has('show_register_button') ? '1' : '0');

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.settings.index', ['tenant' => $tenant])
                ->with('success', 'Navbar settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant navbar settings update error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error updating navbar settings. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update footer settings for the tenant.
     */
    public function updateFooter(Request $request, $tenant)
    {
        try {
            $request->validate([
                'footer_text' => 'nullable|string',
                'footer_logo' => 'nullable|string',
                'show_social_links' => 'boolean',
                'show_contact_info' => 'boolean',
                'show_newsletter_signup' => 'boolean',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update footer settings
            $this->updateSetting('footer_text', $request->footer_text);
            $this->updateSetting('footer_logo', $request->footer_logo);
            $this->updateSetting('show_social_links', $request->has('show_social_links') ? '1' : '0');
            $this->updateSetting('show_contact_info', $request->has('show_contact_info') ? '1' : '0');
            $this->updateSetting('show_newsletter_signup', $request->has('show_newsletter_signup') ? '1' : '0');

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.settings.index', ['tenant' => $tenant])
                ->with('success', 'Footer settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant footer settings update error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error updating footer settings. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update enrollment settings for the tenant.
     */
    public function updateEnrollment(Request $request, $tenant)
    {
        try {
            $request->validate([
                'enrollment_enabled' => 'boolean',
                'require_approval' => 'boolean',
                'auto_approve' => 'boolean',
                'max_enrollments_per_student' => 'nullable|integer|min:1',
                'enrollment_deadline_days' => 'nullable|integer|min:1',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update enrollment settings
            $this->updateSetting('enrollment_enabled', $request->has('enrollment_enabled') ? '1' : '0');
            $this->updateSetting('require_approval', $request->has('require_approval') ? '1' : '0');
            $this->updateSetting('auto_approve', $request->has('auto_approve') ? '1' : '0');
            $this->updateSetting('max_enrollments_per_student', $request->max_enrollments_per_student);
            $this->updateSetting('enrollment_deadline_days', $request->enrollment_deadline_days);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.settings.index', ['tenant' => $tenant])
                ->with('success', 'Enrollment settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant enrollment settings update error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error updating enrollment settings. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update payment settings for the tenant.
     */
    public function updatePaymentTerms(Request $request, $tenant)
    {
        try {
            $request->validate([
                'payment_terms' => 'nullable|string',
                'payment_methods' => 'nullable|string',
                'currency' => 'nullable|string|max:10',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update payment settings
            $this->updateSetting('payment_terms', $request->payment_terms);
            $this->updateSetting('payment_methods', $request->payment_methods);
            $this->updateSetting('currency', $request->currency);
            $this->updateSetting('tax_rate', $request->tax_rate);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.settings.index', ['tenant' => $tenant])
                ->with('success', 'Payment settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant payment settings update error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error updating payment settings. Please try again.')
                ->withInput();
        }
    }

    /**
     * Helper method to update a setting in the tenant database.
     */
    private function updateSetting($key, $value)
    {
        $setting = DB::table('settings')->where('setting_key', $key)->first();
        
        if ($setting) {
            DB::table('settings')
                ->where('setting_key', $key)
                ->update([
                    'setting_value' => $value,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('settings')->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
