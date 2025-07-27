<?php

// Test search functionality without authentication
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\SearchController;

// Simulate a request
$request = new Request();
$request->merge(['query' => 'nursing']);

// Create controller instance
$controller = new SearchController();

try {
    echo "Testing search without authentication...\n";
    
    // This should simulate what happens when no user is logged in
    $response = $controller->search($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
