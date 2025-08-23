<?php

$routes = [
    'professor/grading' => 'Regular Professor Grading',
    'professor/students' => 'Regular Professor Students',
    'professor/announcements' => 'Regular Professor Announcements',
    'professor/meetings' => 'Regular Professor Meetings',
    'professor/modules' => 'Regular Professor Modules',
    'professor/profile' => 'Regular Professor Profile',
    'professor/settings' => 'Regular Professor Settings'
];

echo "Testing Regular Professor Routes for Database Errors:\n";
echo "====================================================\n\n";

foreach ($routes as $route => $name) {
    echo "Testing $name: http://127.0.0.1:8000/$route\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/$route");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode !== 200) {
        echo "❌ FAILED (HTTP $httpCode)\n";
        if (strpos($response, 'professors') !== false && strpos($response, 'not found') !== false) {
            echo "   → PROFESSORS TABLE NOT FOUND ERROR\n";
        }
        if (strpos($response, 'SQLSTATE[42S02]') !== false) {
            echo "   → SQL TABLE NOT FOUND ERROR\n";
        }
    } else {
        echo "✅ SUCCESS (HTTP $httpCode)\n";
    }
    
    curl_close($ch);
    echo "\n";
}

echo "=== Test Complete ===\n";
