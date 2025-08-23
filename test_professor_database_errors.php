<?php

function testRoute($route, $name) {
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
        if (strpos($response, "Table 'smartprep.professors' doesn't exist") !== false) {
            echo "   → PROFESSORS TABLE NOT FOUND ERROR ⚠️\n";
            return false;
        }
        if (strpos($response, 'SQLSTATE[42S02]') !== false) {
            echo "   → SQL TABLE NOT FOUND ERROR\n";
            return false;
        }
    } else {
        echo "✅ SUCCESS (HTTP $httpCode)\n";
        return true;
    }
    
    curl_close($ch);
    return true;
}

echo "Detailed Testing of Professor Routes for Database Errors:\n";
echo "========================================================\n\n";

$routes = [
    'professor/dashboard' => 'Professor Dashboard',
    'professor/grading' => 'Professor Grading', 
    'professor/students' => 'Professor Students',
    'professor/announcements' => 'Professor Announcements',
    'professor/meetings' => 'Professor Meetings',
    'professor/modules' => 'Professor Modules',
    'professor/profile' => 'Professor Profile',
    'professor/settings' => 'Professor Settings',
    'professor/programs' => 'Professor Programs'
];

$failures = [];

foreach ($routes as $route => $name) {
    if (!testRoute($route, $name)) {
        $failures[] = $route;
    }
    echo "\n";
}

if (!empty($failures)) {
    echo "Routes with professors table errors:\n";
    foreach ($failures as $route) {
        echo "  - $route\n";
    }
} else {
    echo "All routes are working - no professors table errors found!\n";
}

echo "\n=== Test Complete ===\n";
