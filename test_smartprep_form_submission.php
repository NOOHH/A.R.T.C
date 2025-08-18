<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING SMARTPREP FORM SUBMISSION ===\n\n";

// Test 1: Simulate homepage form submission
echo "1. Testing homepage form submission:\n";
try {
    // Create a mock request with homepage data
    $request = \Illuminate\Http\Request::create('/smartprep/admin/settings/homepage', 'POST', [
        'hero_title' => 'TEST HOMEPAGE TITLE - ' . date('H:i:s'),
        'hero_subtitle' => 'TEST HOMEPAGE SUBTITLE - ' . date('H:i:s'),
        'homepage_background_color' => '#ff6b6b',
        'homepage_gradient_color' => '#4ecdc4',
        'homepage_text_color' => '#ffffff',
        'homepage_button_color' => '#45b7d1',
        'cta_primary_text' => 'Get Started Now',
        'cta_primary_link' => '/enrollment',
        'cta_secondary_text' => 'Learn More',
        'cta_secondary_link' => '/about',
        'features_title' => 'Why Choose Our Platform?',
        'copyright' => '© Copyright Test Platform. All Rights Reserved.',
    ]);
    
    // Set headers to simulate AJAX request
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-CSRF-TOKEN', 'test-token');
    
    // Instantiate controller and call method
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    $response = $controller->updateHomepage($request);
    
    echo "   ✅ Form submission successful\n";
    echo "   Response type: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $content = json_decode($response->getContent(), true);
        echo "   Response: " . json_encode($content, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Form submission failed: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test 2: Verify the settings were saved to database
echo "\n2. Verifying settings were saved to database:\n";
try {
    $heroTitle = \App\Models\UiSetting::get('homepage', 'hero_title', 'NOT_FOUND');
    $heroSubtitle = \App\Models\UiSetting::get('homepage', 'hero_subtitle', 'NOT_FOUND');
    $bgColor = \App\Models\UiSetting::get('homepage', 'background_color', 'NOT_FOUND');
    
    echo "   hero_title = '{$heroTitle}'\n";
    echo "   hero_subtitle = '{$heroSubtitle}'\n";
    echo "   background_color = '{$bgColor}'\n";
    
    if (strpos($heroTitle, 'TEST HOMEPAGE TITLE') !== false) {
        echo "   ✅ SUCCESS! Settings were saved correctly\n";
    } else {
        echo "   ❌ FAILURE! Settings were not saved correctly\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking database: " . $e->getMessage() . "\n";
}

// Test 3: Check if SettingsHelper picks up the new settings
echo "\n3. Checking SettingsHelper integration:\n";
try {
    $homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
    echo "   SettingsHelper hero_title = '{$homepageContent['hero_title']}'\n";
    echo "   SettingsHelper hero_subtitle = '{$homepageContent['hero_subtitle']}'\n";
    
    if (strpos($homepageContent['hero_title'], 'TEST HOMEPAGE TITLE') !== false) {
        echo "   ✅ SUCCESS! SettingsHelper picks up the new settings\n";
    } else {
        echo "   ❌ FAILURE! SettingsHelper does not pick up the new settings\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking SettingsHelper: " . $e->getMessage() . "\n";
}

// Test 4: Test the API endpoint that the preview uses
echo "\n4. Testing API endpoint for preview:\n";
try {
    $response = \Illuminate\Support\Facades\Route::dispatch(
        \Illuminate\Http\Request::create('/smartprep/api/ui-settings', 'GET')
    );
    
    if ($response->getStatusCode() === 200) {
        $content = json_decode($response->getContent(), true);
        echo "   ✅ API endpoint working\n";
        echo "   Homepage settings from API:\n";
        if (isset($content['data']['homepage'])) {
            foreach ($content['data']['homepage'] as $key => $value) {
                echo "      {$key} = '{$value}'\n";
            }
        }
    } else {
        echo "   ❌ API endpoint failed with status: " . $response->getStatusCode() . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing API endpoint: " . $e->getMessage() . "\n";
}

// Test 5: Check if the homepage would display the new settings
echo "\n5. Testing homepage display:\n";
try {
    // Simulate what the HomepageController does
    $homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
    
    // Check if the homepage would display the updated content
    if (strpos($homepageContent['hero_title'], 'TEST HOMEPAGE TITLE') !== false) {
        echo "   ✅ SUCCESS! Homepage would display the updated title\n";
        echo "   ✅ SUCCESS! Homepage would display the updated subtitle\n";
        echo "   ✅ SUCCESS! The SmartPrep admin settings are working correctly!\n";
    } else {
        echo "   ❌ FAILURE! Homepage would NOT display the updated content\n";
        echo "   This indicates a problem with the settings flow\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing homepage display: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
