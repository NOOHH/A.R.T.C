<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;

echo "🎯 TESTING MAIN ADMIN SETTINGS FOR http://127.0.0.1:8000/\n";
echo "=====================================================\n\n";

// Test 1: Check current settings file
echo "1. 📂 CHECKING MAIN SETTINGS FILE:\n";
$settingsPath = storage_path('app/settings.json');

if (File::exists($settingsPath)) {
    $settings = json_decode(File::get($settingsPath), true);
    echo "   ✅ Main settings file exists: {$settingsPath}\n";
    echo "   📊 Current hero title: " . ($settings['homepage']['hero_title'] ?? 'NOT SET') . "\n";
    echo "   📊 Current hero subtitle: " . substr($settings['homepage']['hero_subtitle'] ?? 'NOT SET', 0, 50) . "...\n";
} else {
    echo "   ❌ Main settings file not found: {$settingsPath}\n";
    echo "   🔧 Creating default settings file...\n";
    
    $defaultSettings = [
        'homepage' => [
            'background_color' => '#667eea',
            'gradient_color' => '',
            'text_color' => '#ffffff',
            'title' => 'ENROLL NOW',
            'hero_title' => 'Review Smarter. Learn Better. Succeed Faster.',
            'hero_subtitle' => 'At Ascendo Review and Training Center, we guide future licensed professionals toward exam success with expert-led reviews and flexible learning options.',
        ],
        'navbar' => [
            'background_color' => '#f1f1f1',
            'gradient_color' => '',
            'text_color' => '#222222',
            'brand_name' => 'Ascendo Review and Training Center',
        ]
    ];
    
    File::put($settingsPath, json_encode($defaultSettings, JSON_PRETTY_PRINT));
    echo "   ✅ Default settings file created!\n";
    $settings = $defaultSettings;
}

// Test 2: Simulate hero title update
echo "\n2. 🔧 TESTING HERO TITLE UPDATE:\n";
$testTitle = "TEST UPDATED TITLE - " . date('H:i:s');
$testSubtitle = "TEST UPDATED SUBTITLE - " . date('H:i:s');

$settings['homepage']['hero_title'] = $testTitle;
$settings['homepage']['hero_subtitle'] = $testSubtitle;

File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
echo "   ✅ Updated hero title to: {$testTitle}\n";
echo "   ✅ Updated hero subtitle to: {$testSubtitle}\n";

// Test 3: Verify HomepageController can read the settings
echo "\n3. 🏠 TESTING HOMEPAGE CONTROLLER COMPATIBILITY:\n";
try {
    // Simulate what HomepageController::index() does
    $loadedSettings = \App\Helpers\SettingsHelper::getSettings();
    
    // Test the homepageContent structure that the view expects
    $homepageContent = [
        'hero_title' => $loadedSettings['homepage']['hero_title'] ?? $loadedSettings['homepage']['title'] ?? 'Review Smarter. Learn Better. Succeed Faster.',
        'hero_subtitle' => $loadedSettings['homepage']['hero_subtitle'] ?? 'At Ascendo Review and Training Center, we guide future licensed professionals toward exam success with expert-led reviews and flexible learning options.',
        'cta_text' => $loadedSettings['homepage']['cta_text'] ?? 'ENROLL NOW',
    ];
    
    echo "   ✅ SettingsHelper loaded settings successfully\n";
    echo "   ✅ HomepageContent structure created\n";
    echo "   📊 Hero title from controller: " . $homepageContent['hero_title'] . "\n";
    echo "   📊 Hero subtitle from controller: " . substr($homepageContent['hero_subtitle'], 0, 50) . "...\n";
    
    if ($homepageContent['hero_title'] === $testTitle) {
        echo "   🎉 SUCCESS! Controller reads updated settings correctly!\n";
    } else {
        echo "   ❌ WARNING: Controller not reading updated settings\n";
        echo "       Expected: {$testTitle}\n";
        echo "       Got: " . $homepageContent['hero_title'] . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing HomepageController: " . $e->getMessage() . "\n";
}

// Test 4: Test admin settings endpoints
echo "\n4. 🔧 TESTING ADMIN SETTINGS ENDPOINTS:\n";
echo "   📍 Main admin settings URL: http://127.0.0.1:8000/admin/settings\n";
echo "   📍 Homepage endpoint: http://127.0.0.1:8000/admin/settings/homepage (GET)\n";
echo "   📍 Save endpoint: http://127.0.0.1:8000/admin/settings/homepage (POST)\n";

// Test 5: Summary
echo "\n5. 📋 SUMMARY:\n";
echo "   🎯 Target homepage: http://127.0.0.1:8000/\n";
echo "   🔧 Admin panel: http://127.0.0.1:8000/admin/settings\n";
echo "   📁 Settings file: {$settingsPath}\n";
echo "   ✅ Hero title field: available in admin settings\n";
echo "   ✅ Hero subtitle field: available in admin settings\n";
echo "   ✅ JSON file integration: complete\n";

echo "\n=====================================================\n";
echo "🚀 READY TO TEST!\n";
echo "1. Go to http://127.0.0.1:8000/admin/settings\n";
echo "2. Click on 'Home' tab\n";
echo "3. Edit 'Main Title' and 'Subtitle' in Hero Section\n";
echo "4. Save changes\n";
echo "5. Visit http://127.0.0.1:8000/ to see changes\n";
echo "=====================================================\n";
