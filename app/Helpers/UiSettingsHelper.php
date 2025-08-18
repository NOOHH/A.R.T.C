<?php

namespace App\Helpers;

use App\Models\UiSetting;

class UiSettingsHelper
{
    /**
     * Get a UI setting value
     */
    public static function get($section, $key, $default = null)
    {
        return UiSetting::get($section, $key, $default);
    }

    /**
     * Get all settings for a section
     */
    public static function getSection($section)
    {
        return UiSetting::getSection($section);
    }

    /**
     * Get all UI settings as an array
     */
    public static function getAll()
    {
        return [
            'general' => self::getSection('general')->toArray(),
            'navbar' => self::getSection('navbar')->toArray(),
            'branding' => self::getSection('branding')->toArray(),
            'homepage' => self::getSection('homepage')->toArray(),
        ];
    }

    /**
     * Get CSS variables for branding
     */
    public static function getCssVariables()
    {
        $branding = self::getSection('branding');
        
        return [
            '--primary-color' => $branding['primary_color'] ?? '#667eea',
            '--secondary-color' => $branding['secondary_color'] ?? '#764ba2',
            '--background-color' => $branding['background_color'] ?? '#ffffff',
            '--font-family' => $branding['font_family'] ?? 'Inter',
        ];
    }

    /**
     * Get navbar settings
     */
    public static function getNavbarSettings()
    {
        return self::getSection('navbar');
    }

    /**
     * Get homepage settings
     */
    public static function getHomepageSettings()
    {
        return self::getSection('homepage');
    }

    /**
     * Get general settings
     */
    public static function getGeneralSettings()
    {
        return self::getSection('general');
    }
}
