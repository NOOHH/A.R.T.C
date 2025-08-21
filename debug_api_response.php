<?php

echo "=== DEBUGGING API RESPONSE ===\n\n";

try {
    echo "1. TESTING RAW API RESPONSE:\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/smartprep/api/ui-settings?website=10');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_STDERR, fopen('curl_debug.log', 'w'));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    echo "  HTTP Code: $httpCode\n";
    echo "  Content Type: $contentType\n";
    echo "  Response length: " . strlen($response) . " bytes\n";
    echo "  First 500 chars:\n";
    echo "  " . substr($response, 0, 500) . "\n\n";
    
    // Try to find JSON in the response
    $jsonStart = strpos($response, '{');
    if ($jsonStart !== false) {
        $jsonPart = substr($response, $jsonStart);
        echo "  JSON part (first 300 chars):\n";
        echo "  " . substr($jsonPart, 0, 300) . "\n\n";
        
        $data = json_decode($jsonPart, true);
        if ($data) {
            echo "  ✓ JSON parsed successfully!\n";
            
            if (isset($data['data']['navbar']['brand_name'])) {
                echo "  ✓ Brand name found: " . $data['data']['navbar']['brand_name'] . "\n";
            } else {
                echo "  ❌ Brand name not found in response\n";
                echo "  Available keys: " . implode(', ', array_keys($data['data'] ?? [])) . "\n";
            }
        } else {
            echo "  ❌ JSON parsing failed. Error: " . json_last_error_msg() . "\n";
        }
    } else {
        echo "  ❌ No JSON found in response\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check curl debug log
if (file_exists('curl_debug.log')) {
    echo "\nCURL Debug Info:\n";
    echo file_get_contents('curl_debug.log');
    unlink('curl_debug.log');
}

echo "\n=== DEBUG COMPLETE ===\n";
