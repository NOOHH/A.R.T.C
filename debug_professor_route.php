<?php

echo "=== DEBUGGING SPECIFIC PROFESSOR ROUTE ===\n\n";

$url = 'http://127.0.0.1:8000/t/draft/test1/professor/meetings?website=15&preview=true';

echo "Testing professor meetings route for detailed error analysis...\n";
echo "URL: {$url}\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "❌ cURL Error: " . curl_error($ch) . "\n";
    exit;
}

echo "📊 HTTP Status: {$httpCode}\n";
echo "📄 Content Length: " . strlen($response) . " bytes\n\n";

if ($httpCode === 500) {
    echo "🔍 Searching for error details in response...\n\n";
    
    // Look for Laravel error patterns
    if (preg_match('/class="exception_title"[^>]*>([^<]+)/i', $response, $matches)) {
        echo "❌ Exception Title: " . trim($matches[1]) . "\n";
    }
    
    if (preg_match('/class="exception_message"[^>]*>([^<]+)/i', $response, $matches)) {
        echo "❌ Exception Message: " . trim($matches[1]) . "\n";
    }
    
    // Look for stack trace hints
    if (preg_match('/in ([^:]+):(\d+)/i', $response, $matches)) {
        echo "📁 File: " . trim($matches[1]) . "\n";
        echo "📍 Line: " . trim($matches[2]) . "\n";
    }
    
    // Look for simple error messages
    $errorPatterns = [
        'Call to undefined method',
        'Class \'[^\']+\' not found',
        'Undefined method',
        'Undefined variable',
        'Undefined array key',
        'View \[[^\]]+\] not found',
        'Route \[[^\]]+\] not defined',
        'SQLSTATE',
        'Base table or view not found'
    ];
    
    foreach ($errorPatterns as $pattern) {
        if (preg_match('/' . preg_quote($pattern, '/') . '.*?(?=<|\n|$)/i', $response, $matches)) {
            echo "🔍 Found Error Pattern: " . trim($matches[0]) . "\n";
        }
    }
    
    // Extract any plain text error lines
    $lines = explode("\n", strip_tags($response));
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && (
            stripos($line, 'error') !== false || 
            stripos($line, 'exception') !== false ||
            stripos($line, 'undefined') !== false ||
            stripos($line, 'not found') !== false
        )) {
            echo "📝 Error Context: " . $line . "\n";
        }
    }
}

curl_close($ch);

echo "\n=== DEBUG ANALYSIS COMPLETE ===\n";
