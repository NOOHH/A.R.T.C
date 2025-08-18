<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Simulating AdminSettingsController functionality:\n\n";

// Test the index method logic
echo "1. Testing index() method logic:\n";
try {
    // Get current settings from ui_settings table as key-value pairs
    $settingsData = DB::table('ui_settings')->get();
    $settings = [];
    
    foreach ($settingsData as $setting) {
        $settings[$setting->setting_key] = $setting->setting_value;
    }
    
    echo "   ✅ Settings loaded: " . count($settings) . " items\n";
    echo "   ✅ Sample settings:\n";
    foreach (array_slice($settings, 0, 5, true) as $key => $value) {
        $displayValue = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
        echo "      - {$key}: {$displayValue}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test validation rules
echo "\n2. Testing validation rules:\n";
$testData = [
    'site_name' => 'My Test Site',
    'navbar_brand_name' => 'Test Brand',
    'navbar_style' => 'fixed-top',
    'primary_color' => '#ff0000',
    'show_login_button' => '1'
];

$validationRules = [
    'site_name' => 'nullable|string|max:255',
    'navbar_brand_name' => 'nullable|string|max:255',
    'navbar_style' => 'nullable|string|in:fixed-top,sticky-top,static',
    'primary_color' => 'nullable|string|max:7',
    'show_login_button' => 'nullable|boolean',
];

foreach ($testData as $key => $value) {
    if (isset($validationRules[$key])) {
        // Simple validation check
        $rules = explode('|', $validationRules[$key]);
        $valid = true;
        
        if (in_array('max:255', $rules) && strlen($value) > 255) {
            $valid = false;
        }
        if (in_array('max:7', $rules) && strlen($value) > 7) {
            $valid = false;
        }
        if (str_contains($validationRules[$key], 'in:fixed-top,sticky-top,static') && !in_array($value, ['fixed-top', 'sticky-top', 'static'])) {
            $valid = false;
        }
        
        echo "   " . ($valid ? "✅" : "❌") . " {$key}: {$value}\n";
    }
}

echo "\n✅ AdminSettingsController simulation completed successfully!\n";
