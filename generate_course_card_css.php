<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Generating Course Card CSS from Settings ===\n";

// Get all student portal settings
$settings = App\Models\UiSetting::where('section', 'student_portal')->get(['setting_key', 'setting_value']);
$cssVars = [];
$cssRules = [];

foreach ($settings as $setting) {
    $key = $setting->setting_key;
    $value = $setting->setting_value;
    
    if (strpos($key, 'course_') === 0 || strpos($key, 'progress_') === 0 || strpos($key, 'resume_') === 0 || 
        strpos($key, 'badge_') === 0 || strpos($key, 'premium_') === 0 || strpos($key, 'type_') === 0 || 
        strpos($key, 'placeholder_') === 0) {
        echo "{$key} = {$value}\n";
        
        // Convert setting key to CSS variable
        $cssVar = '--' . str_replace('_', '-', $key);
        $cssVars[$cssVar] = $value;
    }
}

echo "\n=== Generated CSS Variables ===\n";
$css = ":root {\n";
foreach ($cssVars as $var => $value) {
    $css .= "    {$var}: {$value};\n";
}
$css .= "}\n\n";

$css .= "/* Course Card Customization Styles */\n";
$css .= ".course-placeholder {\n";
$css .= "    color: var(--placeholder-color, #ffffff) !important;\n";
$css .= "}\n\n";

$css .= ".course-details h3 {\n";
$css .= "    color: var(--course-title-color, #2c3e50) !important;\n";
$css .= "    font-size: var(--course-title-font-size, 1.4rem) !important;\n";
$css .= "    font-weight: var(--course-title-font-weight, 700) !important;\n";
$css .= "    font-style: var(--course-title-font-style, normal) !important;\n";
$css .= "}\n\n";

$css .= ".progress-bar {\n";
$css .= "    background: var(--progress-bar-bg, #e9ecef) !important;\n";
$css .= "}\n\n";

$css .= ".progress-bar::before {\n";
$css .= "    background: linear-gradient(90deg, var(--progress-bar-fill, #667eea), var(--progress-bar-fill, #667eea)) !important;\n";
$css .= "}\n\n";

$css .= ".progress-text {\n";
$css .= "    color: var(--progress-text-color, #6c757d) !important;\n";
$css .= "}\n\n";

$css .= ".resume-btn {\n";
$css .= "    background: linear-gradient(135deg, var(--resume-btn-bg, #667eea) 0%, var(--resume-btn-hover, #5a67d8) 100%) !important;\n";
$css .= "    color: var(--resume-btn-text, #ffffff) !important;\n";
$css .= "}\n\n";

$css .= ".enrollment-badge {\n";
$css .= "    background: linear-gradient(135deg, var(--premium-badge-bg, #8e44ad) 0%, #3498db 100%) !important;\n";
$css .= "    color: var(--badge-text-color, #ffffff) !important;\n";
$css .= "}\n\n";

$css .= ".type-badge {\n";
$css .= "    background: rgba(" . substr(($cssVars['--type-badge-bg'] ?? '#e67e22'), 1) . ", 0.1) !important;\n";
$css .= "    color: var(--type-badge-bg, #e67e22) !important;\n";
$css .= "    border: 1px solid rgba(" . substr(($cssVars['--type-badge-bg'] ?? '#e67e22'), 1) . ", 0.3) !important;\n";
$css .= "}\n\n";

echo $css;

// Save CSS to file
file_put_contents(public_path('css/course-card-customization.css'), $css);
echo "\n=== CSS saved to public/css/course-card-customization.css ===\n";
