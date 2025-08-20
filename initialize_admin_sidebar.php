<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Initializing Admin Sidebar Settings ===\n";

// Check if admin sidebar settings exist
$adminSettings = App\Models\UiSetting::where('section', 'admin_sidebar')->count();
echo "Current admin sidebar settings count: {$adminSettings}\n";

if ($adminSettings == 0) {
    echo "Creating default admin sidebar settings...\n";
    
    $defaultSettings = [
        'primary_color' => '#111827',
        'secondary_color' => '#1f2937',
        'accent_color' => '#f59e0b',
        'text_color' => '#f9fafb',
        'hover_color' => '#374151'
    ];
    
    foreach ($defaultSettings as $key => $value) {
        App\Models\UiSetting::updateOrCreate(
            ['section' => 'admin_sidebar', 'setting_key' => $key],
            ['setting_value' => $value, 'setting_type' => 'color']
        );
        echo "Created admin_sidebar.{$key} = {$value}\n";
    }
} else {
    echo "Admin sidebar settings already exist.\n";
}

echo "\n=== Current Sidebar Settings After Update ===\n";
$sidebarSettings = App\Models\UiSetting::whereIn('section', ['student_sidebar', 'professor_sidebar', 'admin_sidebar'])
    ->get(['section', 'setting_key', 'setting_value']);

foreach ($sidebarSettings as $setting) {
    echo "{$setting->section}.{$setting->setting_key} = {$setting->setting_value}\n";
}
