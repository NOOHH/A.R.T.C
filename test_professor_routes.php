<?php

// Test script to verify professor routes are working in preview mode

$routes = [
    'Dashboard' => 'http://127.0.0.1:8000/t/draft/test1/professor/dashboard?preview=true&website=15',
    'Programs' => 'http://127.0.0.1:8000/t/draft/test1/professor/programs?preview=true&website=15',
    'Modules' => 'http://127.0.0.1:8000/t/draft/test1/professor/modules?preview=true&website=15',
    'Meetings' => 'http://127.0.0.1:8000/t/draft/test1/professor/meetings?preview=true&website=15',
    'Grading' => 'http://127.0.0.1:8000/t/draft/test1/professor/grading?preview=true&website=15',
    'Announcements' => 'http://127.0.0.1:8000/t/draft/test1/professor/announcements?preview=true&website=15',
    'Students' => 'http://127.0.0.1:8000/t/draft/test1/professor/students?preview=true&website=15',
    'Profile' => 'http://127.0.0.1:8000/t/draft/test1/professor/profile?preview=true&website=15',
    'Settings' => 'http://127.0.0.1:8000/t/draft/test1/professor/settings?preview=true&website=15'
];

echo "Testing Professor Routes in Preview Mode:\n";
echo "=======================================\n\n";

foreach ($routes as $name => $url) {
    echo "Testing {$name}: ";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ SUCCESS (HTTP 200)\n";
    } else {
        echo "❌ FAILED (HTTP {$httpCode})\n";
    }
}

echo "\n=== Test Complete ===\n";

?>
