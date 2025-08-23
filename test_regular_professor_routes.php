<?php

// Test regular professor routes with preview mode
$testUrls = [
    'Regular Meetings (Preview)' => 'http://127.0.0.1:8000/professor/meetings?preview=true&website=15',
    'Regular Dashboard (Preview)' => 'http://127.0.0.1:8000/professor/dashboard?preview=true&website=15',
];

echo "Testing Regular Professor Routes with Preview Mode...\n";
echo "====================================================\n\n";

foreach ($testUrls as $page => $url) {
    echo "Testing {$page}... ";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Test Script'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "ERROR: {$error}\n";
    } elseif ($httpCode === 200) {
        echo "✓ OK (200)\n";
    } elseif ($httpCode === 302 || $httpCode === 301) {
        echo "⚠ REDIRECT ({$httpCode})\n";
    } else {
        echo "✗ FAILED ({$httpCode})\n";
    }
}

echo "\nTest completed!\n";
