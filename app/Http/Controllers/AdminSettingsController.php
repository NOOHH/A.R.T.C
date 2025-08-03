<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\FormRequirement;
use App\Models\UiSetting;
use Illuminate\Support\Str;
use App\Models\AdminSetting;
use App\Models\PaymentMethod;
use App\Services\DynamicFieldService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        // Only apply to directors
        $isDirector = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'director')
            || (session('user_type') === 'director');
        if ($isDirector) {
            $canManage = AdminSetting::getValue('director_manage_settings', 'false') === 'true' || AdminSetting::getValue('director_manage_settings', '0') === '1';
            if (!$canManage) {
                abort(403, 'Access denied: You do not have permission to manage settings.');
            }
        }
    }

    public function index()
    {
        // Check if user is admin
        if (!session('user_type') || session('user_type') !== 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Access denied. Settings is only available for admins.');
        }

        // Get current settings from config/storage or database
        $settings = $this->getCurrentSettings();
        
        return view('admin.admin-settings.admin-settings', compact('settings'));
    }

    public function updateHomepage(Request $request)
    {
        $request->validate([
            'homepage_background_color' => 'nullable|string|max:7',
            'homepage_gradient_color' => 'nullable|string|max:7',
            'homepage_text_color' => 'nullable|string|max:7',
            'homepage_background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            'homepage_title' => 'nullable|string|max:255',
            'remove_background_image' => 'nullable|boolean',
        ]);

        $settings = $this->getCurrentSettings();

        // Update homepage settings - Always update these values
        $settings['homepage'] = array_merge($settings['homepage'] ?? [], [
            'background_color' => $request->input('homepage_background_color', $settings['homepage']['background_color'] ?? '#667eea'),
            'gradient_color' => $request->input('homepage_gradient_color', $settings['homepage']['gradient_color'] ?? ''),
            'text_color' => $request->input('homepage_text_color', $settings['homepage']['text_color'] ?? '#ffffff'),
            'title' => $request->input('homepage_title', $settings['homepage']['title'] ?? 'ENROLL NOW'),
        ]);

        // Handle image removal first
        if ($request->input('remove_background_image')) {
            if (isset($settings['homepage']['background_image']) && Storage::disk('public')->exists($settings['homepage']['background_image'])) {
                Storage::disk('public')->delete($settings['homepage']['background_image']);
            }
            unset($settings['homepage']['background_image']);
        }

        // Handle background image upload
        if ($request->hasFile('homepage_background_image')) {
            // Delete old image if exists
            if (isset($settings['homepage']['background_image']) && Storage::disk('public')->exists($settings['homepage']['background_image'])) {
                Storage::disk('public')->delete($settings['homepage']['background_image']);
            }

            $imagePath = $request->file('homepage_background_image')->store('settings/homepage', 'public');
            $settings['homepage']['background_image'] = $imagePath;
        }

        $this->saveSettings($settings);

        return back()->with('success', 'Homepage settings updated successfully!');
    }

    public function updateNavbar(Request $request)
    {
        $request->validate([
            'navbar_background_color' => 'nullable|string|max:7',
            'navbar_gradient_color' => 'nullable|string|max:7',
            'navbar_text_color' => 'nullable|string|max:7',
            'navbar_brand_name' => 'nullable|string|max:255',
            'navbar_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:5120', // 5MB max
        ]);

        $settings = $this->getCurrentSettings();

        // Update navbar settings
        $settings['navbar'] = array_merge($settings['navbar'] ?? [], [
            'background_color' => $request->navbar_background_color ?? $settings['navbar']['background_color'] ?? '#f1f1f1',
            'gradient_color' => $request->navbar_gradient_color ?? $settings['navbar']['gradient_color'] ?? '',
            'text_color' => $request->navbar_text_color ?? $settings['navbar']['text_color'] ?? '#222222',
            'brand_name' => $request->navbar_brand_name ?? $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center',
        ]);

        // Handle logo upload
        if ($request->hasFile('navbar_logo')) {
            // Delete old logo if exists
            if (isset($settings['navbar']['logo']) && Storage::disk('public')->exists($settings['navbar']['logo'])) {
                Storage::disk('public')->delete($settings['navbar']['logo']);
            }

            $logoPath = $request->file('navbar_logo')->store('settings/navbar', 'public');
            $settings['navbar']['logo'] = $logoPath;
        }

        $this->saveSettings($settings);

        return back()->with('success', 'Navbar settings updated successfully!');
    }

    public function updateFooter(Request $request)
    {
        $request->validate([
            'footer_background_color' => 'nullable|string|max:7',
            'footer_gradient_color' => 'nullable|string|max:7',
            'footer_text_color' => 'nullable|string|max:7',
            'footer_text' => 'nullable|string|max:500',
        ]);

        $settings = $this->getCurrentSettings();

        // Update footer settings
        $settings['footer'] = array_merge($settings['footer'] ?? [], [
            'background_color' => $request->footer_background_color ?? $settings['footer']['background_color'] ?? '#ffffff',
            'gradient_color' => $request->footer_gradient_color ?? $settings['footer']['gradient_color'] ?? '',
            'text_color' => $request->footer_text_color ?? $settings['footer']['text_color'] ?? '#444444',
            'text' => $request->footer_text ?? $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.',
        ]);

        $this->saveSettings($settings);

        return back()->with('success', 'Footer settings updated successfully!');
    }

    public function updateProgramCards(Request $request)
    {
        $request->validate([
            'program_card_background_color' => 'nullable|string|max:7',
            'program_card_gradient_color' => 'nullable|string|max:7',
            'program_card_text_color' => 'nullable|string|max:7',
            'program_card_border_color' => 'nullable|string|max:7',
            'program_card_hover_color' => 'nullable|string|max:7',
            'program_cards_background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            'remove_background_image' => 'nullable|boolean',
        ]);

        $settings = $this->getCurrentSettings();

        // Update program card settings only
        $settings['program_cards'] = array_merge($settings['program_cards'] ?? [], [
            'background_color' => $request->input('program_card_background_color', $settings['program_cards']['background_color'] ?? '#f9f9f9'),
            'gradient_color' => $request->input('program_card_gradient_color', $settings['program_cards']['gradient_color'] ?? ''),
            'text_color' => $request->input('program_card_text_color', $settings['program_cards']['text_color'] ?? '#333333'),
            'border_color' => $request->input('program_card_border_color', $settings['program_cards']['border_color'] ?? '#dddddd'),
            'hover_color' => $request->input('program_card_hover_color', $settings['program_cards']['hover_color'] ?? '#1c2951'),
        ]);

        // Handle image removal first
        if ($request->input('remove_background_image')) {
            if (isset($settings['program_cards']['background_image']) && Storage::disk('public')->exists($settings['program_cards']['background_image'])) {
                Storage::disk('public')->delete($settings['program_cards']['background_image']);
            }
            unset($settings['program_cards']['background_image']);
        }

        // Handle background image upload
        if ($request->hasFile('program_cards_background_image')) {
            // Delete old image if exists
            if (isset($settings['program_cards']['background_image']) && Storage::disk('public')->exists($settings['program_cards']['background_image'])) {
                Storage::disk('public')->delete($settings['program_cards']['background_image']);
            }

            $imagePath = $request->file('program_cards_background_image')->store('settings/program_cards', 'public');
            $settings['program_cards']['background_image'] = $imagePath;
        }

        $this->saveSettings($settings);

        return back()->with('success', 'Program cards settings updated successfully!');
    }

    public function updateEnrollment(Request $request)
    {
        $request->validate([
            'enrollment_button_color' => 'nullable|string|max:7',
            'enrollment_button_text_color' => 'nullable|string|max:7',
            'enrollment_button_hover_color' => 'nullable|string|max:7',
            'enrollment_page_background_color' => 'nullable|string|max:7',
            'enrollment_page_gradient_color' => 'nullable|string|max:7',
            'enrollment_page_text_color' => 'nullable|string|max:7',
            'enrollment_form_background_color' => 'nullable|string|max:7',
        ]);

        $settings = $this->getCurrentSettings();

        // Update enrollment settings
        $settings['enrollment'] = array_merge($settings['enrollment'] ?? [], [
            'button_color' => $request->input('enrollment_button_color', $settings['enrollment']['button_color'] ?? '#667eea'),
            'button_text_color' => $request->input('enrollment_button_text_color', $settings['enrollment']['button_text_color'] ?? '#ffffff'),
            'button_hover_color' => $request->input('enrollment_button_hover_color', $settings['enrollment']['button_hover_color'] ?? '#5a67d8'),
            'page_background_color' => $request->input('enrollment_page_background_color', $settings['enrollment']['page_background_color'] ?? '#f8f9fa'),
            'page_gradient_color' => $request->input('enrollment_page_gradient_color', $settings['enrollment']['page_gradient_color'] ?? ''),
            'page_text_color' => $request->input('enrollment_page_text_color', $settings['enrollment']['page_text_color'] ?? '#333333'),
            'form_background_color' => $request->input('enrollment_form_background_color', $settings['enrollment']['form_background_color'] ?? '#ffffff'),
        ]);

        $this->saveSettings($settings);

        return back()->with('success', 'Enrollment settings updated successfully!');
    }

    public function updateButtons(Request $request)
    {
        $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'primary_text_color' => 'nullable|string|max:7',
            'primary_hover_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'secondary_text_color' => 'nullable|string|max:7',
            'secondary_hover_color' => 'nullable|string|max:7',
            'success_color' => 'nullable|string|max:7',
            'success_text_color' => 'nullable|string|max:7',
            'success_hover_color' => 'nullable|string|max:7',
            'danger_color' => 'nullable|string|max:7',
            'danger_text_color' => 'nullable|string|max:7',
            'danger_hover_color' => 'nullable|string|max:7',
        ]);

        $settings = $this->getCurrentSettings();

        // Update button settings
        $settings['buttons'] = array_merge($settings['buttons'] ?? [], [
            'primary_color' => $request->primary_color ?? $settings['buttons']['primary_color'] ?? '#667eea',
            'primary_text_color' => $request->primary_text_color ?? $settings['buttons']['primary_text_color'] ?? '#ffffff',
            'primary_hover_color' => $request->primary_hover_color ?? $settings['buttons']['primary_hover_color'] ?? '#5a67d8',
            'secondary_color' => $request->secondary_color ?? $settings['buttons']['secondary_color'] ?? '#6c757d',
            'secondary_text_color' => $request->secondary_text_color ?? $settings['buttons']['secondary_text_color'] ?? '#ffffff',
            'secondary_hover_color' => $request->secondary_hover_color ?? $settings['buttons']['secondary_hover_color'] ?? '#5a6268',
            'success_color' => $request->success_color ?? $settings['buttons']['success_color'] ?? '#28a745',
            'success_text_color' => $request->success_text_color ?? $settings['buttons']['success_text_color'] ?? '#ffffff',
            'success_hover_color' => $request->success_hover_color ?? $settings['buttons']['success_hover_color'] ?? '#218838',
            'danger_color' => $request->danger_color ?? $settings['buttons']['danger_color'] ?? '#dc3545',
            'danger_text_color' => $request->danger_text_color ?? $settings['buttons']['danger_text_color'] ?? '#ffffff',
            'danger_hover_color' => $request->danger_hover_color ?? $settings['buttons']['danger_hover_color'] ?? '#c82333',
        ]);

        $this->saveSettings($settings);

        return back()->with('success', 'Button settings updated successfully!');
    }

    public function updateLogin(Request $request)
    {
        $request->validate([
            'login_background_color' => 'nullable|string|max:7',
            'login_gradient_color' => 'nullable|string|max:7',
            'login_text_color' => 'nullable|string|max:7',
            'login_accent_color' => 'nullable|string|max:7',
            'login_card_background' => 'nullable|string|max:7',
            'login_input_border_color' => 'nullable|string|max:7',
            'login_input_focus_color' => 'nullable|string|max:7',
            'login_background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            'login_illustration' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:5120', // 5MB max
        ]);

        $settings = $this->getCurrentSettings();

        // Update login settings
        $settings['login'] = array_merge($settings['login'] ?? [], [
            'background_color' => $request->login_background_color ?? $settings['login']['background_color'] ?? '#f8f9fa',
            'gradient_color' => $request->login_gradient_color ?? $settings['login']['gradient_color'] ?? '',
            'text_color' => $request->login_text_color ?? $settings['login']['text_color'] ?? '#333333',
            'accent_color' => $request->login_accent_color ?? $settings['login']['accent_color'] ?? '#667eea',
            'card_background' => $request->login_card_background ?? $settings['login']['card_background'] ?? '#ffffff',
            'input_border_color' => $request->login_input_border_color ?? $settings['login']['input_border_color'] ?? '#dee2e6',
            'input_focus_color' => $request->login_input_focus_color ?? $settings['login']['input_focus_color'] ?? '#667eea',
        ]);

        // Handle background image upload
        if ($request->hasFile('login_background_image')) {
            // Delete old image if exists
            if (isset($settings['login']['background_image']) && Storage::disk('public')->exists($settings['login']['background_image'])) {
                Storage::disk('public')->delete($settings['login']['background_image']);
            }

            $imagePath = $request->file('login_background_image')->store('settings/login', 'public');
            $settings['login']['background_image'] = $imagePath;
        }

        // Handle illustration upload
        if ($request->hasFile('login_illustration')) {
            // Delete old illustration if exists
            if (isset($settings['login']['login_illustration']) && Storage::disk('public')->exists($settings['login']['login_illustration'])) {
                Storage::disk('public')->delete($settings['login']['login_illustration']);
            }

            $illustrationPath = $request->file('login_illustration')->store('settings/login/illustrations', 'public');
            $settings['login']['login_illustration'] = $illustrationPath;
        }

        $this->saveSettings($settings);

        return back()->with('success', 'Login page settings updated successfully!');
    }

    public function updateGlobalLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,jpg,png,svg,webp|max:2048',
                'logo_position' => 'nullable|in:left,center,right',
                'site_title' => 'nullable|string|max:255',
                'show_on_all_pages' => 'nullable|boolean'
            ]);

            $logoPath = null;
            
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                $oldLogo = UiSetting::get('global', 'logo_path');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                
                // Store new logo
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('logos', $filename, 'public');
                
                // Save to database
                UiSetting::set('global', 'logo_path', $logoPath, 'file');
                UiSetting::set('global', 'logo_url', Storage::url($logoPath), 'text');
            }
            
            // Save other settings
            if ($request->has('logo_position')) {
                UiSetting::set('global', 'logo_position', $request->logo_position);
            }
            
            if ($request->has('site_title')) {
                UiSetting::set('global', 'site_title', $request->site_title);
            }
            
            UiSetting::set('global', 'show_on_all_pages', $request->has('show_on_all_pages') ? '1' : '0', 'boolean');
            
            return response()->json([
                'success' => true,
                'logo_url' => $logoPath ? Storage::url($logoPath) : null,
                'message' => 'Logo updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating logo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating logo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateFavicon(Request $request)
    {
        try {
            $request->validate([
                'favicon' => 'required|file|mimes:ico,png|max:1024'
            ]);
            
            if ($request->hasFile('favicon')) {
                // Delete old favicon if exists
                $oldFavicon = UiSetting::get('global', 'favicon_path');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }
                
                // Store new favicon
                $file = $request->file('favicon');
                $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
                $faviconPath = $file->storeAs('favicons', $filename, 'public');
                
                // Save to database
                UiSetting::set('global', 'favicon_path', $faviconPath, 'file');
                UiSetting::set('global', 'favicon_url', Storage::url($faviconPath), 'text');
                
                // Copy favicon to public root for direct access
                $publicPath = public_path('favicon.' . $file->getClientOriginalExtension());
                copy(storage_path('app/public/' . $faviconPath), $publicPath);
                
                return response()->json([
                    'success' => true,
                    'favicon_url' => Storage::url($faviconPath),
                    'message' => 'Favicon updated successfully!'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating favicon: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating favicon: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateGlobalSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'app_keywords' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:2048',
            'app_favicon' => 'nullable|image|mimes:ico,png|max:1024',
        ]);

        $settings = $this->getCurrentSettings();

        // Update app name and description
        $settings['app_name'] = $request->input('app_name');
        $settings['app_description'] = $request->input('app_description', $settings['app_description'] ?? '');
        $settings['app_keywords'] = $request->input('app_keywords', $settings['app_keywords'] ?? '');

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            // Delete old logo if exists
            if (isset($settings['app_logo']) && Storage::disk('public')->exists($settings['app_logo'])) {
                Storage::disk('public')->delete($settings['app_logo']);
            }

            $logoPath = $request->file('app_logo')->store('settings/logos', 'public');
            $settings['app_logo'] = $logoPath;
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            // Delete old favicon if exists
            if (isset($settings['app_favicon']) && Storage::disk('public')->exists($settings['app_favicon'])) {
                Storage::disk('public')->delete($settings['app_favicon']);
            }

            $faviconPath = $request->file('app_favicon')->store('settings/favicons', 'public');
            $settings['app_favicon'] = $faviconPath;
        }

        $this->saveSettings($settings);

        return back()->with('success', 'Global settings updated successfully!');
    }

    public function updateSeoSettings(Request $request)
    {
        $request->validate([
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_robots' => 'nullable|string|max:255',
        ]);

        $settings = $this->getCurrentSettings();

        // Update SEO settings
        $settings['seo'] = array_merge($settings['seo'] ?? [], [
            'title' => $request->input('seo_title', $settings['seo']['title'] ?? ''),
            'description' => $request->input('seo_description', $settings['seo']['description'] ?? ''),
            'keywords' => $request->input('seo_keywords', $settings['seo']['keywords'] ?? ''),
            'robots' => $request->input('seo_robots', $settings['seo']['robots'] ?? ''),
        ]);

        $this->saveSettings($settings);

        return back()->with('success', 'SEO settings updated successfully!');
    }

    private function getCurrentSettings()
    {
        $settingsPath = storage_path('app/settings.json');
        
        if (File::exists($settingsPath)) {
            return json_decode(File::get($settingsPath), true);
        }

        // Default settings
        return [
            'homepage' => [
                'background_color' => '#667eea',
                'gradient_color' => '',
                'text_color' => '#ffffff',
                'title' => 'ENROLL NOW',
                'background_image' => null,
            ],
            'navbar' => [
                'background_color' => '#f1f1f1',
                'gradient_color' => '',
                'text_color' => '#222222',
                'brand_name' => 'Ascendo Review and Training Center',
                'logo' => null,
            ],
            'footer' => [
                'background_color' => '#ffffff',
                'gradient_color' => '',
                'text_color' => '#444444',
                'text' => '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.',
            ],
            'program_cards' => [
                'background_color' => '#f9f9f9',
                'gradient_color' => '',
                'text_color' => '#333333',
                'border_color' => '#dddddd',
                'hover_color' => '#1c2951',
                'background_image' => null,
            ],
            'enrollment' => [
                'button_color' => '#667eea',
                'button_text_color' => '#ffffff',
                'button_hover_color' => '#5a67d8',
                'page_background_color' => '#f8f9fa',
                'page_gradient_color' => '',
                'page_text_color' => '#333333',
                'form_background_color' => '#ffffff',
            ],
            'buttons' => [
                'primary_color' => '#667eea',
                'primary_text_color' => '#ffffff',
                'primary_hover_color' => '#5a67d8',
                'secondary_color' => '#6c757d',
                'secondary_text_color' => '#ffffff',
                'secondary_hover_color' => '#5a6268',
                'success_color' => '#28a745',
                'success_text_color' => '#ffffff',
                'success_hover_color' => '#218838',
                'danger_color' => '#dc3545',
                'danger_text_color' => '#ffffff',
                'danger_hover_color' => '#c82333',
            ],
            'login' => [
                'background_color' => '#f8f9fa',
                'gradient_color' => '',
                'text_color' => '#333333',
                'accent_color' => '#667eea',
                'card_background' => '#ffffff',
                'input_border_color' => '#dee2e6',
                'input_focus_color' => '#667eea',
                'background_image' => null,
                'login_illustration' => null,
            ],
            'global_logo' => null,
        ];
    }

    private function saveSettings($settings)
    {
        $settingsPath = storage_path('app/settings.json');
        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
    }

    public function removeImage(Request $request)
    {
        $request->validate([
            'type' => 'required|in:homepage,navbar,login,program_cards',
        ]);

        $settings = $this->getCurrentSettings();
        $type = $request->type;
        $success = false;
        $message = '';

        try {
            if ($type === 'navbar') {
                if (isset($settings['navbar']['logo'])) {
                    // Delete the logo file
                    if (Storage::disk('public')->exists($settings['navbar']['logo'])) {
                        Storage::disk('public')->delete($settings['navbar']['logo']);
                    }

                    // Remove from settings
                    unset($settings['navbar']['logo']);
                    $this->saveSettings($settings);
                    $success = true;
                    $message = 'Navbar logo removed successfully!';
                } else {
                    $message = 'No navbar logo to remove.';
                }
            } else {
                // Handle background image removal for all sections
                if (isset($settings[$type]['background_image'])) {
                    // Delete the image file
                    if (Storage::disk('public')->exists($settings[$type]['background_image'])) {
                        Storage::disk('public')->delete($settings[$type]['background_image']);
                    }

                    // Remove from settings
                    unset($settings[$type]['background_image']);
                    $this->saveSettings($settings);
                    $success = true;
                    $sectionName = $type === 'program_cards' ? 'Program cards' : ucfirst($type);
                    $message = $sectionName . ' background image removed successfully!';
                } else {
                    $sectionName = $type === 'program_cards' ? 'Program cards' : ucfirst($type);
                    $message = 'No ' . strtolower($sectionName) . ' background image to remove.';
                }
            }
        } catch (\Exception $e) {
            Log::error('Error removing image: ' . $e->getMessage());
            $message = 'Error removing image. Please try again.';
        }

        if ($success) {
            return back()->with('success', $message);
        } else {
            return back()->with('info', $message);
        }
    }

    public function removeLoginIllustration()
    {
        $settings = $this->getCurrentSettings();

        try {
            if (isset($settings['login']['login_illustration'])) {
                // Delete the illustration file
                if (Storage::disk('public')->exists($settings['login']['login_illustration'])) {
                    Storage::disk('public')->delete($settings['login']['login_illustration']);
                }

                // Remove from settings
                unset($settings['login']['login_illustration']);
                $this->saveSettings($settings);
                
                return back()->with('success', 'Login illustration removed successfully!');
            } else {
                return back()->with('info', 'No login illustration to remove.');
            }
        } catch (\Exception $e) {
            Log::error('Error removing login illustration: ' . $e->getMessage());
            return back()->with('error', 'Error removing login illustration. Please try again.');
        }
    }

    public function getFormRequirements()
    {
        // Return ALL requirements (both active and inactive) for admin management
        $requirements = FormRequirement::ordered()->get();
        return response()->json($requirements);
    }
    
    public function saveFormRequirements(Request $request)
    {
        try {
            $requirements = $request->input('requirements', []);
            
            // Delete existing requirements that are not in the request
            $existingIds = collect($requirements)->pluck('id')->filter();
            FormRequirement::whereNotIn('id', $existingIds)->delete();
            
            foreach ($requirements as $index => $reqData) {
                if ($reqData['field_type'] === 'section') {
                    // For section type, only section_name is required
                    if (empty($reqData['section_name'])) {
                        continue;
                    }
                    
                    $data = [
                        'field_name' => 'section_' . $index,
                        'field_label' => $reqData['section_name'],
                        'field_type' => 'section',
                        'program_type' => $reqData['program_type'],
                        'is_required' => false,
                        'is_active' => true,
                        'sort_order' => $reqData['sort_order'] ?? $index,
                        'section_name' => $reqData['section_name']
                    ];
                } else {
                    // For regular fields, use label to derive field_name if missing or contains spaces
                    if (empty($reqData['field_label'])) {
                        continue;
                    }
                    $fieldName = $reqData['field_name'] ?? '';
                    if (Str::contains($fieldName, ' ') || empty($fieldName)) {
                        $fieldName = Str::snake($reqData['field_label']);
                    }
                    $data = [
                        'field_name' => $fieldName,
                        'field_label' => $reqData['field_label'],
                        'field_type' => $reqData['field_type'],
                        'program_type' => $reqData['program_type'],
                        'is_required' => isset($reqData['is_required']) && $reqData['is_required'] === '1',
                        'is_active' => isset($reqData['is_active']) && $reqData['is_active'] === '1',
                        'sort_order' => $reqData['sort_order'] ?? $index,
                        'section_name' => $reqData['section_name'] ?? null,
                        'is_bold' => isset($reqData['is_bold']) && $reqData['is_bold'] === '1',
                        'field_options' => $this->processFieldOptions($reqData['field_options'] ?? null)
                    ];
                }
                
                if (!empty($reqData['id'])) {
                    // Update existing
                    FormRequirement::where('id', $reqData['id'])->update($data);
                } else {
                    // Create new
                    FormRequirement::create($data);
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error saving form requirements: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function saveStudentPortalSettings(Request $request)
    {
        try {
            $request->validate([
                'primary_color' => 'nullable|string|max:7',
                'background_color' => 'nullable|string|max:7',
                'header_logo' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:2048'
            ]);
            
            // Save color settings
            if ($request->has('primary_color')) {
                UiSetting::set('student_portal', 'primary_color', $request->primary_color, 'color');
            }
            
            if ($request->has('background_color')) {
                UiSetting::set('student_portal', 'background_color', $request->background_color, 'color');
            }
            
            // Handle logo upload
            if ($request->hasFile('header_logo')) {
                $oldLogo = UiSetting::get('student_portal', 'header_logo_path');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                
                $file = $request->file('header_logo');
                $filename = 'student_header_logo_' . time() . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('logos/student', $filename, 'public');
                
                UiSetting::set('student_portal', 'header_logo_path', $logoPath, 'file');
                UiSetting::set('student_portal', 'header_logo_url', Storage::url($logoPath), 'text');
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error saving student portal settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function getStudentPortalSettings()
    {
        $settings = UiSetting::getSection('student_portal');
        return response()->json($settings);
    }
    
    public function saveNavbarSettings(Request $request)
    {
        try {
            $colorSettings = [
                'header_bg', 'header_text', 'header_border', 'search_bg',
                'sidebar_bg', 'sidebar_text', 'active_link_bg', 'active_link_text',
                'hover_bg', 'hover_text', 'submenu_bg', 'submenu_text',
                'footer_bg', 'icon_color'
            ];
            
            foreach ($colorSettings as $setting) {
                if ($request->has($setting)) {
                    UiSetting::set('navbar', $setting, $request->input($setting), 'color');
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error saving navbar settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function getNavbarSettings()
    {
        try {
            $settings = UiSetting::getSection('navbar');
            return response()->json(['success' => true, 'data' => $settings]);
        } catch (\Exception $e) {
            Log::error('Error loading navbar settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to load navbar settings'], 500);
        }
    }

    public function saveFooterSettings(Request $request)
    {
        try {
            $footerSettings = [
                'footer_bg_color', 'footer_text_color', 'footer_text',
                'footer_link_color', 'footer_link_hover_color'
            ];
            
            foreach ($footerSettings as $setting) {
                if ($request->has($setting)) {
                    $type = in_array($setting, ['footer_bg_color', 'footer_text_color', 'footer_link_color', 'footer_link_hover_color']) ? 'color' : 'text';
                    UiSetting::set('footer', $setting, $request->input($setting), $type);
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error saving footer settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function getFooterSettings()
    {
        try {
            $settings = UiSetting::getSection('footer');
            return response()->json(['success' => true, 'data' => $settings]);
        } catch (\Exception $e) {
            Log::error('Error loading footer settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to load footer settings'], 500);
        }
    }

    public function saveReferralSettings(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'referral_enabled' => 'required|in:0,1',
                'referral_required' => 'required|in:0,1'
            ]);

            // Update or create the settings
            DB::table('admin_settings')->updateOrInsert(
                ['setting_key' => 'referral_enabled'],
                ['setting_value' => $request->input('referral_enabled')]
            );

            DB::table('admin_settings')->updateOrInsert(
                ['setting_key' => 'referral_required'],
                ['setting_value' => $request->input('referral_required')]
            );

            return response()->json([
                'success' => true,
                'message' => 'Referral settings updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving referral settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to save referral settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateEnrollmentForm($programType = 'both')
    {
        $requirements = FormRequirement::active()
            ->forProgram($programType)
            ->ordered()
            ->get();
            
        return response()->json($requirements);
    }
    
    public function renderEnrollmentForm($programType)
    {
        $requirements = FormRequirement::active()
            ->forProgram($programType)
            ->ordered()
            ->get();
            
        $html = '';
        
        foreach ($requirements as $requirement) {
            $html .= $this->generateFieldHtml($requirement);
        }
        
        return $html;
    }
    
  private function generateFieldHtml($requirement)
{
    $fieldName = $requirement->field_name;
    $fieldLabel = $requirement->field_label;
    $fieldType = $requirement->field_type;
    $isRequired = $requirement->is_required;
    $requiredAttr = $isRequired ? 'required' : '';
    $requiredMark = $isRequired ? '<span class="text-danger">*</span>' : '';

    switch ($fieldType) {
        case 'section_label':
            return "
                <div class='form-section-label my-4'>
                    <h5 class='text-primary fw-bold'>{$fieldLabel}</h5>
                </div>
            ";

        case 'text':
        case 'email':
        case 'tel':
            return "
                <div class='form-group mb-3'>
                    <label for='{$fieldName}' class='form-label'>{$fieldLabel} {$requiredMark}</label>
                    <input type='{$fieldType}' class='form-control' id='{$fieldName}' name='{$fieldName}' {$requiredAttr}>
                </div>
            ";

        case 'date':
            return "
                <div class='form-group mb-3'>
                    <label for='{$fieldName}' class='form-label'>{$fieldLabel} {$requiredMark}</label>
                    <input type='date' class='form-control' id='{$fieldName}' name='{$fieldName}' {$requiredAttr}>
                </div>
            ";

        case 'file':
            return "
                <div class='form-group mb-3'>
                    <label for='{$fieldName}' class='form-label'>{$fieldLabel} {$requiredMark}</label>
                    <div class='file-upload-container'>
                        <input type='file' class='form-control' id='{$fieldName}' name='{$fieldName}' {$requiredAttr}>
                        <button type='button' class='btn btn-outline-primary btn-sm upload-btn' data-field='{$fieldName}'>
                            <i class='bi bi-upload'></i> Choose File
                        </button>
                    </div>
                </div>
            ";

        case 'textarea':
            return "
                <div class='form-group mb-3'>
                    <label for='{$fieldName}' class='form-label'>{$fieldLabel} {$requiredMark}</label>
                    <textarea class='form-control' id='{$fieldName}' name='{$fieldName}' rows='3' {$requiredAttr}></textarea>
                </div>
            ";

        case 'select':
            $options = $requirement->field_options ?: [];
            $optionsHtml = '<option value="">Select an option</option>';
            foreach ($options as $option) {
                $optionsHtml .= "<option value='{$option}'>{$option}</option>";
            }

            return "
                <div class='form-group mb-3'>
                    <label for='{$fieldName}' class='form-label'>{$fieldLabel} {$requiredMark}</label>
                    <select class='form-select' id='{$fieldName}' name='{$fieldName}' {$requiredAttr}>
                        {$optionsHtml}
                    </select>
                </div>
            ";

        default:
            return '';
    }
}

    /**
     * Toggle field active status
     */
    public function toggleFieldActive(Request $request)
    {
        try {
            $fieldId = $request->input('field_id');
            $isActive = $request->input('is_active');

            $requirement = FormRequirement::findOrFail($fieldId);
            $requirement->is_active = $isActive;
            $requirement->save();

            return response()->json([
                'success' => true,
                'message' => 'Field status updated successfully',
                'field_name' => $requirement->field_name,
                'is_active' => $requirement->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling field active status: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }



    /**
     * Preview form for a specific program type
     */
    public function previewForm($programType)
    {
        try {
            $requirements = FormRequirement::active()
                ->forProgram($programType)
                ->ordered()
                ->get();

            return view('admin.settings.form-preview', compact('requirements', 'programType'));
        } catch (\Exception $e) {
            Log::error('Error previewing form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading form preview');
        }
    }

    /**
     * Process field options from textarea input
     */
    private function processFieldOptions($optionsString)
    {
        if (empty($optionsString)) {
            return null;
        }
        
        // Split by newlines and trim each option
        $options = array_map('trim', explode("\n", $optionsString));
        
        // Remove empty options
        $options = array_filter($options, function($option) {
            return !empty($option);
        });
        
        // Return as array for JSON storage
        return array_values($options);
    }

    public function getHomepageSettings()
    {
        $settings = UiSetting::getSection('homepage');
        return response()->json($settings);
    }
    
    public function saveHomepageSettings(Request $request)
    {
        try {
            $request->validate([
                'hero_bg_color' => 'nullable|string|max:7',
                'hero_text_color' => 'nullable|string|max:7',
                'hero_title' => 'nullable|string|max:500',
                'hero_subtitle' => 'nullable|string|max:1000',
                'hero_button_text' => 'nullable|string|max:100',
                'hero_button_color' => 'nullable|string|max:7',
                'programs_bg_color' => 'nullable|string|max:7',
                'programs_text_color' => 'nullable|string|max:7',
                'programs_title' => 'nullable|string|max:200',
                'programs_subtitle' => 'nullable|string|max:500',
                'modalities_bg_color' => 'nullable|string|max:7',
                'modalities_text_color' => 'nullable|string|max:7',
                'modalities_title' => 'nullable|string|max:200',
                'modalities_subtitle' => 'nullable|string|max:500',
                'about_bg_color' => 'nullable|string|max:7',
                'about_text_color' => 'nullable|string|max:7',
                'about_title' => 'nullable|string|max:200',
                'about_subtitle' => 'nullable|string|max:500',
            ]);
            
            // Save all homepage settings
            $settings = [
                'hero_bg_color', 'hero_text_color', 'hero_title', 'hero_subtitle', 
                'hero_button_text', 'hero_button_color',
                'programs_bg_color', 'programs_text_color', 'programs_title', 'programs_subtitle',
                'modalities_bg_color', 'modalities_text_color', 'modalities_title', 'modalities_subtitle',
                'about_bg_color', 'about_text_color', 'about_title', 'about_subtitle'
            ];
            
            foreach ($settings as $setting) {
                if ($request->has($setting)) {
                    $type = str_contains($setting, 'color') ? 'color' : 'text';
                    UiSetting::set('homepage', $setting, $request->input($setting), $type);
                }
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error saving homepage settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function getAllSettings()
    {
        $settings = [
            'navbar' => UiSetting::getSection('navbar'),
            'homepage' => UiSetting::getSection('homepage'),
            'student_portal' => UiSetting::getSection('student_portal'),
        ];
        
        return response()->json($settings);
    }
    
    public function saveAllSettings(Request $request)
    {
        try {
            $sections = $request->input('sections', []);
            
            foreach ($sections as $section => $settings) {
                foreach ($settings as $key => $value) {
                    $type = str_contains($key, 'color') ? 'color' : 'text';
                    UiSetting::set($section, $key, $value, $type);
                }
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error saving all settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateProfessorFeatures(Request $request)
    {
        $request->validate([
            'ai_quiz_enabled' => 'nullable',
            'grading_enabled' => 'nullable', 
            'upload_videos_enabled' => 'nullable',
            'attendance_enabled' => 'nullable',
            'view_programs_enabled' => 'nullable',
            'meeting_creation_enabled' => 'nullable',
            'professor_module_management_enabled' => 'nullable',
            'professor_announcement_management_enabled' => 'nullable',
        ]);

        try {
            // Convert to boolean values
            $aiQuizEnabled = filter_var($request->input('ai_quiz_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $gradingEnabled = filter_var($request->input('grading_enabled', true), FILTER_VALIDATE_BOOLEAN);
            $uploadVideosEnabled = filter_var($request->input('upload_videos_enabled', true), FILTER_VALIDATE_BOOLEAN);
            $attendanceEnabled = filter_var($request->input('attendance_enabled', true), FILTER_VALIDATE_BOOLEAN);
            $viewProgramsEnabled = filter_var($request->input('view_programs_enabled', true), FILTER_VALIDATE_BOOLEAN);
        // Determine meeting creation toggle (default false if unchecked)
        $meetingCreationEnabled = filter_var($request->input('meeting_creation_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $moduleManagementEnabled = filter_var($request->input('professor_module_management_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $announcementManagementEnabled = filter_var($request->input('professor_announcement_management_enabled', false), FILTER_VALIDATE_BOOLEAN);


            // Save AI Quiz Enabled setting
            AdminSetting::updateOrCreate(
                ['setting_key' => 'ai_quiz_enabled'],
                [
                    'setting_value' => $aiQuizEnabled ? 'true' : 'false',
                    'is_active' => 1
                ]
            );

            // Save Meeting Creation Enabled setting
            AdminSetting::updateOrCreate(
                ['setting_key' => 'meeting_creation_enabled'],
                [
                    'setting_value' => $meetingCreationEnabled ? '1' : '0',
                    'is_active' => 1
                ]
            );

            // Save Module Management Enabled setting
            AdminSetting::updateOrCreate(
                ['setting_key' => 'professor_module_management_enabled'],
                [
                    'setting_value' => $moduleManagementEnabled ? '1' : '0',
                    'is_active' => 1
                ]
            );

            // Save Announcement Management Enabled setting
            AdminSetting::updateOrCreate(
                ['setting_key' => 'professor_announcement_management_enabled'],
                [
                    'setting_value' => $announcementManagementEnabled ? '1' : '0',
                    'is_active' => 1
                ]
            );

            // The rest can use the same keys as before (or add if missing)
            AdminSetting::updateOrCreate(
                ['setting_key' => 'grading_enabled'],
                [
                    'setting_value' => $gradingEnabled ? 'true' : 'false',
                    'is_active' => 1
                ]
            );

            AdminSetting::updateOrCreate(
                ['setting_key' => 'upload_videos_enabled'],
                [
                    'setting_value' => $uploadVideosEnabled ? 'true' : 'false',
                    'is_active' => 1
                ]
            );

            AdminSetting::updateOrCreate(
                ['setting_key' => 'attendance_enabled'],
                [
                    'setting_value' => $attendanceEnabled ? 'true' : 'false',
                    'is_active' => 1
                ]
            );

            AdminSetting::updateOrCreate(
                ['setting_key' => 'view_programs_enabled'],
                [
                    'setting_value' => $viewProgramsEnabled ? 'true' : 'false',
                    'is_active' => 1
                ]
            );

            // No further Meeting Creation updates needed

            return response()->json(['success' => true, 'message' => 'Professor feature settings updated successfully!']);

        } catch (\Exception $e) {
            Log::error('Error updating professor feature settings: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to update settings. ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function getProfessorFeatures()
    {
        try {
            $features = [
                'ai_quiz_enabled' => AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') === 'true',
                'grading_enabled' => AdminSetting::where('setting_key', 'grading_enabled')->value('setting_value') !== 'false',
                'upload_videos_enabled' => AdminSetting::where('setting_key', 'upload_videos_enabled')->value('setting_value') !== 'false',
                'attendance_enabled' => AdminSetting::where('setting_key', 'attendance_enabled')->value('setting_value') !== 'false',
                'view_programs_enabled' => AdminSetting::where('setting_key', 'view_programs_enabled')->value('setting_value') !== 'false',
                'meeting_creation_enabled' => AdminSetting::where('setting_key', 'meeting_creation_enabled')->value('setting_value') === '1',
                'professor_module_management_enabled' => AdminSetting::where('setting_key', 'professor_module_management_enabled')->value('setting_value') === '1',
                'professor_announcement_management_enabled' => AdminSetting::where('setting_key', 'professor_announcement_management_enabled')->value('setting_value') === '1',
            ];

            return response()->json([
                'success' => true,
                'settings' => $features
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting professor features: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load settings'], 500);
        }
    }

    public function updateDirectorFeatures(Request $request)
    {
        $settings = [
            'view_students',
            'manage_programs',
            'manage_modules',
            'manage_enrollments',
            'view_analytics',
            'manage_professors',
            'manage_batches',
            'manage_announcements'
        ];

        try {
            foreach ($settings as $key) {
                $value = $request->input($key, 'true');
                $boolValue = ($value === true || $value === 'true' || $value === 1 || $value === '1');
                \App\Models\AdminSetting::updateOrCreate(
                    ['setting_key' => "director_{$key}"],
                    ['setting_value' => $boolValue ? 'true' : 'false']
                );
            }
            return response()->json(['success' => true, 'message' => 'Director feature settings updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Error updating director feature settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to update settings. Please try again.'], 500);
        }
    }

    public function getDirectorFeatures()
    {
        try {
            $features = [
                'view_students' => AdminSetting::where('setting_key', 'director_view_students')->value('setting_value') !== 'false',
                'manage_programs' => AdminSetting::where('setting_key', 'director_manage_programs')->value('setting_value') !== 'false',
                'manage_modules' => AdminSetting::where('setting_key', 'director_manage_modules')->value('setting_value') !== 'false',
                'manage_enrollments' => AdminSetting::where('setting_key', 'director_manage_enrollments')->value('setting_value') !== 'false',
                'view_analytics' => AdminSetting::where('setting_key', 'director_view_analytics')->value('setting_value') !== 'false',
                'manage_professors' => AdminSetting::where('setting_key', 'director_manage_professors')->value('setting_value') !== 'false',
                'manage_batches' => AdminSetting::where('setting_key', 'director_manage_batches')->value('setting_value') !== 'false',
                'manage_announcements' => AdminSetting::where('setting_key', 'director_manage_announcements')->value('setting_value') !== 'false',
            ];
            return response()->json(['success' => true, 'settings' => $features]);
        } catch (\Exception $e) {
            Log::error('Error getting director features: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load settings'], 500);
        }
    }

    /**
     * Show plan settings page for learning mode configuration
     */
    public function planSettings()
    {
        $plans = \App\Models\Plan::all();
        return view('admin.admin-settings.plan-settings', compact('plans'));
    }

    /**
     * Update learning mode settings for plans
     */
    public function updateLearningModes(Request $request)
    {
        $request->validate([
            'plans' => 'required|array',
            'plans.*.plan_id' => 'required|exists:plan,plan_id',
            'plans.*.enable_synchronous' => 'boolean',
            'plans.*.enable_asynchronous' => 'boolean',
        ]);

        try {
            foreach ($request->input('plans') as $planData) {
                $plan = \App\Models\Plan::find($planData['plan_id']);
                if ($plan) {
                    $plan->enable_synchronous = $planData['enable_synchronous'] ?? false;
                    $plan->enable_asynchronous = $planData['enable_asynchronous'] ?? false;
                    $plan->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'Learning mode settings updated successfully']);

        } catch (\Exception $e) {
            Log::error('Error updating learning mode settings: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update settings'], 500);
        }
    }

    public function getPlanSettings()
    {
        try {
            $plans = \App\Models\Plan::select('plan_id', 'plan_name', 'description', 'enable_synchronous', 'enable_asynchronous')
                ->get();
            
            return response()->json([
                'success' => true,
                'plans' => $plans
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching plan settings: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch plan settings'], 500);
        }
    }

    public function savePlanSettings(Request $request)
    {
        try {
            $plans = $request->input('plans', []);
            
            foreach ($plans as $planData) {
                \App\Models\Plan::where('plan_id', $planData['plan_id'])
                    ->update([
                        'enable_synchronous' => $planData['enable_synchronous'],
                        'enable_asynchronous' => $planData['enable_asynchronous']
                    ]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error saving plan settings: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save plan settings'], 500);
        }
    }

    // Payment Methods functionality
    public function getPaymentMethods()
    {
        try {
            $paymentMethods = PaymentMethod::orderBy('sort_order')->get();
            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching payment methods: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch payment methods'], 500);
        }
    }

    public function storePaymentMethod(Request $request)
    {
        try {
            // Log incoming request data for debugging
            Log::info('Payment method store request', [
                'method_name' => $request->method_name,
                'method_type' => $request->method_type,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'is_enabled' => $request->is_enabled,
                'has_file' => $request->hasFile('qr_code'),
                'all_data' => $request->all()
            ]);

            $request->validate([
                'method_name' => 'required|string|max:255',
                'method_type' => 'required|in:credit_card,gcash,maya,bank_transfer,cash,other',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'qr_code' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'is_enabled' => 'nullable|in:0,1,true,false,on,off'
            ]);

            $qrCodePath = null;
            if ($request->hasFile('qr_code')) {
                $file = $request->file('qr_code');
                Log::info('Payment method QR code upload', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
                
                $qrCodePath = $file->store('payment_qr_codes', 'public');
                
                if (!$qrCodePath) {
                    throw new \Exception('Failed to store QR code file');
                }
                
                Log::info('QR code stored successfully', ['path' => $qrCodePath]);
            }

            $paymentMethod = PaymentMethod::create([
                'method_name' => $request->method_name,
                'method_type' => $request->method_type,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'qr_code_path' => $qrCodePath,
                'is_enabled' => $request->boolean('is_enabled', true),
                'sort_order' => PaymentMethod::max('sort_order') + 1,
                'created_by_admin_id' => session('admin_id') ?? session('user_id') ?? 1
            ]);

            Log::info('Payment method created successfully', ['payment_method_id' => $paymentMethod->payment_method_id]);

            return response()->json([
                'success' => true,
                'data' => $paymentMethod,
                'message' => 'Payment method created successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Payment method validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'file_data' => $request->hasFile('qr_code') ? [
                    'name' => $request->file('qr_code')->getClientOriginalName(),
                    'size' => $request->file('qr_code')->getSize(),
                    'mime' => $request->file('qr_code')->getMimeType(),
                    'valid' => $request->file('qr_code')->isValid()
                ] : 'no_file'
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $e->errors(),
                'message' => 'Please check the form fields and try again.'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating payment method', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to create payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        try {
            $request->validate([
                'method_name' => 'required|string|max:255',
                'method_type' => 'required|in:credit_card,gcash,maya,bank_transfer,cash,other',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'qr_code' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'is_enabled' => 'boolean',
                'remove_qr_code' => 'nullable|boolean'
            ]);

            $paymentMethod = PaymentMethod::findOrFail($id);
            
            // Handle QR code removal
            if ($request->boolean('remove_qr_code') && $paymentMethod->qr_code_path) {
                if (Storage::disk('public')->exists($paymentMethod->qr_code_path)) {
                    Storage::disk('public')->delete($paymentMethod->qr_code_path);
                    Log::info('Old QR code deleted', ['path' => $paymentMethod->qr_code_path]);
                }
                $paymentMethod->qr_code_path = null;
            }

            // Handle new QR code upload
            if ($request->hasFile('qr_code')) {
                $file = $request->file('qr_code');
                Log::info('Payment method QR code update', [
                    'payment_method_id' => $id,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
                
                // Delete old QR code if exists
                if ($paymentMethod->qr_code_path && Storage::disk('public')->exists($paymentMethod->qr_code_path)) {
                    Storage::disk('public')->delete($paymentMethod->qr_code_path);
                    Log::info('Old QR code replaced', ['old_path' => $paymentMethod->qr_code_path]);
                }
                
                $qrCodePath = $file->store('payment_qr_codes', 'public');
                
                if (!$qrCodePath) {
                    throw new \Exception('Failed to store QR code file');
                }
                
                $paymentMethod->qr_code_path = $qrCodePath;
                Log::info('New QR code stored', ['path' => $qrCodePath]);
            }

            $paymentMethod->update([
                'method_name' => $request->method_name,
                'method_type' => $request->method_type,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'is_enabled' => $request->boolean('is_enabled', true)
            ]);

            Log::info('Payment method updated successfully', ['payment_method_id' => $id]);

            return response()->json([
                'success' => true,
                'data' => $paymentMethod->fresh(),
                'message' => 'Payment method updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Payment method update validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating payment method', [
                'payment_method_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to update payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePaymentMethod($id)
    {
        try {
            $paymentMethod = PaymentMethod::findOrFail($id);
            
            // Delete QR code file if exists
            if ($paymentMethod->qr_code_path) {
                Storage::disk('public')->delete($paymentMethod->qr_code_path);
            }

            $paymentMethod->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment method deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting payment method: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete payment method'], 500);
        }
    }

    public function updatePaymentMethodOrder(Request $request)
    {
        $request->validate([
            'payment_methods' => 'required|array',
            'payment_methods.*.id' => 'required|integer|exists:payment_methods,payment_method_id',
            'payment_methods.*.sort_order' => 'required|integer'
        ]);

        try {
            foreach ($request->payment_methods as $method) {
                PaymentMethod::where('payment_method_id', $method['id'])
                    ->update(['sort_order' => $method['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment method order updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating payment method order: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update payment method order'], 500);
        }
    }

    // Method to get enabled payment methods for students
    public function getEnabledPaymentMethods()
    {
        try {
            $paymentMethods = PaymentMethod::enabled()->ordered()->get();
            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching enabled payment methods: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch payment methods'], 500);
        }
    }

    // Dynamic Field Synchronization Methods
    public function syncDynamicFields()
    {
        try {
            $dynamicFieldService = new DynamicFieldService();
            $result = $dynamicFieldService->synchronizeDynamicFields();
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dynamic fields synchronized successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to synchronize dynamic fields'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error synchronizing dynamic fields: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to synchronize dynamic fields'], 500);
        }
    }

    public function addDynamicColumn(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string|regex:/^[a-zA-Z][a-zA-Z0-9_]*$/|max:64',
            'field_type' => 'required|in:text,textarea,number,date,file,select,checkbox,radio,multiple_selection',
            'field_label' => 'nullable|string|max:255'
        ]);

        try {
            $dynamicFieldService = new DynamicFieldService();
            $result = $dynamicFieldService->addDynamicField(
                $request->field_name,
                $request->field_type,
                $request->field_label
            );
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dynamic field added successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add dynamic field'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error adding dynamic field: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add dynamic field'], 500);
        }
    }

    public function getMissingColumns()
    {
        try {
            $dynamicFieldService = new DynamicFieldService();
            $missingColumns = $dynamicFieldService->getMissingColumnsInStudents();
            
            return response()->json([
                'success' => true,
                'data' => $missingColumns
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting missing columns: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get missing columns'], 500);
        }
    }

    public function updateSidebar(Request $request)
    {
        $request->validate([
            'sidebar_background_color' => 'nullable|string|max:7',
            'sidebar_gradient_color' => 'nullable|string|max:7',
            'sidebar_text_color' => 'nullable|string|max:7',
            'sidebar_hover_color' => 'nullable|string|max:7',
            'sidebar_active_bg_color' => 'nullable|string|max:7',
            'sidebar_active_text_color' => 'nullable|string|max:7',
            'sidebar_footer_bg_color' => 'nullable|string|max:7',
            'sidebar_footer_text_color' => 'nullable|string|max:7',
        ]);

        $settings = $this->getCurrentSettings();
        
        // Get the background color for unified styling
        $sidebarBg = $request->sidebar_background_color ?? $settings['sidebar']['background_color'] ?? '#2d1b69';
        $gradientColor = $request->sidebar_gradient_color ?? $settings['sidebar']['gradient_color'] ?? '#1a1340';
        $footerBg = $request->sidebar_footer_bg_color ?? $sidebarBg; // Use sidebar bg as default for footer

        // Update sidebar settings with unified colors for nav and footer
        $settings['sidebar'] = array_merge($settings['sidebar'] ?? [], [
            'background_color' => $sidebarBg,
            'gradient_color' => $gradientColor,
            'text_color' => $request->sidebar_text_color ?? $settings['sidebar']['text_color'] ?? '#ffffff',
            'hover_color' => $request->sidebar_hover_color ?? $settings['sidebar']['hover_color'] ?? '#a91d3a',
            'active_bg_color' => $request->sidebar_active_bg_color ?? $settings['sidebar']['active_bg_color'] ?? '#a91d3a',
            'active_text_color' => $request->sidebar_active_text_color ?? $settings['sidebar']['active_text_color'] ?? '#ffffff',
            'footer_bg_color' => $footerBg,
            'footer_text_color' => $request->sidebar_footer_text_color ?? $settings['sidebar']['footer_text_color'] ?? '#ffffff',
        ]);

        $this->saveSettings($settings);

        return back()->with('success', 'Sidebar navigation and footer settings updated successfully! The changes are now applied to both sections.');
    }

    /**
     * Update meeting whitelist settings
     */
    public function updateMeetingWhitelist(Request $request)
    {
        try {
            $whitelistedProfessors = $request->input('whitelist_professors', []);
            $whitelistString = implode(',', $whitelistedProfessors);
            
            // Save to database using AdminSetting model
            AdminSetting::updateOrCreate(
                ['setting_key' => 'meeting_whitelist_professors'],
                ['setting_value' => $whitelistString]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Meeting whitelist updated successfully',
                'count' => count($whitelistedProfessors)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating meeting whitelist: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update meeting whitelist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update module management whitelist settings
     */
    public function updateModuleManagementWhitelist(Request $request)
    {
        try {
            $whitelistedProfessors = $request->input('whitelist_module_professors', []);
            $whitelistString = implode(',', $whitelistedProfessors);
            
            // Save to database using AdminSetting model
            AdminSetting::updateOrCreate(
                ['setting_key' => 'professor_module_management_whitelist'],
                ['setting_value' => $whitelistString]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Module management whitelist updated successfully',
                'count' => count($whitelistedProfessors)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating module management whitelist: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update module management whitelist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update announcement management whitelist settings
     */
    public function updateAnnouncementManagementWhitelist(Request $request)
    {
        try {
            $whitelistedProfessors = $request->input('whitelist_announcement_professors', []);
            $whitelistString = implode(',', $whitelistedProfessors);
            
            // Save to database using AdminSetting model
            AdminSetting::updateOrCreate(
                ['setting_key' => 'professor_announcement_management_whitelist'],
                ['setting_value' => $whitelistString]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Announcement management whitelist updated successfully',
                'count' => count($whitelistedProfessors)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating announcement management whitelist: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update announcement management whitelist: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePaymentTerms(Request $request)
    {
        try {
            $request->validate([
                'payment_terms' => 'nullable|string',
                'abort_terms' => 'nullable|string'
            ]);

            // Get current settings
            $settings = $this->getCurrentSettings();
            
            // Update payment terms settings
            $settings['payment_terms'] = [
                'payment_terms' => $request->input('payment_terms', ''),
                'abort_terms' => $request->input('abort_terms', ''),
                'updated_at' => now()->toISOString()
            ];

            // Save the updated settings
            $this->saveSettings($settings);

            return response()->json([
                'success' => true,
                'message' => 'Payment terms updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating payment terms: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update payment terms: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTermsConditions(Request $request)
    {
        try {
            $request->validate([
                'full_enrollment_terms' => 'nullable|string',
                'modular_enrollment_terms' => 'nullable|string',
                'require_terms_acceptance' => 'nullable'
            ]);

            // Save full enrollment terms
            AdminSetting::updateOrCreate(
                ['setting_key' => 'full_enrollment_terms'],
                ['setting_value' => $request->input('full_enrollment_terms', '')]
            );

            // Save modular enrollment terms
            AdminSetting::updateOrCreate(
                ['setting_key' => 'modular_enrollment_terms'],
                ['setting_value' => $request->input('modular_enrollment_terms', '')]
            );

            // Save require terms acceptance setting
            AdminSetting::updateOrCreate(
                ['setting_key' => 'require_terms_acceptance'],
                ['setting_value' => $request->has('require_terms_acceptance') ? '1' : '0']
            );

            return response()->json([
                'success' => true,
                'message' => 'Terms and conditions updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating terms and conditions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update terms and conditions: ' . $e->getMessage()
            ], 500);
        }
    }
}
