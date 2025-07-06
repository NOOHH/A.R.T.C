<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class SettingsHelper
{
    public static function getSettings()
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

    public static function getLogoUrl()
    {
        $settings = self::getSettings();
        
        // Check for global logo first
        if (isset($settings['global_logo']) && $settings['global_logo']) {
            return asset('storage/' . $settings['global_logo']);
        }
        
        // Check for navbar specific logo
        if (isset($settings['navbar']['logo']) && $settings['navbar']['logo']) {
            return asset('storage/' . $settings['navbar']['logo']);
        }
        
        // Default logo
        return asset('images/logo.png');
    }

    public static function getBrandName()
    {
        $settings = self::getSettings();
        return $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center';
    }

    public static function getLoginIllustrationUrl()
    {
        $settings = self::getSettings();
        $loginIllustration = $settings['login']['login_illustration'] ?? null;
        
        if ($loginIllustration) {
            return asset('storage/' . $loginIllustration);
        }
        
        // Default illustration
        return asset('images/login-illustration.png');
    }

    public static function getHomepageStyles()
    {
        $settings = self::getSettings();
        $homepage = $settings['homepage'] ?? [];
        
        $styles = '';
        
        // Background color or image with gradient support
        if (isset($homepage['background_image']) && $homepage['background_image']) {
            $styles .= "
                body {
                    background: url('" . asset('storage/' . $homepage['background_image']) . "') center/cover no-repeat fixed;
                }
                body::before {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(" . self::hexToRgb($homepage['background_color'] ?? '#667eea') . ", 0.8);
                    z-index: -1;
                }
            ";
        } else {
            // Check if gradient is enabled
            if (isset($homepage['gradient_color']) && !empty($homepage['gradient_color'])) {
                $styles .= "
                    body {
                        background: linear-gradient(135deg, " . ($homepage['background_color'] ?? '#667eea') . " 0%, " . $homepage['gradient_color'] . " 100%);
                    }
                ";
            } else {
                $styles .= "
                    body {
                        background: " . ($homepage['background_color'] ?? '#667eea') . ";
                    }
                ";
            }
        }
        
        // Text color
        $styles .= "
            .enroll-link {
                color: " . ($homepage['text_color'] ?? '#ffffff') . " !important;
            }
        ";
        
        return $styles;
    }

    public static function getEnrollmentStyles()
    {
        $settings = self::getSettings();
        $enrollment = $settings['enrollment'] ?? [];
        
        $styles = '';
        
        // Page background with gradient support
        $backgroundStyle = '';
        if (isset($enrollment['page_gradient_color']) && !empty($enrollment['page_gradient_color'])) {
            $backgroundStyle = "background: linear-gradient(135deg, " . ($enrollment['page_background_color'] ?? '#f8f9fa') . " 0%, " . $enrollment['page_gradient_color'] . " 100%) !important;";
        } else {
            $backgroundStyle = "background-color: " . ($enrollment['page_background_color'] ?? '#f8f9fa') . " !important;";
        }
        
        $styles .= "
            body.enrollment-page {
                " . $backgroundStyle . "
            }
            
            .enrollment-container,
            .enrollment-page-content {
                " . $backgroundStyle . "
                color: " . ($enrollment['page_text_color'] ?? '#333333') . " !important;
            }
            
            .enrollment-form-container {
                background-color: " . ($enrollment['form_background_color'] ?? '#ffffff') . " !important;
            }
            
            .main-content,
            .page-content {
                color: " . ($enrollment['page_text_color'] ?? '#333333') . " !important;
            }
            
            .enrollment-btn,
            .btn-enroll,
            .enroll-btn {
                background-color: " . ($enrollment['button_color'] ?? '#667eea') . " !important;
                border-color: " . ($enrollment['button_color'] ?? '#667eea') . " !important;
                color: " . ($enrollment['button_text_color'] ?? '#ffffff') . " !important;
            }
            
            .enrollment-btn:hover,
            .btn-enroll:hover,
            .enroll-btn:hover {
                background-color: " . ($enrollment['button_hover_color'] ?? '#5a67d8') . " !important;
                border-color: " . ($enrollment['button_hover_color'] ?? '#5a67d8') . " !important;
            }
            
            /* Enrollment program card text color */
            .enrollment-program-card h3,
            .enrollment-program-card {
                color: " . ($enrollment['page_text_color'] ?? '#333333') . " !important;
            }
        ";
        
        return $styles;
    }

    public static function getNavbarStyles()
    {
        $settingsCollection = \App\Models\UiSetting::getSection('navbar');
        $navbar = $settingsCollection ? $settingsCollection->toArray() : [];
        
        $backgroundStyle = '';
        
        // Check if gradient is enabled
        if (isset($navbar['navbar_gradient_color']) && !empty($navbar['navbar_gradient_color'])) {
            $backgroundStyle = "background: linear-gradient(135deg, " . ($navbar['navbar_bg_color'] ?? '#f1f1f1') . " 0%, " . $navbar['navbar_gradient_color'] . " 100%) !important;";
        } else {
            $backgroundStyle = "background-color: " . ($navbar['navbar_bg_color'] ?? '#f1f1f1') . " !important;";
        }
        
        $styles = "
            .navbar {
                " . $backgroundStyle . "
            }
            
            .navbar .navbar-brand,
            .navbar .nav-link {
                color: " . ($navbar['navbar_text_color'] ?? '#222222') . " !important;
            }
            
            .navbar .nav-link:hover {
                color: " . ($navbar['navbar_hover_color'] ?? self::darkenColor($navbar['navbar_text_color'] ?? '#222222', 20)) . " !important;
            }
            
            .navbar .nav-link.active {
                color: " . ($navbar['navbar_active_color'] ?? '#0056b3') . " !important;
            }
        ";
        
        return $styles;
    }

    public static function getFooterStyles()
    {
        $settingsCollection = \App\Models\UiSetting::getSection('footer');
        $footer = $settingsCollection ? $settingsCollection->toArray() : [];
        
        $backgroundStyle = '';
        
        // Check if gradient is enabled
        if (isset($footer['footer_gradient_color']) && !empty($footer['footer_gradient_color'])) {
            $backgroundStyle = "background: linear-gradient(135deg, " . ($footer['footer_bg_color'] ?? '#ffffff') . " 0%, " . $footer['footer_gradient_color'] . " 100%) !important;";
        } else {
            $backgroundStyle = "background-color: " . ($footer['footer_bg_color'] ?? '#ffffff') . " !important;";
        }
        
        $styles = "
            .footer {
                " . $backgroundStyle . "
                color: " . ($footer['footer_text_color'] ?? '#444444') . " !important;
            }
            
            .footer-links a {
                color: " . ($footer['footer_link_color'] ?? '#adb5bd') . " !important;
            }
            
            .footer-links a:hover {
                color: " . ($footer['footer_link_hover_color'] ?? '#ffffff') . " !important;
            }
        ";
        
        return $styles;
    }

    public static function getProgramCardStyles()
    {
        $settings = self::getSettings();
        $cards = $settings['program_cards'] ?? [];
        
        $backgroundStyle = '';
        
        // Check if gradient is enabled for program cards
        if (isset($cards['gradient_color']) && !empty($cards['gradient_color'])) {
            $backgroundStyle = "background: linear-gradient(135deg, " . ($cards['background_color'] ?? '#f9f9f9') . " 0%, " . $cards['gradient_color'] . " 100%) !important;";
        } else {
            $backgroundStyle = "background-color: " . ($cards['background_color'] ?? '#f9f9f9') . " !important;";
        }
        
        $styles = "
            .program-card,
            .enrollment-program-card {
                " . $backgroundStyle . "
                color: " . ($cards['text_color'] ?? '#333333') . " !important;
                border-color: " . ($cards['border_color'] ?? '#dddddd') . " !important;
            }
            
            .program-card:hover,
            .enrollment-program-card:hover {
                border-color: " . ($cards['hover_color'] ?? '#1c2951') . " !important;
                box-shadow: 0 4px 8px rgba(" . self::hexToRgb($cards['hover_color'] ?? '#1c2951') . ", 0.3) !important;
            }
            
            .program-card h3,
            .enrollment-program-card h3 {
                color: " . ($cards['text_color'] ?? '#333333') . " !important;
            }
        ";
        
        return $styles;
    }

    public static function getButtonStyles()
    {
        $settings = self::getSettings();
        $buttons = $settings['buttons'] ?? [];
        
        $styles = "
            .btn-primary {
                background-color: " . ($buttons['primary_color'] ?? '#667eea') . " !important;
                border-color: " . ($buttons['primary_color'] ?? '#667eea') . " !important;
                color: " . ($buttons['primary_text_color'] ?? '#ffffff') . " !important;
            }
            
            .btn-primary:hover {
                background-color: " . ($buttons['primary_hover_color'] ?? '#5a67d8') . " !important;
                border-color: " . ($buttons['primary_hover_color'] ?? '#5a67d8') . " !important;
            }
            
            .btn-secondary {
                background-color: " . ($buttons['secondary_color'] ?? '#6c757d') . " !important;
                border-color: " . ($buttons['secondary_color'] ?? '#6c757d') . " !important;
                color: " . ($buttons['secondary_text_color'] ?? '#ffffff') . " !important;
            }
            
            .btn-secondary:hover {
                background-color: " . ($buttons['secondary_hover_color'] ?? '#5a6268') . " !important;
                border-color: " . ($buttons['secondary_hover_color'] ?? '#5a6268') . " !important;
            }
            
            .btn-success {
                background-color: " . ($buttons['success_color'] ?? '#28a745') . " !important;
                border-color: " . ($buttons['success_color'] ?? '#28a745') . " !important;
                color: " . ($buttons['success_text_color'] ?? '#ffffff') . " !important;
            }
            
            .btn-success:hover {
                background-color: " . ($buttons['success_hover_color'] ?? '#218838') . " !important;
                border-color: " . ($buttons['success_hover_color'] ?? '#218838') . " !important;
            }
            
            .btn-danger {
                background-color: " . ($buttons['danger_color'] ?? '#dc3545') . " !important;
                border-color: " . ($buttons['danger_color'] ?? '#dc3545') . " !important;
                color: " . ($buttons['danger_text_color'] ?? '#ffffff') . " !important;
            }
            
            .btn-danger:hover {
                background-color: " . ($buttons['danger_hover_color'] ?? '#c82333') . " !important;
                border-color: " . ($buttons['danger_hover_color'] ?? '#c82333') . " !important;
            }
        ";
        
        return $styles;
    }

    public static function getLoginStyles()
    {
        $settings = self::getSettings();
        $login = $settings['login'] ?? [];
        
        $styles = '';
        
        // Background for the left side (main background)
        if (isset($login['background_image']) && $login['background_image']) {
            $styles .= "
                body.login-page .left {
                    background: url('" . asset('storage/' . $login['background_image']) . "') center/cover no-repeat !important;
                }
                body.login-page .left::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(" . self::hexToRgb($login['background_color'] ?? '#f8f9fa') . ", 0.8);
                    z-index: 0;
                }
                body.login-page .left > * {
                    position: relative;
                    z-index: 1;
                }
            ";
        } else {
            // Check if gradient is enabled for login
            if (isset($login['gradient_color']) && !empty($login['gradient_color'])) {
                $styles .= "
                    body.login-page .left {
                        background: linear-gradient(135deg, " . ($login['background_color'] ?? '#8e2de2') . " 0%, " . $login['gradient_color'] . " 100%) !important;
                    }
                ";
            } else {
                $styles .= "
                    body.login-page .left {
                        background: " . ($login['background_color'] ?? '#8e2de2') . " !important;
                        background: linear-gradient(to bottom, " . ($login['background_color'] ?? '#8e2de2') . ", " . self::darkenColor($login['background_color'] ?? '#8e2de2', 20) . ") !important;
                    }
                ";
            }
            
            // Add illustration as background if available
            if (isset($login['login_illustration']) && $login['login_illustration']) {
                $styles .= "
                    body.login-page .left {
                        background: " . ($login['background_color'] ?? '#8e2de2') . " url('" . asset('storage/' . $login['login_illustration']) . "') center/contain no-repeat !important;
                        background-size: auto 60% !important;
                        background-position: center 70% !important;
                    }
                    body.login-page .left::after {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(" . self::hexToRgb($login['background_color'] ?? '#8e2de2') . ", 0.1);
                        z-index: 0;
                    }
                    body.login-page .left .review-text,
                    body.login-page .left .copyright {
                        position: relative;
                        z-index: 1;
                        text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
                    }
                ";
            }
        }
        
        $styles .= "
            body.login-page .right {
                background-color: " . ($login['card_background'] ?? '#ffffff') . " !important;
                color: " . ($login['text_color'] ?? '#333333') . " !important;
            }
            
            body.login-page .right h2,
            body.login-page .right label,
            body.login-page .right .brand-text {
                color: " . ($login['text_color'] ?? '#333333') . " !important;
            }
            
            body.login-page .login-form input[type='text'],
            body.login-page .login-form input[type='email'],
            body.login-page .login-form input[type='password'] {
                border-color: " . ($login['input_border_color'] ?? '#dee2e6') . " !important;
            }
            
            body.login-page .login-form input[type='text']:focus,
            body.login-page .login-form input[type='email']:focus,
            body.login-page .login-form input[type='password']:focus {
                border-color: " . ($login['input_focus_color'] ?? '#667eea') . " !important;
                box-shadow: 0 0 0 0.2rem rgba(" . self::hexToRgb($login['input_focus_color'] ?? '#667eea') . ", 0.25) !important;
            }
            
            body.login-page .login-form button[type='submit'] {
                background-color: " . ($login['accent_color'] ?? '#667eea') . " !important;
                border-color: " . ($login['accent_color'] ?? '#667eea') . " !important;
                color: #ffffff !important;
            }
            
            body.login-page .login-form button[type='submit']:hover {
                background-color: " . self::darkenColor($login['accent_color'] ?? '#667eea', 20) . " !important;
                border-color: " . self::darkenColor($login['accent_color'] ?? '#667eea', 20) . " !important;
            }
            
            body.login-page .login-form .google-btn {
                background-color: #ffffff !important;
                border-color: #888888 !important;
                color: #222222 !important;
            }
            
            body.login-page .login-form .google-btn:hover {
                background-color: #f3f3f3 !important;
                border-color: " . ($login['accent_color'] ?? '#667eea') . " !important;
            }
        ";
        
        return $styles;
    }

    public static function getHomepageCustomStyles()
    {
        $settingsCollection = \App\Models\UiSetting::getSection('homepage');
        $settings = $settingsCollection ? $settingsCollection->toArray() : [];
        
        // Default values
        $defaults = [
            'hero_bg_color' => '#667eea',
            'hero_text_color' => '#ffffff',
            'hero_button_color' => '#4CAF50',
            'programs_bg_color' => '#f8f9fa',
            'programs_text_color' => '#333333',
            'modalities_bg_color' => '#667eea',
            'modalities_text_color' => '#ffffff',
            'about_bg_color' => '#ffffff',
            'about_text_color' => '#333333',
        ];
        
        // Merge with defaults
        $settings = array_merge($defaults, $settings);
        
        $css = "<style>";
        
        // Hero section styles
        $css .= "
        .homepage-hero {
            background: linear-gradient(135deg, {$settings['hero_bg_color']} 0%, {$settings['hero_bg_color']}dd 100%) !important;
            color: {$settings['hero_text_color']} !important;
        }
        .homepage-hero .hero-title {
            color: {$settings['hero_text_color']} !important;
        }
        .homepage-hero .hero-subtitle {
            color: {$settings['hero_text_color']} !important;
        }
        .homepage-hero .enroll-btn {
            background-color: {$settings['hero_button_color']} !important;
            border-color: {$settings['hero_button_color']} !important;
        }
        .homepage-hero .enroll-btn:hover {
            background-color: {$settings['hero_button_color']}dd !important;
            border-color: {$settings['hero_button_color']}dd !important;
        }
        ";
        
        // Programs section styles
        $css .= "
        .programs-section {
            background-color: {$settings['programs_bg_color']} !important;
            color: {$settings['programs_text_color']} !important;
        }
        .programs-section h2 {
            color: {$settings['programs_text_color']} !important;
        }
        .programs-section .lead {
            color: {$settings['programs_text_color']}aa !important;
        }
        ";
        
        // Modalities section styles
        $css .= "
        .modalities-section {
            background: linear-gradient(135deg, {$settings['modalities_bg_color']} 0%, {$settings['modalities_bg_color']}dd 100%) !important;
            color: {$settings['modalities_text_color']} !important;
        }
        .modalities-section h2 {
            color: {$settings['modalities_text_color']} !important;
        }
        .modalities-section .lead {
            color: {$settings['modalities_text_color']}aa !important;
        }
        ";
        
        // About section styles
        $css .= "
        .about-section {
            background-color: {$settings['about_bg_color']} !important;
            color: {$settings['about_text_color']} !important;
        }
        .about-section h2 {
            color: {$settings['about_text_color']} !important;
        }
        .about-section .lead {
            color: {$settings['about_text_color']}aa !important;
        }
        ";
        
        $css .= "</style>";
        
        return $css;
    }
    
    public static function getHomepageContent()
    {
        $settingsCollection = \App\Models\UiSetting::getSection('homepage');
        $settings = $settingsCollection ? $settingsCollection->toArray() : [];
        
        // Default content
        $defaults = [
            'hero_title' => 'Review <span style="color: #4CAF50;">Smarter.</span><br>Learn <span style="color: #4CAF50;">Better.</span><br>Succeed <span style="color: #4CAF50;">Faster.</span>',
            'hero_subtitle' => 'At Ascendo Review and Training Center, we guide future licensed professionals toward exam success with expert-led reviews and flexible learning options.',
            'hero_button_text' => 'ENROLL NOW',
            'programs_title' => 'Programs Offered',
            'programs_subtitle' => 'Choose from our comprehensive review programs designed for success',
            'modalities_title' => 'Learning Modalities',
            'modalities_subtitle' => 'Choose the learning style that works best for you',
            'about_title' => 'About Us',
            'about_subtitle' => 'Learn more about our mission and values',
        ];
        
        return array_merge($defaults, $settings);
    }

    public static function getAllStyles()
    {
        $allStyles = '';
        $allStyles .= self::getHomepageStyles();
        $allStyles .= self::getEnrollmentStyles();
        $allStyles .= self::getNavbarStyles();
        $allStyles .= self::getFooterStyles();
        $allStyles .= self::getProgramCardStyles();
        $allStyles .= self::getButtonStyles();
        $allStyles .= self::getLoginStyles();
        
        return $allStyles;
    }

    // Helper methods
    private static function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $rgb = array_map('hexdec', str_split($hex, 2));
        
        return implode(', ', $rgb);
    }

    private static function darkenColor($hex, $percent)
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $rgb = array_map('hexdec', str_split($hex, 2));
        
        foreach ($rgb as &$color) {
            $color = max(0, min(255, (int)round($color - ($color * $percent / 100))));
        }
        
        return '#' . implode('', array_map(function($color) {
            return str_pad(dechex((int)$color), 2, '0', STR_PAD_LEFT);
        }, $rgb));
    }
}
