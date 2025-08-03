<?php
// Test authentication status for API endpoint
require_once(__DIR__ . '/vendor/autoload.php');

// Start session to check current authentication
session_start();

echo "=== Session Authentication Test ===\n";
echo "PHP Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";
echo "PHP Session Data:\n";
var_dump($_SESSION);

echo "\n=== Laravel Application Test ===\n";

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to simulate the API call
$request = Illuminate\Http\Request::create('/admin/registration/4/details', 'GET');

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() === 200) {
        echo "Response Content (first 200 chars): " . substr($response->getContent(), 0, 200) . "\n";
    } else {
        echo "Response Content: " . $response->getContent() . "\n";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

$kernel->terminate($request, $response);
