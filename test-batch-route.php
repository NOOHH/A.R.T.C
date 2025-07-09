<?php

// Test accessing the batch enrollment route
require_once 'vendor/autoload.php';

// Start the Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Start PHP session and simulate admin login
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'admin';
$_SESSION['user_name'] = 'Test Admin';
$_SESSION['user_email'] = 'admin@test.com';
$_SESSION['logged_in'] = true;

echo "Simulating access to /admin/batches route...\n";

// Create a fake request to the batch enrollment route
$request = Illuminate\Http\Request::create('/admin/batches', 'GET');

try {
    $response = $kernel->handle($request);
    echo "Status Code: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() == 200) {
        echo "SUCCESS: Route accessible!\n";
        echo "Content length: " . strlen($response->getContent()) . " bytes\n";
    } elseif ($response->getStatusCode() == 302) {
        echo "REDIRECT: Location: " . $response->headers->get('Location') . "\n";
    } else {
        echo "UNEXPECTED STATUS: " . $response->getStatusCode() . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\nTest completed.\n";
