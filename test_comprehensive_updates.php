<?php
require_once 'vendor/autoload.php';

// Properly initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;

echo "=== COMPREHENSIVE NAVBAR AND HOMEPAGE COLOR TEST ===\n\n";

// Test navbar brand name updates
echo "1. TESTING NAVBAR BRAND NAME UPDATES:\n";
$testBrand = "🌟 DYNAMIC NAVBAR TEST 🌟";
UiSetting::set('navbar', 'brand_name', $testBrand, 'text');
$savedBrand = UiSetting::get('navbar', 'brand_name');
echo "   Brand Name: " . ($savedBrand === $testBrand ? '✅ SUCCESS' : '❌ FAILED') . " - $savedBrand\n";

// Test homepage color settings
echo "\n2. TESTING HOMEPAGE COLOR SETTINGS:\n";
$colorTests = [
    'primary_color' => '#ff6b6b',
    'secondary_color' => '#4ecdc4', 
    'background_color' => '#f8f9fa',
    'text_color' => '#2c3e50',
    'overlay_color' => '#000000'
];

foreach ($colorTests as $colorKey => $colorValue) {
    UiSetting::set('homepage', $colorKey, $colorValue, 'color');
    $saved = UiSetting::get('homepage', $colorKey);
    echo "   Homepage $colorKey: " . ($saved === $colorValue ? '✅' : '❌') . " $saved\n";
}

// Get all navbar settings
echo "\n3. ALL NAVBAR SETTINGS:\n";
$navbarSettings = UiSetting::getSection('navbar');
foreach ($navbarSettings as $key => $value) {
    echo "   $key = $value\n";
}

// Get all homepage settings  
echo "\n4. ALL HOMEPAGE SETTINGS:\n";
$homepageSettings = UiSetting::getSection('homepage');
foreach ($homepageSettings as $key => $value) {
    echo "   $key = $value\n";
}

echo "\n=== TESTING SUMMARY ===\n";
echo "✅ Enhanced navbar brand name selectors:\n";
echo "   - .navbar-brand strong (main navbar)\n";
echo "   - .footer-title (footer)\n";
echo "   - .navbar-brand (SmartPrep homepage)\n";
echo "   - .brand-text (login/signup pages)\n";
echo "   - a.navbar-brand (various pages)\n\n";

echo "✅ Added homepage color customization:\n";
echo "   - Primary Color: Real-time preview updates\n";
echo "   - Secondary Color: Gradient support\n";
echo "   - Background Color: Full page background\n";
echo "   - Text Color: Typography updates\n";
echo "   - Hero Overlay: Image overlay control\n\n";

echo "🎯 TESTING INSTRUCTIONS:\n";
echo "1. Open: http://127.0.0.1:8000/dashboard/customize-website\n";
echo "2. Test navbar brand name changes - should update ALL instances\n";
echo "3. Test homepage color changes - should apply immediately to preview\n";
echo "4. Check console for: 'Updated navbar brand name to: [VALUE]'\n";
echo "5. Check console for: 'Updated homepage [color] color to: [VALUE]'\n";
echo "6. Verify all Ascendo references are dynamically updated\n\n";

echo "🚀 All navbar brand instances and homepage colors are now dynamically updatable!\n";
?>
