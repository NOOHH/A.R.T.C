<?php

namespace App\Helpers;

use App\Models\UiSetting;

class UIHelper
{
    public static function getNavbarStyles()
    {
        $settingsCollection = UiSetting::getSection('navbar');
        $settings = $settingsCollection ? $settingsCollection->toArray() : [];
        
        $defaults = [
            'header_bg' => '#ffffff',
            'header_text' => '#333333',
            'header_border' => '#e0e0e0',
            'search_bg' => '#f8f9fa',
            'sidebar_bg' => '#343a40',
            'sidebar_text' => '#ffffff',
            'active_link_bg' => '#007bff',
            'active_link_text' => '#ffffff',
            'hover_bg' => '#495057',
            'hover_text' => '#ffffff',
            'submenu_bg' => '#2c3034',
            'submenu_text' => '#adb5bd',
            'footer_bg' => '#212529',
            'icon_color' => '#6c757d'
        ];
        
        // Merge with defaults
        $colors = array_merge($defaults, $settings);
        
        return "
        <style>
        :root {
            --navbar-header-bg: {$colors['header_bg']};
            --navbar-header-text: {$colors['header_text']};
            --navbar-header-border: {$colors['header_border']};
            --navbar-search-bg: {$colors['search_bg']};
            --navbar-sidebar-bg: {$colors['sidebar_bg']};
            --navbar-sidebar-text: {$colors['sidebar_text']};
            --navbar-active-link-bg: {$colors['active_link_bg']};
            --navbar-active-link-text: {$colors['active_link_text']};
            --navbar-hover-bg: {$colors['hover_bg']};
            --navbar-hover-text: {$colors['hover_text']};
            --navbar-submenu-bg: {$colors['submenu_bg']};
            --navbar-submenu-text: {$colors['submenu_text']};
            --navbar-footer-bg: {$colors['footer_bg']};
            --navbar-icon-color: {$colors['icon_color']};
        }
        
        /* Header Styles */
        .main-header,
        .navbar,
        header {
            background-color: var(--navbar-header-bg) !important;
            color: var(--navbar-header-text) !important;
            border-bottom: 1px solid var(--navbar-header-border) !important;
        }
        
        .search-box,
        .header-search input {
            background-color: var(--navbar-search-bg) !important;
        }
        
        /* Sidebar Styles */
        .sidebar,
        .sidebar-nav,
        nav.sidebar {
            background-color: var(--navbar-sidebar-bg) !important;
            color: var(--navbar-sidebar-text) !important;
        }
        
        .sidebar .sidebar-link,
        .sidebar a,
        .nav-link {
            color: var(--navbar-sidebar-text) !important;
        }
        
        .sidebar .sidebar-link:hover,
        .sidebar a:hover,
        .nav-link:hover {
            background-color: var(--navbar-hover-bg) !important;
            color: var(--navbar-hover-text) !important;
        }
        
        .sidebar li.active > .sidebar-link,
        .sidebar li.active > a,
        .nav-link.active {
            background-color: var(--navbar-active-link-bg) !important;
            color: var(--navbar-active-link-text) !important;
        }
        
        .sidebar .sidebar-submenu,
        .dropdown-menu {
            background-color: var(--navbar-submenu-bg) !important;
        }
        
        .sidebar .sidebar-submenu a,
        .dropdown-menu a {
            color: var(--navbar-submenu-text) !important;
        }
        
        .sidebar-footer,
        .footer {
            background-color: var(--navbar-footer-bg) !important;
        }
        
        .sidebar .icon,
        .fa,
        .fas,
        .far,
        .fab {
            color: var(--navbar-icon-color) !important;
        }
        
        /* Student Portal Specific */
        .student-navbar {
            background: linear-gradient(135deg, var(--navbar-header-bg) 0%, var(--navbar-sidebar-bg) 100%) !important;
        }
        
        /* Professor Portal Specific */
        .professor-navbar {
            background: linear-gradient(135deg, var(--navbar-header-bg) 0%, var(--navbar-active-link-bg) 100%) !important;
        }
        
        /* Enrollment Page Background */
        .enrollment-page {
            background: linear-gradient(135deg, var(--navbar-header-bg) 0%, var(--navbar-sidebar-bg) 100%) !important;
        }
        
        /* Homepage Background */
        .homepage-hero {
            background: linear-gradient(135deg, var(--navbar-active-link-bg) 0%, var(--navbar-hover-bg) 100%) !important;
        }
        </style>
        ";
    }
    
    public static function getGlobalLogo()
    {
        $logoUrl = UiSetting::get('global', 'logo_url', asset('images/ARTC_logo.png'));
        
        // Add cache busting timestamp if it's a stored file
        if (strpos($logoUrl, '/storage/') !== false) {
            $logoUrl .= '?v=' . time();
        }
        
        return $logoUrl;
    }
    
    public static function getGlobalSettings()
    {
        return UiSetting::getSection('global');
    }
    
    public static function getFavicon()
    {
        $faviconUrl = UiSetting::get('global', 'favicon_url', asset('favicon.ico'));
        
        // Add cache busting timestamp if it's a stored file
        if (strpos($faviconUrl, '/storage/') !== false) {
            $faviconUrl .= '?v=' . time();
        }
        
        return $faviconUrl;
    }
    
    public static function getSiteTitle()
    {
        return UiSetting::get('global', 'site_title', 'A.R.T.C');
    }
    
    /**
     * Get global styles for injection into any page
     */
    public static function getGlobalStyles()
    {
        $styles = self::getNavbarStyles();
        $logo = self::getGlobalLogo();
        $favicon = self::getFavicon();
        
        return [
            'styles' => $styles,
            'logo' => $logo,
            'favicon' => $favicon
        ];
    }
    
    /**
     * Inject global meta tags and styles into page head
     */
    public static function getPageHead()
    {
        $logo = self::getGlobalLogo();
        $favicon = self::getFavicon();
        $title = self::getSiteTitle();
        
        return "
        <link rel=\"icon\" type=\"image/x-icon\" href=\"{$favicon}\">
        <meta name=\"description\" content=\"{$title} - Professional Review Center\">
        <meta property=\"og:title\" content=\"{$title}\">
        <meta property=\"og:image\" content=\"{$logo}\">
        " . self::getNavbarStyles();
    }
}
