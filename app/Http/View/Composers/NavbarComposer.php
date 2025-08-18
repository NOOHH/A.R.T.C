<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\UiSetting;
use App\Helpers\SettingsHelper;

class NavbarComposer
{
    /**
     * Bind navbar data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Get navbar settings from database first, then fallback to SettingsHelper
        try {
            $navbarSettings = UiSetting::getSection('navbar');
            
            // Convert to array if it's a collection
            if ($navbarSettings && method_exists($navbarSettings, 'toArray')) {
                $navbar = $navbarSettings->toArray();
            } else {
                $navbar = $navbarSettings ?: [];
            }
            
            // Ensure brand_name is always available
            if (empty($navbar['brand_name'])) {
                $fallbackSettings = SettingsHelper::getSettings();
                $navbar['brand_name'] = $fallbackSettings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center';
            }
            
        } catch (\Exception $e) {
            // Fallback to SettingsHelper if database fails
            $fallbackSettings = SettingsHelper::getSettings();
            $navbar = $fallbackSettings['navbar'] ?? [
                'brand_name' => 'Ascendo Review and Training Center',
                'background_color' => '#f1f1f1',
                'text_color' => '#222222'
            ];
        }
        
        // Always ensure navbar data is available
        $view->with('navbar', $navbar);
        
        // Also provide settings in the format expected by login pages
        $settings = [
            'navbar' => $navbar
        ];
        $view->with('settings', $settings);
        
        // Also provide uiSettings in the format expected by SmartPrep views
        $uiSettings = [
            'navbar' => $navbar
        ];
        $view->with('uiSettings', $uiSettings);
    }
}
