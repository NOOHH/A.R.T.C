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
        $general = self::getSection('general')->toArray();
        // Force preview URL to ARTC preview route to avoid SmartPrep root redirect
        $general['preview_url'] = url('/artc');

        return [
            'general' => $general,
            'navbar' => self::getSection('navbar')->toArray(),
            'branding' => self::getSection('branding')->toArray(),
            'homepage' => self::getSection('homepage')->toArray(),
            'student_portal' => self::getSection('student_portal')->toArray(),
            'student_sidebar' => self::getSection('student_sidebar')->toArray(),
            'professor_sidebar' => self::getSection('professor_sidebar')->toArray(),
            'admin_sidebar' => self::getSection('admin_sidebar')->toArray(),
            'professor_panel' => self::getSection('professor_panel')->toArray(),
            'admin_panel' => self::getSection('admin_panel')->toArray(),
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
