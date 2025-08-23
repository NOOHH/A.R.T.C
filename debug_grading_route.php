<?php

// Test the grading route specifically
$url = 'http://127.0.0.1:8000/t/draft/test1/professor/grading?preview=true';

$context = stream_context_create([
    'http' => [
        'timeout' => 30,
        'ignore_errors' => true
    ]
]);

echo "Testing Grading Route: $url\n";
echo "=====================================\n\n";

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ Request failed completely\n";
    exit(1);
}

// Get status code
$status = null;
if (isset($http_response_header)) {
    foreach ($http_response_header as $header) {
        if (strpos($header, 'HTTP/') === 0) {
            $status = (int) substr($header, 9, 3);
            break;
        }
    }
}

echo "Status Code: $status\n";
echo "Response Length: " . strlen($response) . " bytes\n\n";

if ($status === 500) {
    echo "=== ERROR RESPONSE (first 2000 chars) ===\n";
    echo substr($response, 0, 2000) . "\n";
    
    // Look for specific error patterns
    if (strpos($response, 'Class') !== false && strpos($response, 'not found') !== false) {
        echo "\n❌ CLASS NOT FOUND ERROR\n";
    }
    if (strpos($response, 'Method') !== false && strpos($response, 'does not exist') !== false) {
        echo "\n❌ METHOD NOT FOUND ERROR\n";
    }
    if (strpos($response, 'Undefined variable') !== false) {
        echo "\n❌ UNDEFINED VARIABLE ERROR\n";
    }
} else {
    echo "✅ SUCCESS\n";
}
