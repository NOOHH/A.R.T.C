<?php
// Simple test for students-list endpoint
echo "Testing students-list endpoint...\n";

try {
    // Test the endpoint by making an HTTP request
    $url = 'http://127.0.0.1:8000/admin/analytics/students-list';
    
    // Create context with session simulation
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Cookie: laravel_session=test_session_value'
            ],
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($data)) {
                echo "✓ Students-list endpoint returns valid JSON array\n";
                echo "✓ Number of students: " . count($data) . "\n";
                
                if (count($data) > 0) {
                    echo "✓ Sample student data: " . json_encode($data[0]) . "\n";
                }
            } else {
                echo "✗ Response is not an array: " . $response . "\n";
            }
        } else {
            echo "✗ Invalid JSON response: " . $response . "\n";
        }
    } else {
        echo "✗ Failed to fetch endpoint\n";
        $error = error_get_last();
        if ($error) {
            echo "Error: " . $error['message'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
