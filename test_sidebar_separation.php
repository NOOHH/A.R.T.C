<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Sidebar Settings Separation ===\n";

$controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();

// Test 1: Update Student Sidebar
echo "\n1. Updating Student Sidebar...\n";
$studentRequest = \Illuminate\Http\Request::create('/smartprep/admin/settings/sidebar', 'POST');
$studentRequest->merge([
    'role' => 'student',
    'colors' => [
        'primary_color' => '#ff0000',
        'secondary_color' => '#ff1111',
        'accent_color' => '#ff2222',
        'text_color' => '#ff3333',
        'hover_color' => '#ff4444'
    ]
]);
$studentRequest->headers->set('Content-Type', 'application/json');

try {
    $response = $controller->updateSidebar($studentRequest);
    echo "Student update response: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Student update error: " . $e->getMessage() . "\n";
}

// Test 2: Update Professor Sidebar
echo "\n2. Updating Professor Sidebar...\n";
$professorRequest = \Illuminate\Http\Request::create('/smartprep/admin/settings/sidebar', 'POST');
$professorRequest->merge([
    'role' => 'professor',
    'colors' => [
        'primary_color' => '#00ff00',
        'secondary_color' => '#11ff11',
        'accent_color' => '#22ff22',
        'text_color' => '#33ff33',
        'hover_color' => '#44ff44'
    ]
]);
$professorRequest->headers->set('Content-Type', 'application/json');

try {
    $response = $controller->updateSidebar($professorRequest);
    echo "Professor update response: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Professor update error: " . $e->getMessage() . "\n";
}

// Test 3: Update Admin Sidebar
echo "\n3. Updating Admin Sidebar...\n";
$adminRequest = \Illuminate\Http\Request::create('/smartprep/admin/settings/sidebar', 'POST');
$adminRequest->merge([
    'role' => 'admin',
    'colors' => [
        'primary_color' => '#0000ff',
        'secondary_color' => '#1111ff',
        'accent_color' => '#2222ff',
        'text_color' => '#3333ff',
        'hover_color' => '#4444ff'
    ]
]);
$adminRequest->headers->set('Content-Type', 'application/json');

try {
    $response = $controller->updateSidebar($adminRequest);
    echo "Admin update response: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Admin update error: " . $e->getMessage() . "\n";
}

// Test 4: Verify separation
echo "\n4. Verifying Sidebar Settings Separation...\n";
$sidebarSettings = App\Models\UiSetting::whereIn('section', ['student_sidebar', 'professor_sidebar', 'admin_sidebar'])
    ->orderBy('section')
    ->orderBy('setting_key')
    ->get(['section', 'setting_key', 'setting_value']);

foreach ($sidebarSettings as $setting) {
    echo "{$setting->section}.{$setting->setting_key} = {$setting->setting_value}\n";
}

echo "\n=== Test Summary ===\n";
echo "- Student colors should be red (#ff...)\n";
echo "- Professor colors should be green (#...ff...)\n";
echo "- Admin colors should be blue (#...ff)\n";
echo "- Each section should have its own separate settings\n";
