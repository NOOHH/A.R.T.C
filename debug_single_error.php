<?php
// Simple test to get actual error content
$url = 'http://127.0.0.1:8000/t/draft/test1/admin/students?website=15&preview=true&t=' . time();

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

echo "Testing URL: $url\n\n";

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "Failed to get response\n";
} else {
    // Extract error info
    if (strpos($response, 'Exception') !== false || strpos($response, 'Error') !== false) {
        echo "=== ERROR CONTENT ===\n";
        
        // Look for specific error patterns
        if (preg_match('/("exception"[^}]+}[^}]+})/', $response, $matches)) {
            $errorData = json_decode($matches[1], true);
            if ($errorData) {
                echo "Error: " . ($errorData['message'] ?? 'Unknown') . "\n";
                echo "File: " . ($errorData['file'] ?? 'Unknown') . "\n";
                echo "Line: " . ($errorData['line'] ?? 'Unknown') . "\n";
            }
        } else if (preg_match('/Exception.*?in (.*?) on line (\d+)/', $response, $matches)) {
            echo "File: " . $matches[1] . "\n";
            echo "Line: " . $matches[2] . "\n";
        }
        
        // Show first few lines that contain error keywords
        $lines = explode("\n", $response);
        foreach ($lines as $i => $line) {
            if (stripos($line, 'exception') !== false || stripos($line, 'error') !== false) {
                echo "Line $i: " . trim($line) . "\n";
                if ($i < count($lines) - 1) {
                    echo "Next: " . trim($lines[$i + 1]) . "\n";
                }
                break;
            }
        }
    } else {
        echo "No obvious errors found, status likely 200\n";
        echo "Content length: " . strlen($response) . " characters\n";
    }
}
