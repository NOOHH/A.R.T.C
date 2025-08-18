<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;
use App\Models\UiSetting;

echo "🔍 DEBUGGING SMARTPREP SETTINGS PERSISTENCE\n";
echo "==========================================\n\n";

// Test 1: Check current state
echo "1. 📂 CURRENT SETTINGS STATE:\n";
$mainSettingsPath = storage_path('app/settings.json');

if (File::exists($mainSettingsPath)) {
    $currentSettings = json_decode(File::get($mainSettingsPath), true);
    echo "   📊 Current hero title: " . ($currentSettings['homepage']['hero_title'] ?? 'NOT SET') . "\n";
    echo "   📊 Last updated: " . ($currentSettings['homepage']['updated_at'] ?? 'NOT SET') . "\n";
} else {
    echo "   ❌ Settings file not found\n";
}

// Test 2: Check database settings
echo "\n2. 🗄️ DATABASE SETTINGS STATE:\n";
try {
    $dbHeroTitle = UiSetting::get('homepage', 'hero_title', 'NOT IN DB');
    $dbHeroSubtitle = UiSetting::get('homepage', 'hero_subtitle', 'NOT IN DB');
    echo "   📊 DB Hero title: {$dbHeroTitle}\n";
    echo "   📊 DB Hero subtitle: " . substr($dbHeroTitle, 0, 50) . "...\n";
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Simulate the SmartPrep updateHomepage method
echo "\n3. 🧪 SIMULATING SMARTPREP UPDATEHOMEPAGE:\n";
$testTitle = "PERSISTENCE TEST - " . date('H:i:s');
$testSubtitle = "Testing if this persists - " . date('H:i:s');

echo "   🔧 Testing with title: {$testTitle}\n";

try {
    // Simulate database save (what the controller does)
    UiSetting::set('homepage', 'hero_title', $testTitle, 'text');
    UiSetting::set('homepage', 'hero_subtitle', $testSubtitle, 'text');
    echo "   ✅ Saved to database via UiSetting\n";
    
    // Simulate JSON save (what the controller also does)
    $settings = [];
    if (File::exists($mainSettingsPath)) {
        $settings = json_decode(File::get($mainSettingsPath), true);
    }
    
    $settings['homepage'] = array_merge($settings['homepage'] ?? [], [
        'hero_title' => $testTitle,
        'hero_subtitle' => $testSubtitle,
        'updated_at' => now()->toISOString()
    ]);
    
    File::put($mainSettingsPath, json_encode($settings, JSON_PRETTY_PRINT));
    echo "   ✅ Saved to JSON file\n";
    
} catch (Exception $e) {
    echo "   ❌ Error in simulation: " . $e->getMessage() . "\n";
}

// Test 4: Verify persistence immediately
echo "\n4. ✅ VERIFYING IMMEDIATE PERSISTENCE:\n";
try {
    // Check database
    $dbCheck = UiSetting::get('homepage', 'hero_title', 'FAILED');
    echo "   📊 DB check: {$dbCheck}\n";
    
    // Check JSON file
    $jsonCheck = json_decode(File::get($mainSettingsPath), true);
    echo "   📊 JSON check: " . ($jsonCheck['homepage']['hero_title'] ?? 'FAILED') . "\n";
    
    if ($dbCheck === $testTitle && $jsonCheck['homepage']['hero_title'] === $testTitle) {
        echo "   🎉 SUCCESS! Settings persisted in both DB and JSON\n";
    } else {
        echo "   ❌ PERSISTENCE FAILED!\n";
        echo "       Expected: {$testTitle}\n";
        echo "       DB got: {$dbCheck}\n";
        echo "       JSON got: " . ($jsonCheck['homepage']['hero_title'] ?? 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error verifying: " . $e->getMessage() . "\n";
}

// Test 5: Check what the main homepage controller would see
echo "\n5. 🏠 WHAT MAIN HOMEPAGE SEES:\n";
try {
    $loadedSettings = \App\Helpers\SettingsHelper::getSettings();
    $homepageWouldShow = $loadedSettings['homepage']['hero_title'] ?? $loadedSettings['homepage']['title'] ?? 'DEFAULT';
    
    echo "   📊 Main homepage would show: {$homepageWouldShow}\n";
    
    if ($homepageWouldShow === $testTitle) {
        echo "   🎉 SUCCESS! Main homepage would show SmartPrep changes\n";
    } else {
        echo "   ❌ ISSUE! Main homepage not showing SmartPrep changes\n";
        echo "       Expected: {$testTitle}\n";
        echo "       Would show: {$homepageWouldShow}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking homepage: " . $e->getMessage() . "\n";
}

echo "\n==========================================\n";
echo "🔍 DIAGNOSTIC COMPLETE\n";

// Test 6: Show current file contents
echo "\n6. 📄 CURRENT SETTINGS FILE CONTENT:\n";
if (File::exists($mainSettingsPath)) {
    $content = File::get($mainSettingsPath);
    echo substr($content, 0, 500) . (strlen($content) > 500 ? "..." : "") . "\n";
}

echo "\n==========================================\n";
