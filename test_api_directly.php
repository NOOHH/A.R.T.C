<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing API Call Simulation ===\n";

try {
    // Simulate what happens when the API is called
    echo "1. Testing ReferralController::validateReferralCode() method directly:\n";
    
    $controller = new App\Http\Controllers\Api\ReferralController();
    
    // Create a mock request with the referral code
    $request = new Illuminate\Http\Request();
    $request->merge(['referral_code' => 'PROF08ROBERT']);
    
    // Test the validation method directly
    $response = $controller->validateReferralCode($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Checking CSRF and Middleware Issues ===\n";

// Check if there are middleware issues
echo "Checking route middleware:\n";
$routes = Route::getRoutes();
foreach ($routes as $route) {
    if (strpos($route->uri(), 'validate-referral-code') !== false) {
        echo "Route: " . $route->uri() . "\n";
        echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
        echo "Methods: " . implode(', ', $route->methods()) . "\n";
    }
}
