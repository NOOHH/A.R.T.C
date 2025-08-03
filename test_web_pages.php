<?php
// Test the web pages to ensure no route errors
echo "=== Testing Web Pages ===\n";

$testUrls = [
    'http://127.0.0.1:8000/admin/dashboard' => 'Admin Dashboard',
    'http://127.0.0.1:8000/admin/students' => 'Students List',
    'http://127.0.0.1:8000/admin/modules' => 'Modules Page',
    'http://127.0.0.1:8000/admin/announcements' => 'Announcements Page',
    'http://127.0.0.1:8000/admin/modules/archived' => 'Archived Modules'
];

foreach ($testUrls as $url => $description) {
    echo "\nTesting: $description\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "   ✗ CURL Error: $error\n";
    } elseif ($httpCode == 200) {
        echo "   ✓ Success (HTTP $httpCode)\n";
        
        // Check for specific errors in response
        if (strpos($response, 'Route [') !== false && strpos($response, 'not defined') !== false) {
            echo "   ⚠ Contains route error in response\n";
        } elseif (strpos($response, 'admin-sidebar.js') !== false) {
            echo "   ✓ Sidebar JS included\n";
        }
        
        // Check if "Assign Course to Student" is still present
        if (strpos($response, 'Assign Course to Student') !== false) {
            echo "   ⚠ Still contains 'Assign Course to Student'\n";
        } else {
            echo "   ✓ 'Assign Course to Student' removed\n";
        }
    } else {
        echo "   ✗ HTTP Error: $httpCode\n";
    }
}

echo "\n=== Web Test Complete ===\n";
