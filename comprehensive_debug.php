<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== COMPREHENSIVE SMARTPREP DEBUGGING ===\n\n";

// Step 1: Clear any bad data
echo "1. Clearing any problematic data...\n";
try {
    \App\Models\UiSetting::where('section', 'homepage')
                         ->where('setting_key', 'hero_title')
                         ->delete();
    echo "   ✓ Cleared existing hero_title\n";
} catch(Exception $e) {
    echo "   ✗ Error clearing: " . $e->getMessage() . "\n";
}

// Step 2: Test the controller method directly with proper data
echo "\n2. Testing controller method with proper data...\n";

// Create a proper HTTP request with form data
$request = \Illuminate\Http\Request::create('/smartprep/admin/settings/homepage', 'POST', [
    'hero_title' => 'Testing Hero Title - This Should Be Full Length',
    'hero_subtitle' => 'Testing Hero Subtitle - This Should Also Be Full Length',
    'cta_primary_text' => 'Get Started',
    'cta_primary_link' => '/programs',
    'cta_secondary_text' => 'Learn More',
    'cta_secondary_link' => '/about',
    'features_title' => 'Why Choose Us?',
    'copyright' => '© Copyright Test.'
]);

$request->headers->set('Accept', 'application/json');
$request->headers->set('Content-Type', 'application/x-www-form-urlencoded');

echo "   Request data:\n";
foreach($request->all() as $key => $value) {
    echo "      {$key} = '{$value}' (length: " . strlen($value) . ")\n";
}

try {
    // Instantiate controller and call method
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    $response = $controller->updateHomepage($request);
    
    echo "   ✓ Controller method executed successfully\n";
    echo "   Response type: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        echo "   JSON Response: " . $response->getContent() . "\n";
    } else {
        echo "   Response status: " . $response->getStatusCode() . "\n";
    }
    
} catch(Exception $e) {
    echo "   ✗ Controller error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

// Step 3: Check what was actually saved
echo "\n3. Checking what was saved to database...\n";
$heroTitle = \App\Models\UiSetting::get('homepage', 'hero_title', 'NOT_FOUND');
$heroSubtitle = \App\Models\UiSetting::get('homepage', 'hero_subtitle', 'NOT_FOUND');

echo "   hero_title = '{$heroTitle}' (length: " . strlen($heroTitle) . ")\n";
echo "   hero_subtitle = '{$heroSubtitle}' (length: " . strlen($heroSubtitle) . ")\n";

// Step 4: Check SettingsHelper output
echo "\n4. Checking SettingsHelper output...\n";
$homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
echo "   SettingsHelper hero_title = '{$homepageContent['hero_title']}' (length: " . strlen($homepageContent['hero_title']) . ")\n";

// Step 5: Test with edge cases
echo "\n5. Testing edge cases...\n";

// Test with empty string
$emptyRequest = \Illuminate\Http\Request::create('/test', 'POST', [
    'hero_title' => '',
    'hero_subtitle' => 'Test subtitle'
]);
$emptyRequest->headers->set('Accept', 'application/json');

try {
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    $response = $controller->updateHomepage($emptyRequest);
    echo "   ✓ Empty string test passed\n";
    
    $emptyResult = \App\Models\UiSetting::get('homepage', 'hero_title', 'NOT_FOUND');
    echo "   Empty result: '{$emptyResult}' (length: " . strlen($emptyResult) . ")\n";
    
} catch(Exception $e) {
    echo "   ✗ Empty string test failed: " . $e->getMessage() . "\n";
}

// Test with single character
$singleCharRequest = \Illuminate\Http\Request::create('/test', 'POST', [
    'hero_title' => 'X',
    'hero_subtitle' => 'Test subtitle'
]);
$singleCharRequest->headers->set('Accept', 'application/json');

try {
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    $response = $controller->updateHomepage($singleCharRequest);
    echo "   ✓ Single character test passed\n";
    
    $singleResult = \App\Models\UiSetting::get('homepage', 'hero_title', 'NOT_FOUND');
    echo "   Single char result: '{$singleResult}' (length: " . strlen($singleResult) . ")\n";
    
} catch(Exception $e) {
    echo "   ✗ Single character test failed: " . $e->getMessage() . "\n";
}

echo "\n=== END COMPREHENSIVE DEBUG ===\n";
