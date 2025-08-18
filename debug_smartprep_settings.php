<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING SMARTPREP SETTINGS ===\n\n";

// Test 1: Check current database settings
echo "1. Current UI Settings in Database:\n";
$uiSettings = \App\Models\UiSetting::all();
foreach($uiSettings as $setting) {
    echo "   {$setting->section}.{$setting->setting_key} = {$setting->setting_value}\n";
}

if($uiSettings->isEmpty()) {
    echo "   No settings found in database\n";
}

// Test 2: Check what SettingsHelper::getHomepageContent() returns
echo "\n2. SettingsHelper::getHomepageContent() returns:\n";
$homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
foreach($homepageContent as $key => $value) {
    echo "   {$key} = {$value}\n";
}

// Test 3: Test saving a setting manually
echo "\n3. Testing manual setting save:\n";
try {
    \App\Models\UiSetting::set('homepage', 'hero_title', 'TEST TITLE FROM SCRIPT', 'text');
    echo "   ✓ Successfully saved test setting\n";
    
    $testValue = \App\Models\UiSetting::get('homepage', 'hero_title', 'default');
    echo "   ✓ Retrieved value: {$testValue}\n";
} catch(Exception $e) {
    echo "   ✗ Error saving setting: " . $e->getMessage() . "\n";
}

// Test 4: Check what SmartPrep controller's getCurrentSettings() returns
echo "\n4. SmartPrep AdminSettingsController getCurrentSettings():\n";
try {
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getCurrentSettings');
    $method->setAccessible(true);
    $settings = $method->invoke($controller);
    
    echo "   Homepage settings:\n";
    if(isset($settings['homepage'])) {
        foreach($settings['homepage'] as $key => $value) {
            echo "      {$key} = {$value}\n";
        }
    } else {
        echo "      No homepage settings found\n";
    }
} catch(Exception $e) {
    echo "   ✗ Error calling getCurrentSettings(): " . $e->getMessage() . "\n";
}

// Test 5: Check if main settings.json file exists and what it contains
echo "\n5. Main settings.json file:\n";
$settingsPath = storage_path('app/settings.json');
if(file_exists($settingsPath)) {
    echo "   ✓ File exists: {$settingsPath}\n";
    $jsonContent = json_decode(file_get_contents($settingsPath), true);
    if(isset($jsonContent['homepage'])) {
        echo "   Homepage settings in JSON:\n";
        foreach($jsonContent['homepage'] as $key => $value) {
            echo "      {$key} = {$value}\n";
        }
    } else {
        echo "   No homepage settings in JSON file\n";
    }
} else {
    echo "   ✗ File does not exist: {$settingsPath}\n";
}

echo "\n=== END DEBUG ===\n";
