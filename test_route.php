<?php

// Simple test to check if the route works
$url = 'http://localhost/A.R.T.C/admin/packages';

// Create a context with necessary headers
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ],
        'timeout' => 30
    ]
]);

echo "Testing URL: $url\n";

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Could not connect to URL\n";
    $error = error_get_last();
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "SUCCESS: Got response from admin packages page\n";
    echo "Response length: " . strlen($response) . " characters\n";
    
    // Check if it contains expected content
    if (strpos($response, 'packages') !== false) {
        echo "✓ Response contains 'packages' text\n";
    }
    
    if (strpos($response, 'error') !== false || strpos($response, 'Exception') !== false) {
        echo "⚠ Response may contain errors\n";
        echo "First 500 chars: " . substr($response, 0, 500) . "\n";
    }
}
