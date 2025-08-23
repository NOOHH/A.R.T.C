<?php

echo "=== TENANT PREVIEW DIRECT TEST ===\n\n";

$testUrls = [
    'Dashboard' => 'http://127.0.0.1:8000/t/draft/test1/student/dashboard?website=15&preview=true',
    'Calendar' => 'http://127.0.0.1:8000/t/draft/test1/student/calendar?website=15&preview=true',
    'Courses' => 'http://127.0.0.1:8000/t/draft/test1/student/enrolled-courses?website=15&preview=true',
    'Meetings' => 'http://127.0.0.1:8000/t/draft/test1/student/meetings?website=15&preview=true',
    'Settings' => 'http://127.0.0.1:8000/t/draft/test1/student/settings?website=15&preview=true',
];

foreach ($testUrls as $name => $url) {
    echo "Testing $name: $url\n";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "  ❌ Failed to connect\n";
    } else {
        $httpCode = 200; // Default
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = (int)$matches[1];
                    break;
                }
            }
        }
        
        echo "  📊 HTTP Status: $httpCode\n";
        echo "  📄 Content Length: " . strlen($response) . " bytes\n";
        
        // Check for common error indicators
        if (strpos($response, 'Error') !== false || strpos($response, 'Exception') !== false) {
            echo "  ⚠️ Possible error detected in content\n";
            
            // Try to extract error message
            if (preg_match('/<title>([^<]+)<\/title>/i', $response, $matches)) {
                echo "  Title: " . $matches[1] . "\n";
            }
        } else {
            echo "  ✅ Response looks healthy\n";
        }
        
        // Check if it looks like actual page content
        if (strpos($response, '<html') !== false && strpos($response, 'student') !== false) {
            echo "  📱 Appears to be a student page\n";
        }
    }
    
    echo "\n";
}

echo "=== TEST COMPLETE ===\n";
