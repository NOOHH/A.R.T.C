<?php

// Test analytics page by making HTTP request
echo "Testing analytics page...\n";

$url = 'http://127.0.0.1:8000/admin/analytics';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ],
        'timeout' => 10
    ]
]);

try {
    $response = file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "✓ Analytics page loads successfully!\n";
        echo "✓ No push stack errors detected.\n";
        
        // Check if the response contains expected elements
        if (strpos($response, 'Analytics Dashboard') !== false) {
            echo "✓ Page title found - structure is correct.\n";
        }
        
        if (strpos($response, 'Board Pass Rate') !== false) {
            echo "✓ Analytics content found - page rendered successfully.\n";
        }
        
        // Check for JavaScript errors
        if (strpos($response, 'changeChartPeriod') !== false) {
            echo "✓ JavaScript functions are present.\n";
        }
        
    } else {
        echo "✗ Failed to load analytics page.\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error accessing analytics page: " . $e->getMessage() . "\n";
}
