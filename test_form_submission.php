<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== TESTING SMARTPREP FORM SUBMISSION ===\n\n";

// Simulate a homepage form submission
echo "1. Simulating homepage form submission:\n";

// Create a mock request
$request = new \Illuminate\Http\Request();
$request->replace([
    'hero_title' => 'Test Hero Title from Form Submission',
    'hero_subtitle' => 'Test Hero Subtitle from Form Submission',
    'cta_primary_text' => 'Test CTA Primary',
    'cta_primary_link' => '/test-programs',
    'cta_secondary_text' => 'Test CTA Secondary',
    'cta_secondary_link' => '/test-about',
    'features_title' => 'Test Features Title',
    'copyright' => 'Test Copyright Text'
]);

// Set method and headers
$request->setMethod('POST');
$request->headers->set('X-CSRF-TOKEN', 'test-token'); // We'll skip CSRF for testing

echo "   Request data:\n";
foreach($request->all() as $key => $value) {
    echo "      {$key} = {$value}\n";
}

try {
    // Create controller instance
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    
    // Call the updateHomepage method
    echo "\n2. Calling updateHomepage method:\n";
    $response = $controller->updateHomepage($request);
    
    echo "   ✓ Method executed successfully\n";
    echo "   Response status: " . ($response->getStatusCode() ?? 'N/A') . "\n";
    
    // Check if settings were saved
    echo "\n3. Checking saved settings:\n";
    $heroTitle = \App\Models\UiSetting::get('homepage', 'hero_title', 'not found');
    $heroSubtitle = \App\Models\UiSetting::get('homepage', 'hero_subtitle', 'not found');
    
    echo "   hero_title = {$heroTitle}\n";
    echo "   hero_subtitle = {$heroSubtitle}\n";
    
} catch(Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

// Test validation
echo "\n4. Testing validation with empty data:\n";
$emptyRequest = new \Illuminate\Http\Request();
$emptyRequest->setMethod('POST');

try {
    $controller = new \App\Http\Controllers\Smartprep\Admin\AdminSettingsController();
    $response = $controller->updateHomepage($emptyRequest);
    echo "   ✓ Empty request handled successfully\n";
} catch(\Illuminate\Validation\ValidationException $e) {
    echo "   ✓ Validation works - caught ValidationException\n";
    echo "   Validation errors: " . json_encode($e->errors()) . "\n";
} catch(Exception $e) {
    echo "   ✗ Unexpected error: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
