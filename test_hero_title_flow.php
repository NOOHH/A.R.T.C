<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING HERO TITLE SAVE/LOAD FLOW ===\n\n";

// Test 1: Save a new hero title to the database
echo "1. 🔧 SAVING NEW HERO TITLE TO DATABASE:\n";
$testTitle = "Test Hero Title " . time();
\App\Models\UiSetting::set('homepage', 'hero_title', $testTitle, 'text');
echo "   ✅ Saved hero title: {$testTitle}\n\n";

// Test 2: Verify it's saved in the database
echo "2. 📊 VERIFYING DATABASE STORAGE:\n";
$savedTitle = \App\Models\UiSetting::get('homepage', 'hero_title');
echo "   📄 Retrieved from database: {$savedTitle}\n";
echo "   " . ($savedTitle === $testTitle ? "✅ MATCHES" : "❌ MISMATCH") . "\n\n";

// Test 3: Test UiSettingsHelper
echo "3. 🔄 TESTING UiSettingsHelper:\n";
$helperData = \App\Helpers\UiSettingsHelper::getSection('homepage');
echo "   📄 UiSettingsHelper hero_title: " . ($helperData['hero_title'] ?? 'NOT FOUND') . "\n";
echo "   " . (($helperData['hero_title'] ?? '') === $testTitle ? "✅ MATCHES" : "❌ MISMATCH") . "\n\n";

// Test 4: Test SmartPrep admin settings controller
echo "4. ⚙️ TESTING SMARTPREP ADMIN SETTINGS CONTROLLER:\n";
$controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('getCurrentSettings');
$method->setAccessible(true);
$settings = $method->invoke($controller);
echo "   📄 Controller settings hero_title: " . ($settings['homepage']['hero_title'] ?? 'NOT FOUND') . "\n";
echo "   " . (($settings['homepage']['hero_title'] ?? '') === $testTitle ? "✅ MATCHES" : "❌ MISMATCH") . "\n\n";

// Test 5: Test SmartPrep homepage controller
echo "5. 🏠 TESTING SMARTPREP HOMEPAGE CONTROLLER:\n";
$homepageController = new \App\Http\Controllers\Smartprep\HomepageController();
// This is a bit tricky to test without HTTP context, but we can test the UiSettingsHelper it uses
$homepageData = \App\Helpers\UiSettingsHelper::getAll();
echo "   📄 Homepage data hero_title: " . ($homepageData['homepage']['hero_title'] ?? 'NOT FOUND') . "\n";
echo "   " . (($homepageData['homepage']['hero_title'] ?? '') === $testTitle ? "✅ MATCHES" : "❌ MISMATCH") . "\n\n";

echo "=== TEST COMPLETE ===\n";
echo "If all tests show ✅ MATCHES, then the hero title save/load flow is working correctly!\n";
