<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UiSetting;

class UiSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            // Global settings
            [
                'section' => 'global',
                'setting_key' => 'logo_url',
                'setting_value' => asset('images/ARTC_logo.png'),
                'setting_type' => 'file'
            ],
            [
                'section' => 'global',
                'setting_key' => 'favicon_url',
                'setting_value' => asset('favicon.ico'),
                'setting_type' => 'file'
            ],
            [
                'section' => 'global',
                'setting_key' => 'site_title',
                'setting_value' => 'A.R.T.C',
                'setting_type' => 'text'
            ],
            
            // Default navbar colors
            [
                'section' => 'navbar',
                'setting_key' => 'header_bg',
                'setting_value' => '#ffffff',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'header_text',
                'setting_value' => '#333333',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'header_border',
                'setting_value' => '#e0e0e0',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'search_bg',
                'setting_value' => '#f8f9fa',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'sidebar_bg',
                'setting_value' => '#343a40',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'sidebar_text',
                'setting_value' => '#ffffff',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'active_link_bg',
                'setting_value' => '#007bff',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'active_link_text',
                'setting_value' => '#ffffff',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'hover_bg',
                'setting_value' => '#495057',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'hover_text',
                'setting_value' => '#ffffff',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'submenu_bg',
                'setting_value' => '#2c3034',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'submenu_text',
                'setting_value' => '#adb5bd',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'footer_bg',
                'setting_value' => '#212529',
                'setting_type' => 'color'
            ],
            [
                'section' => 'navbar',
                'setting_key' => 'icon_color',
                'setting_value' => '#6c757d',
                'setting_type' => 'color'
            ]
        ];
        
        foreach ($settings as $setting) {
            UiSetting::updateOrCreate(
                ['section' => $setting['section'], 'setting_key' => $setting['setting_key']],
                [
                    'setting_value' => $setting['setting_value'],
                    'setting_type' => $setting['setting_type']
                ]
            );
        }
    }
}
