<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a fake request for password reset
$request = Request::create('/password/email', 'POST', [
    'email' => 'vince03handsome11@gmail.com'
]);

// Set up the environment
$response = $kernel->handle($request);

try {
    // Get the controller
    $controller = new App\Http\Controllers\UnifiedLoginController();
    
    echo "Testing password reset email...\n";
    
    // Call the sendResetLinkEmail method directly
    $result = $controller->sendResetLinkEmail($request);
    
    echo "Result: " . $result . "\n";
    echo "Test completed.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

$kernel->terminate($request, $response);
