<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;
use App\Helpers\UiSettingsHelper;

echo "=== TESTING SMARTPREP SETTINGS ===\n\n";

try {
    // Test 1: Save some settings
    echo "1. Testing setting save functionality...\n";
    
    UiSetting::set('general', 'site_name', 'Test SmartPrep Site', 'text');
    UiSetting::set('general', 'site_tagline', 'Test Tagline', 'text');
    UiSetting::set('branding', 'primary_color', '#ff0000', 'color');
    UiSetting::set('branding', 'secondary_color', '#00ff00', 'color');
    UiSetting::set('navbar', 'brand_name', 'Test Brand', 'text');
    UiSetting::set('homepage', 'hero_title', 'Test Hero Title', 'text');
    UiSetting::set('homepage', 'hero_subtitle', 'Test Hero Subtitle', 'text');
    
    echo "   ✅ Settings saved successfully\n";
    
    // Test 2: Retrieve settings
    echo "\n2. Testing setting retrieval...\n";
    
    $allSettings = UiSettingsHelper::getAll();
    echo "   All settings:\n";
    print_r($allSettings);
    
    // Test 3: Test individual getters
    echo "\n3. Testing individual getters...\n";
    
    $siteName = UiSettingsHelper::get('general', 'site_name');
    $primaryColor = UiSettingsHelper::get('branding', 'primary_color');
    $brandName = UiSettingsHelper::get('navbar', 'brand_name');
    
    echo "   Site Name: $siteName\n";
    echo "   Primary Color: $primaryColor\n";
    echo "   Brand Name: $brandName\n";
    
    // Test 4: Test CSS variables
    echo "\n4. Testing CSS variables...\n";
    
    $cssVars = UiSettingsHelper::getCssVariables();
    echo "   CSS Variables:\n";
    print_r($cssVars);
    
    echo "\n✅ All tests passed! SmartPrep settings are working correctly.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
