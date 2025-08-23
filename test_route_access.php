<?php

echo "Testing Admin Announcements Route Access\n";
echo "=======================================\n\n";

$testUrl = "http://localhost/t/draft/test1/admin/announcements?website=15&preview=true&t=" . time();

echo "Testing URL: $testUrl\n\n";

// Test if the route exists in Laravel
try {
    // Create a simple GET request to test
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: PHP Test Script',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
            ],
            'timeout' => 10
        ]
    ]);
    
    echo "Attempting to fetch URL...\n";
    $response = @file_get_contents($testUrl, false, $context);
    
    if ($response === false) {
        echo "❌ Failed to fetch URL\n";
        
        // Check the HTTP response code
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (strpos($header, 'HTTP/') === 0) {
                    echo "HTTP Response: $header\n";
                }
            }
        }
    } else {
        echo "✅ URL is accessible\n";
        echo "Response length: " . strlen($response) . " bytes\n";
        
        // Check if it contains expected admin content
        if (strpos($response, 'admin') !== false || strpos($response, 'Admin') !== false) {
            echo "✅ Response contains admin content\n";
        } else {
            echo "⚠️ Response may not be admin page\n";
        }
        
        // Check for error messages
        if (strpos($response, 'Not Found') !== false || strpos($response, '404') !== false) {
            echo "❌ Response contains 'Not Found' error\n";
        }
        
        if (strpos($response, 'Test1') !== false) {
            echo "✅ Response contains custom branding 'Test1'\n";
        } else {
            echo "⚠️ Custom branding 'Test1' not found in response\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n";
echo "Alternative test URLs:\n";
echo "- Dashboard: http://localhost/t/draft/test1/admin-dashboard?website=15&preview=true&t=" . time() . "\n";
echo "- Laravel serve: http://localhost:8000/t/draft/test1/admin/announcements?website=15&preview=true&t=" . time() . "\n";
