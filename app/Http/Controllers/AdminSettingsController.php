<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Get current settings from config/storage or database
        $settings = $this->getCurrentSettings();
        
        return view('admin.admin-settings.admin-settings', compact('settings'));
    }

    public function newIndex()
    {
        // Get current settings from config/storage or database
        $settings = $this->getCurrentSettings();
        
        return view('admin.admin-settings.admin-settings-new', compact('settings'));
    }

    public function fixedIndex()
    {
        // Get current settings from config/storage or database
        $settings = $this->getCurrentSettings();
        
        return view('admin.admin-settings.admin-settings-fixed', compact('settings'));
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
        $request->validate([
            'global_logo' => 'required|image|mimes:jpeg,jpg,png,gif,webp,svg|max:5120', // 5MB max, specific image types
        ]);

        $settings = $this->getCurrentSettings();

        // Delete old global logo if exists
        if (isset($settings['global_logo']) && Storage::disk('public')->exists($settings['global_logo'])) {
            Storage::disk('public')->delete($settings['global_logo']);
        }

        // Store new logo
        $logoPath = $request->file('global_logo')->store('settings/global', 'public');
        $settings['global_logo'] = $logoPath;

        $this->saveSettings($settings);

        return back()->with('success', 'Global logo updated successfully!');
    }

    public function removeGlobalLogo()
    {
        $settings = $this->getCurrentSettings();

        try {
            if (isset($settings['global_logo'])) {
                // Delete the logo file
                if (Storage::disk('public')->exists($settings['global_logo'])) {
                    Storage::disk('public')->delete($settings['global_logo']);
                }

                // Remove from settings
                unset($settings['global_logo']);
                $this->saveSettings($settings);
                
                return back()->with('success', 'Global logo removed successfully!');
            } else {
                return back()->with('info', 'No global logo to remove.');
            }
        } catch (\Exception $e) {
            Log::error('Error removing global logo: ' . $e->getMessage());
            return back()->with('error', 'Error removing global logo. Please try again.');
        }
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
}
