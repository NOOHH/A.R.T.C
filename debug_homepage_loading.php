<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;

echo "🔍 DEBUGGING HOMEPAGE SETTINGS LOADING\n";
echo "======================================\n\n";

$settingsPath = storage_path('app/smartprep_settings.json');

echo "1. 📄 CHECKING SETTINGS FILE:\n";
if (File::exists($settingsPath)) {
    $settings = json_decode(File::get($settingsPath), true);
    echo "   ✅ File exists: {$settingsPath}\n";
    echo "   ✅ File size: " . File::size($settingsPath) . " bytes\n";
    echo "   ✅ Hero title in file: '" . ($settings['homepage']['hero_title'] ?? 'NOT FOUND') . "'\n";
    echo "   ✅ Hero subtitle in file: '" . ($settings['homepage']['hero_subtitle'] ?? 'NOT FOUND') . "'\n\n";
} else {
    echo "   ❌ Settings file not found!\n\n";
    exit;
}

echo "2. 🎯 SIMULATING HOMEPAGE CONTROLLER:\n";
try {
    // Simulate HomepageController::welcome() logic
    if (File::exists($settingsPath)) {
        $uiSettings = json_decode(File::get($settingsPath), true);
        echo "   ✅ Settings loaded by controller simulation\n";
        echo "   ✅ Homepage section exists: " . (isset($uiSettings['homepage']) ? 'YES' : 'NO') . "\n";
        
        if (isset($uiSettings['homepage'])) {
            echo "   ✅ Hero title from controller: '" . ($uiSettings['homepage']['hero_title'] ?? 'DEFAULT') . "'\n";
            echo "   ✅ Hero subtitle from controller: '" . ($uiSettings['homepage']['hero_subtitle'] ?? 'DEFAULT') . "'\n";
        }
    } else {
        echo "   ❌ Controller would use default settings\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error in controller simulation: " . $e->getMessage() . "\n";
}

echo "\n3. 🌐 CHECKING VIEW LOGIC:\n";
// Simulate what the view template would output
$uiSettings = $settings; // Use the loaded settings

$heroTitle = $uiSettings['homepage']['hero_title'] ?? 'Transform Education with SmartPrep';
$heroSubtitle = $uiSettings['homepage']['hero_subtitle'] ?? 'Empower your educational institution with our cutting-edge multi-tenant learning management platform.';

echo "   📝 What view should display:\n";
echo "   📝 Hero Title: '{$heroTitle}'\n";
echo "   📝 Hero Subtitle: '{$heroSubtitle}'\n";

echo "\n4. 🔎 CHECKING FOR CACHING ISSUES:\n";

// Check if there are any view cache files
$viewCachePath = storage_path('framework/views');
if (is_dir($viewCachePath)) {
    $viewFiles = glob($viewCachePath . '/*.php');
    echo "   📁 View cache directory: {$viewCachePath}\n";
    echo "   📁 Cached view files: " . count($viewFiles) . "\n";
    
    // Look for SmartPrep homepage view cache
    $smartprepViews = array_filter($viewFiles, function($file) {
        return strpos($file, 'smartprep') !== false || strpos($file, 'welcome') !== false;
    });
    
    if (!empty($smartprepViews)) {
        echo "   ⚠️  SmartPrep view cache files found:\n";
        foreach ($smartprepViews as $file) {
            echo "      - " . basename($file) . "\n";
        }
        echo "   💡 Try: php artisan view:clear\n";
    } else {
        echo "   ✅ No SmartPrep view cache files found\n";
    }
} else {
    echo "   ✅ No view cache directory\n";
}

echo "\n5. 🚨 POTENTIAL ISSUES:\n";

// Check if general and navbar are empty
if (empty($settings['general']) || empty($settings['navbar'])) {
    echo "   ⚠️  Empty general or navbar sections might cause issues\n";
    echo "   💡 General section: " . (empty($settings['general']) ? 'EMPTY' : 'OK') . "\n";
    echo "   💡 Navbar section: " . (empty($settings['navbar']) ? 'EMPTY' : 'OK') . "\n";
}

// Check if there are any PHP errors in view
if (isset($uiSettings['homepage']['hero_title']) && $uiSettings['homepage']['hero_title'] === 'edit') {
    echo "   ✅ Settings are correct - issue is likely caching or view loading\n";
} else {
    echo "   ❌ Settings don't match expected values\n";
}

echo "\n======================================\n";
echo "🎯 NEXT STEPS TO FIX:\n";
echo "1. Clear all caches: php artisan view:clear && php artisan cache:clear\n";
echo "2. Restart development server\n";
echo "3. Hard refresh browser (Ctrl+F5)\n";
echo "4. Check browser developer tools for any JavaScript errors\n";
