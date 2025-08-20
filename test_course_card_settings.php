<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Course Card Settings Update ===\n";

// Simulate a request to update course card settings
$controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();

// Create a mock request with course card data
$request = \Illuminate\Http\Request::create('/smartprep/admin/settings/student', 'POST', [
    'course_card_bg' => '#ffffff',
    'progress_bar_bg' => '#e9ecef',
    'progress_bar_fill' => '#667eea',
    'progress_text_color' => '#6c757d',
    'resume_btn_bg' => '#667eea',
    'resume_btn_text' => '#ffffff',
    'resume_btn_hover' => '#5a67d8',
    'premium_badge_bg' => '#8e44ad',
    'type_badge_bg' => '#e67e22',
    'badge_text_color' => '#ffffff',
    'placeholder_color' => '#ffffff',
    'course_title_font_size' => '1.4rem',
    'course_title_font_weight' => '700',
    'course_title_font_style' => 'normal',
    'course_title_color' => '#2c3e50',
    'progress_bar_color' => '#28a745',
]);

$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

try {
    $response = $controller->updateStudent($request);
    echo "Update response: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Checking Course Card Settings in Database ===\n";
$courseCardSettings = App\Models\UiSetting::where('section', 'student_portal')
    ->whereIn('setting_key', [
        'progress_bar_bg', 'progress_bar_fill', 'progress_text_color',
        'resume_btn_bg', 'resume_btn_text', 'resume_btn_hover',
        'premium_badge_bg', 'type_badge_bg', 'badge_text_color',
        'placeholder_color', 'course_title_font_size', 'course_title_font_weight', 'course_title_font_style'
    ])
    ->get(['setting_key', 'setting_value']);

foreach ($courseCardSettings as $setting) {
    echo "student_portal.{$setting->setting_key} = {$setting->setting_value}\n";
}
