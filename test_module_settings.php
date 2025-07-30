<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test inserting module management settings
try {
    echo 'Testing AdminSetting model...' . PHP_EOL;
    
    // Create the settings if they don't exist
    App\Models\AdminSetting::updateOrCreate(
        ['setting_key' => 'professor_module_management_enabled'],
        ['setting_value' => '0', 'setting_description' => 'Enable/disable module management for professors', 'is_active' => 1]
    );
    
    App\Models\AdminSetting::updateOrCreate(
        ['setting_key' => 'professor_module_management_whitelist'],
        ['setting_value' => '', 'setting_description' => 'Comma-separated list of professor IDs allowed to manage modules', 'is_active' => 1]
    );
    
    echo 'Professor module management settings created successfully!' . PHP_EOL;
    
    // Check the settings
    $enabled = App\Models\AdminSetting::where('setting_key', 'professor_module_management_enabled')->first();
    $whitelist = App\Models\AdminSetting::where('setting_key', 'professor_module_management_whitelist')->first();
    
    echo 'Module Management Enabled: ' . ($enabled ? $enabled->setting_value : 'not found') . PHP_EOL;
    echo 'Module Management Whitelist: ' . ($whitelist ? $whitelist->setting_value : 'not found') . PHP_EOL;
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo 'Stack trace: ' . $e->getTraceAsString() . PHP_EOL;
}
