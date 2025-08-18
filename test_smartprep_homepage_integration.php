<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING SMARTPREP HOMEPAGE INTEGRATION ===\n\n";

// Test 1: Check current homepage settings in database
echo "1. Current homepage settings in database:\n";
$homepageSettings = \App\Models\UiSetting::getSection('homepage');
if ($homepageSettings) {
    foreach ($homepageSettings as $key => $value) {
        echo "   {$key} = '{$value}'\n";
    }
} else {
    echo "   No homepage settings found in database\n";
}

// Test 2: Check what SettingsHelper returns
echo "\n2. SettingsHelper::getHomepageContent() returns:\n";
$homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
foreach ($homepageContent as $key => $value) {
    echo "   {$key} = '{$value}'\n";
}

// Test 3: Simulate SmartPrep admin saving a setting
echo "\n3. Testing SmartPrep admin save functionality:\n";
try {
    // Simulate what the SmartPrep admin controller does
    \App\Models\UiSetting::set('homepage', 'hero_title', 'TEST TITLE FROM SMARTPREP - ' . date('H:i:s'), 'text');
    \App\Models\UiSetting::set('homepage', 'hero_subtitle', 'TEST SUBTITLE FROM SMARTPREP - ' . date('H:i:s'), 'text');
    echo "   ✅ Successfully saved test settings via UiSetting::set()\n";
    
    // Check if they were saved
    $testTitle = \App\Models\UiSetting::get('homepage', 'hero_title', 'NOT_FOUND');
    $testSubtitle = \App\Models\UiSetting::get('homepage', 'hero_subtitle', 'NOT_FOUND');
    echo "   ✅ Retrieved from database:\n";
    echo "      hero_title = '{$testTitle}'\n";
    echo "      hero_subtitle = '{$testSubtitle}'\n";
    
} catch (Exception $e) {
    echo "   ❌ Error saving settings: " . $e->getMessage() . "\n";
}

// Test 4: Check if SettingsHelper picks up the new settings
echo "\n4. Checking if SettingsHelper picks up new settings:\n";
$updatedContent = \App\Helpers\SettingsHelper::getHomepageContent();
echo "   hero_title = '{$updatedContent['hero_title']}'\n";
echo "   hero_subtitle = '{$updatedContent['hero_subtitle']}'\n";

// Test 5: Check if the homepage view would display the new settings
echo "\n5. Testing homepage view integration:\n";
if (strpos($updatedContent['hero_title'], 'TEST TITLE FROM SMARTPREP') !== false) {
    echo "   ✅ SUCCESS! Homepage would display the updated title\n";
} else {
    echo "   ❌ FAILURE! Homepage would NOT display the updated title\n";
    echo "      Expected to contain: 'TEST TITLE FROM SMARTPREP'\n";
    echo "      Actual: '{$updatedContent['hero_title']}'\n";
}

// Test 6: Check the SmartPrep admin controller's getCurrentSettings method
echo "\n6. Testing SmartPrep admin controller getCurrentSettings():\n";
try {
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getCurrentSettings');
    $method->setAccessible(true);
    $settings = $method->invoke($controller);
    
    echo "   ✅ getCurrentSettings() successful\n";
    echo "   Homepage settings from controller:\n";
    if (isset($settings['homepage'])) {
        foreach ($settings['homepage'] as $key => $value) {
            echo "      {$key} = '{$value}'\n";
        }
    } else {
        echo "      No homepage settings found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing controller: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
