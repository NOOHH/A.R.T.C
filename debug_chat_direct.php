<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set up a fake session to simulate user 179
session(['user_id' => 179, 'user_name' => 'Vince Michael Dela Vega', 'role' => 'student']);

try {
    echo "Testing chat API directly...\n";
    
    // Create the request data
    $requestData = [
        'receiver_id' => 8,
        'message' => 'Test message from API debug'
    ];
    
    echo "Request data: " . json_encode($requestData) . "\n";
    
    // Create a fake request
    $request = new \Illuminate\Http\Request();
    $request->merge($requestData);
    $request->headers->set('Content-Type', 'application/json');
    
    // Create controller instance
    $controller = new \App\Http\Controllers\ChatController();
    
    echo "Calling sendSessionMessage...\n";
    
    // Call the method
    $response = $controller->sendSessionMessage($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
