<?php

// Simple test script to verify professor preview pages are working
$testUrls = [
    'Dashboard' => 'http://127.0.0.1:8000/t/draft/test1/professor/dashboard?website=15&preview=true',
    'Meetings' => 'http://127.0.0.1:8000/t/draft/test1/professor/meetings?website=15&preview=true',
    'Announcements' => 'http://127.0.0.1:8000/t/draft/test1/professor/announcements?website=15&preview=true',
    'Grading' => 'http://127.0.0.1:8000/t/draft/test1/professor/grading?website=15&preview=true',
    'Modules' => 'http://127.0.0.1:8000/t/draft/test1/professor/modules?website=15&preview=true',
    'Programs' => 'http://127.0.0.1:8000/t/draft/test1/professor/programs?website=15&preview=true',
    'Students' => 'http://127.0.0.1:8000/t/draft/test1/professor/students?website=15&preview=true',
    'Profile' => 'http://127.0.0.1:8000/t/draft/test1/professor/profile?website=15&preview=true',
    'Settings' => 'http://127.0.0.1:8000/t/draft/test1/professor/settings?website=15&preview=true',
];

echo "Testing Professor Preview Pages...\n";
echo "==================================\n\n";

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
