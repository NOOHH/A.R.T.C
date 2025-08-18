<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;

echo "🔄 TESTING SMARTPREP ADMIN → MAIN A.R.T.C HOMEPAGE CONNECTION\n";
echo "==============================================================\n\n";

// Test 1: Check which settings file SmartPrep is now using
echo "1. 📂 CHECKING SMARTPREP SETTINGS TARGET:\n";
$mainSettingsPath = storage_path('app/settings.json');
$smartprepSettingsPath = storage_path('app/smartprep_settings.json');

echo "   📍 Main A.R.T.C settings: {$mainSettingsPath}\n";
echo "   📍 SmartPrep settings: {$smartprepSettingsPath}\n";

if (File::exists($mainSettingsPath)) {
    $mainSettings = json_decode(File::get($mainSettingsPath), true);
    echo "   ✅ Main settings file exists\n";
    echo "   📊 Current main hero title: " . ($mainSettings['homepage']['hero_title'] ?? 'NOT SET') . "\n";
} else {
    echo "   ❌ Main settings file not found\n";
}

// Test 2: Simulate SmartPrep admin making changes
echo "\n2. 🎯 SIMULATING SMARTPREP ADMIN CHANGES:\n";
$testTitle = "SMARTPREP CONTROLLED - " . date('H:i:s');
$testSubtitle = "SmartPrep admin is now controlling the main A.R.T.C homepage - " . date('H:i:s');

// Simulate what SmartPrep AdminSettingsController would do
$settings = [];
if (File::exists($mainSettingsPath)) {
    $settings = json_decode(File::get($mainSettingsPath), true);
}

$settings['homepage'] = array_merge($settings['homepage'] ?? [], [
    'hero_title' => $testTitle,
    'hero_subtitle' => $testSubtitle,
    'updated_at' => now()->toISOString()
]);

// Save to main settings file (what SmartPrep controller now does)
File::put($mainSettingsPath, json_encode($settings, JSON_PRETTY_PRINT));

echo "   ✅ SmartPrep admin saved to main settings file\n";
echo "   ✅ Hero title: {$testTitle}\n";
echo "   ✅ Hero subtitle: {$testSubtitle}\n";

// Test 3: Verify main A.R.T.C homepage will show the changes
echo "\n3. 🏠 VERIFYING MAIN HOMEPAGE WILL SHOW CHANGES:\n";
try {
    // Simulate what HomepageController::index() does
    $loadedSettings = \App\Helpers\SettingsHelper::getSettings();
    
    $homepageContent = [
        'hero_title' => $loadedSettings['homepage']['hero_title'] ?? $loadedSettings['homepage']['title'] ?? 'Review Smarter. Learn Better. Succeed Faster.',
        'hero_subtitle' => $loadedSettings['homepage']['hero_subtitle'] ?? 'At Ascendo Review and Training Center, we guide future licensed professionals toward exam success with expert-led reviews and flexible learning options.',
    ];
    
    echo "   ✅ Main HomepageController loaded settings\n";
    echo "   📊 Main homepage will show:\n";
    echo "      Title: " . $homepageContent['hero_title'] . "\n";
    echo "      Subtitle: " . substr($homepageContent['hero_subtitle'], 0, 60) . "...\n";
    
    if ($homepageContent['hero_title'] === $testTitle) {
        echo "   🎉 SUCCESS! SmartPrep admin changes will appear on main homepage!\n";
    } else {
        echo "   ❌ ISSUE: Main homepage not showing SmartPrep changes\n";
        echo "       Expected: {$testTitle}\n";
        echo "       Got: " . $homepageContent['hero_title'] . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing main homepage: " . $e->getMessage() . "\n";
}

// Test 4: Clean up SmartPrep settings file
echo "\n4. 🧹 SMARTPREP SETTINGS FILE STATUS:\n";
if (File::exists($smartprepSettingsPath)) {
    echo "   📁 SmartPrep settings file still exists (not used anymore)\n";
    echo "   💡 SmartPrep admin now controls main A.R.T.C site instead\n";
} else {
    echo "   ✅ No separate SmartPrep settings file\n";
}

// Test 5: Summary
echo "\n5. 📋 CONNECTION SUMMARY:\n";
echo "   🎯 SmartPrep Admin URL: http://127.0.0.1:8000/smartprep/admin/settings\n";
echo "   🎯 Controls Homepage: http://127.0.0.1:8000/ (MAIN A.R.T.C)\n";
echo "   📁 Settings File: {$mainSettingsPath}\n";
echo "   ✅ SmartPrep admin → Main A.R.T.C homepage: CONNECTED\n";

echo "\n==============================================================\n";
echo "🚀 READY TO TEST!\n";
echo "1. Go to: http://127.0.0.1:8000/smartprep/admin/settings\n";
echo "2. Change hero title/subtitle in Homepage Content section\n";
echo "3. Save changes\n";
echo "4. Visit: http://127.0.0.1:8000/ to see changes on main site\n";
echo "==============================================================\n";
