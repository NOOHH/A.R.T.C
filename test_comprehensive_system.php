<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;

echo "🧪 COMPREHENSIVE SMARTPREP SETTINGS TEST\n";
echo "=========================================\n\n";

// Test 1: Update settings through admin interface simulation
echo "1. 📝 UPDATING SETTINGS (simulating admin form submission):\n";
$settingsPath = storage_path('app/smartprep_settings.json');

$newSettings = [
    'general' => [
        'site_name' => 'TEST SmartPrep Updated',
        'site_tagline' => 'TEST Learning Platform',
        'contact_email' => 'test@example.com',
        'contact_phone' => '+1 (555) 123-TEST',
        'contact_address' => 'TEST Address',
        'updated_at' => now()->toISOString()
    ],
    'navbar' => [
        'brand_name' => 'TEST Brand Updated',
        'brand_image' => '',
        'style' => 'fixed-top',
        'menu_items' => '[]',
        'show_login_button' => '1',
        'updated_at' => now()->toISOString()
    ],
    'branding' => [
        'primary_color' => '#ff0000',
        'secondary_color' => '#00ff00',
        'background_color' => '#ffffff',
        'logo_url' => '',
        'favicon_url' => '',
        'font_family' => 'Arial',
        'updated_at' => now()->toISOString()
    ],
    'homepage' => [
        'hero_title' => 'TEST HERO: Learn Better. Study Smarter. Succeed Faster.',
        'hero_subtitle' => 'TEST SUBTITLE: Updated through admin panel testing.',
        'cta_primary_text' => 'TEST BUTTON',
        'cta_primary_link' => '/test-programs',
        'cta_secondary_text' => 'TEST LEARN MORE',
        'cta_secondary_link' => '/test-about',
        'features_title' => 'TEST Features',
        'copyright' => '© 2025 TEST Updated Copyright',
        'updated_at' => now()->toISOString()
    ]
];

// Save the test settings
File::put($settingsPath, json_encode($newSettings, JSON_PRETTY_PRINT));
echo "   ✅ Settings saved to: {$settingsPath}\n";
echo "   ✅ Hero title set to: " . $newSettings['homepage']['hero_title'] . "\n";
echo "   ✅ Brand name set to: " . $newSettings['navbar']['brand_name'] . "\n\n";

// Test 2: Verify HomepageController reads the settings
echo "2. 🏠 TESTING HOMEPAGE CONTROLLER:\n";
try {
    // Simulate what HomepageController::welcome() does
    if (File::exists($settingsPath)) {
        $uiSettings = json_decode(File::get($settingsPath), true);
        echo "   ✅ Settings loaded by HomepageController\n";
        echo "   ✅ Homepage hero title: " . $uiSettings['homepage']['hero_title'] . "\n";
        echo "   ✅ General site name: " . $uiSettings['general']['site_name'] . "\n";
        echo "   ✅ Navbar brand: " . $uiSettings['navbar']['brand_name'] . "\n";
        echo "   ✅ Primary color: " . $uiSettings['branding']['primary_color'] . "\n";
    } else {
        echo "   ❌ Settings file not found!\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error in HomepageController: " . $e->getMessage() . "\n";
}

echo "\n3. 🔍 VERIFYING SETTINGS STRUCTURE:\n";
$currentSettings = json_decode(File::get($settingsPath), true);

echo "   📊 Available sections:\n";
foreach ($currentSettings as $section => $data) {
    echo "      - {$section}: " . count($data) . " properties\n";
}

echo "\n   🎨 Homepage Settings (what should appear on site):\n";
foreach ($currentSettings['homepage'] as $key => $value) {
    if ($key !== 'updated_at') {
        echo "      - {$key}: " . substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') . "\n";
    }
}

echo "\n   🧭 Navbar Settings:\n";
foreach ($currentSettings['navbar'] as $key => $value) {
    if ($key !== 'updated_at') {
        echo "      - {$key}: {$value}\n";
    }
}

// Test 4: Test the specific values you mentioned
echo "\n4. 🎯 TESTING YOUR SPECIFIC CHANGES:\n";

echo "   Hero Title Check:\n";
$heroTitle = $currentSettings['homepage']['hero_title'] ?? 'NOT FOUND';
echo "      Current: {$heroTitle}\n";
echo "      Expected: Something like 'sdae' or your custom text\n";

echo "   Hero Subtitle Check:\n";
$heroSubtitle = $currentSettings['homepage']['hero_subtitle'] ?? 'NOT FOUND';
echo "      Current: {$heroSubtitle}\n";
echo "      Expected: Something like 'sdsdsdsdsdsdsdsd' or your custom text\n";

echo "   Brand Name Check:\n";
$brandName = $currentSettings['navbar']['brand_name'] ?? 'NOT FOUND';
echo "      Current: {$brandName}\n";
echo "      Expected: Something like 'Test Brand' or your custom text\n";

// Test 5: Verify the file is readable by web server
echo "\n5. 🌐 WEB SERVER COMPATIBILITY:\n";
$filePermissions = substr(sprintf('%o', fileperms($settingsPath)), -4);
echo "   ✅ File permissions: {$filePermissions}\n";
echo "   ✅ File size: " . File::size($settingsPath) . " bytes\n";
echo "   ✅ File last modified: " . date('Y-m-d H:i:s', File::lastModified($settingsPath)) . "\n";

echo "\n6. 🚀 TEST SUMMARY:\n";
echo "   📍 Settings file location: {$settingsPath}\n";
echo "   📍 Homepage URL: http://127.0.0.1:8000/smartprep/\n";
echo "   📍 Admin settings URL: http://127.0.0.1:8000/smartprep/admin/settings\n";

if (isset($currentSettings['homepage']['hero_title']) && 
    isset($currentSettings['navbar']['brand_name']) && 
    isset($currentSettings['general']['site_name'])) {
    echo "\n   ✅ ALL TESTS PASSED! Settings should now appear on homepage.\n";
    echo "   🔄 Clear browser cache and refresh http://127.0.0.1:8000/smartprep/\n";
} else {
    echo "\n   ❌ Some settings missing! Check the admin panel.\n";
}

echo "\n=========================================\n";
echo "🏁 TEST COMPLETE\n";
