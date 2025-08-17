<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Get current settings from ui_settings table as key-value pairs
        $settingsData = DB::table('ui_settings')->get();
        $settings = [];
        
        foreach ($settingsData as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }
        
        return view('smartprep.admin.admin-settings.index', compact('settings'));
    }
    
    public function save(Request $request)
    {
        // Define the allowed settings and their types
        $allowedSettings = [
            'site_name' => 'text',
            'site_logo' => 'file',
            'primary_color' => 'color',
            'secondary_color' => 'color',
            'footer_text' => 'text',
            'contact_email' => 'text',
            'contact_phone' => 'text',
            'social_facebook' => 'text',
            'social_twitter' => 'text',
            'social_linkedin' => 'text',
            'analytics_code' => 'text',
            'meta_description' => 'text',
            'meta_keywords' => 'text',
        ];

        // Validate the request
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'footer_text' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'social_facebook' => 'nullable|string|max:255',
            'social_twitter' => 'nullable|string|max:255',
            'social_linkedin' => 'nullable|string|max:255',
            'analytics_code' => 'nullable|string',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        // Save each setting individually
        foreach ($validated as $key => $value) {
            if ($value !== null && isset($allowedSettings[$key])) {
                // Check if setting already exists
                $existingSetting = DB::table('ui_settings')
                    ->where('setting_key', $key)
                    ->first();
                
                if ($existingSetting) {
                    // Update existing setting
                    DB::table('ui_settings')
                        ->where('id', $existingSetting->id)
                        ->update([
                            'setting_value' => $value,
                            'updated_at' => now()
                        ]);
                } else {
                    // Create new setting
                    DB::table('ui_settings')->insert([
                        'section' => 'admin',
                        'setting_key' => $key,
                        'setting_value' => $value,
                        'setting_type' => $allowedSettings[$key],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        return redirect()->route('smartprep.admin.settings')
            ->with('success', 'Settings saved successfully to the database!');
    }
}
