<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Testing Routes After Fix ===\n\n";

// Test the tenant programs route
echo "Testing tenant.draft.admin.programs route:\n";
$route = Route::getRoutes()->getByName('tenant.draft.admin.programs');
if ($route) {
    echo "✅ Route found: " . $route->uri() . "\n";
    $action = $route->getAction();
    echo "   Controller: " . $action['controller'] . "\n";
    echo "   Method: " . $route->getActionMethod() . "\n";
} else {
    echo "❌ Route not found\n";
}

echo "\n";

// Test the tenant packages route
echo "Testing tenant.draft.admin.packages route:\n";
$route = Route::getRoutes()->getByName('tenant.draft.admin.packages');
if ($route) {
    echo "✅ Route found: " . $route->uri() . "\n";
    $action = $route->getAction();
    echo "   Controller: " . $action['controller'] . "\n";
    echo "   Method: " . $route->getActionMethod() . "\n";
} else {
    echo "❌ Route not found\n";
}

echo "\n";

// Test HTTP requests
echo "=== Testing HTTP Requests ===\n\n";

// Test tenant programs page
$url = 'http://127.0.0.1:8000/draft/test2/admin/programs';
echo "Testing URL: $url\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 5,
        'ignore_errors' => true
    ]
]);

$response = file_get_contents($url, false, $context);
$httpCode = $http_response_header[0] ?? 'Unknown';

echo "HTTP Response: $httpCode\n";
if (strpos($httpCode, '200') !== false) {
    echo "✅ Success - Page loads correctly\n";
} elseif (strpos($httpCode, '500') !== false) {
    echo "❌ Server Error - Still has issues\n";
} else {
    echo "⚠️  Other response: $httpCode\n";
}

echo "\n";

// Test tenant packages page
$url = 'http://127.0.0.1:8000/draft/test2/admin/packages';
echo "Testing URL: $url\n";

$response = file_get_contents($url, false, $context);
$httpCode = $http_response_header[0] ?? 'Unknown';

echo "HTTP Response: $httpCode\n";
if (strpos($httpCode, '200') !== false) {
    echo "✅ Success - Page loads correctly\n";
} elseif (strpos($httpCode, '500') !== false) {
    echo "❌ Server Error - Still has issues\n";
} else {
    echo "⚠️  Other response: $httpCode\n";
}

echo "\n=== Test Complete ===\n";
