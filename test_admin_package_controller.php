<?php

require 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';

// Boot the application
$app->boot();

// Test the AdminPackageController directly
try {
    $controller = new \App\Http\Controllers\AdminPackageController();
    
    echo "Testing AdminPackageController->index()...\n";
    
    // Mock request
    $request = new \Illuminate\Http\Request();
    
    // Call the index method
    $response = $controller->index($request);
    
    echo "Response type: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\Http\Response) {
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Content Length: " . strlen($response->getContent()) . "\n";
        echo "SUCCESS: Controller method works!\n";
    } elseif ($response instanceof \Illuminate\View\View) {
        echo "View Name: " . $response->getName() . "\n";
        echo "SUCCESS: Controller returns view!\n";
        
        // Check if required variables are passed
        $data = $response->getData();
        echo "Variables passed to view:\n";
        foreach ($data as $key => $value) {
            if (is_object($value) || is_array($value)) {
                echo "  $key: " . gettype($value) . " (count: " . (is_countable($value) ? count($value) : 'N/A') . ")\n";
            } else {
                echo "  $key: $value\n";
            }
        }
    } else {
        echo "Unexpected response type\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
