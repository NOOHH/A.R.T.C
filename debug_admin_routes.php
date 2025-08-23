<?php
// Debug script to test all admin routes
$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';
$params = '?website=15&preview=true&t=' . time();

$routes = [
    'Dashboard' => "/t/draft/{$tenant}/admin-dashboard{$params}",
    'Students' => "/t/draft/{$tenant}/admin/students{$params}",
    'Professors' => "/t/draft/{$tenant}/admin/professors{$params}",
    'Programs' => "/t/draft/{$tenant}/admin/programs{$params}",
    'Modules' => "/t/draft/{$tenant}/admin/modules{$params}",
    'Announcements' => "/t/draft/{$tenant}/admin/announcements{$params}",
    'Batch Enrollment' => "/t/draft/{$tenant}/admin/batches{$params}",
    'Analytics' => "/t/draft/{$tenant}/admin/analytics{$params}",
    'Settings' => "/t/draft/{$tenant}/admin/settings{$params}",
    'Packages' => "/t/draft/{$tenant}/admin/packages{$params}",
    'Directors' => "/t/draft/{$tenant}/admin/directors{$params}",
    'Quiz Generator' => "/t/draft/{$tenant}/admin/quiz-generator{$params}",
    'Payment Pending' => "/t/draft/{$tenant}/admin-student-registration/payment/pending{$params}",
    'Payment History' => "/t/draft/{$tenant}/admin-student-registration/payment/history{$params}",
    'Archived Programs' => "/t/draft/{$tenant}/admin/programs/archived{$params}",
];

echo "=== ADMIN ROUTES DEBUG TEST ===\n";
echo "Testing " . count($routes) . " admin routes...\n\n";

foreach ($routes as $name => $path) {
    $url = $baseUrl . $path;
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    echo "Testing $name...\n";
    echo "URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "❌ FAILED - Network error\n";
    } else {
        $httpCode = null;
        if (isset($http_response_header)) {
            $statusLine = $http_response_header[0];
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $statusLine, $matches)) {
                $httpCode = (int)$matches[1];
            }
        }
        
        if ($httpCode === 200) {
            // Check for specific error patterns
            if (strpos($response, 'No query results for model') !== false) {
                echo "❌ FAILED - Model not found error\n";
            } elseif (strpos($response, 'Method') !== false && strpos($response, 'does not exist') !== false) {
                echo "❌ FAILED - Method does not exist\n";
            } elseif (strpos($response, 'Object of class stdClass could not be converted to string') !== false) {
                echo "❌ FAILED - stdClass conversion error\n";
            } elseif (strpos($response, 'Error') !== false || strpos($response, 'Exception') !== false) {
                echo "❌ FAILED - PHP/Laravel error\n";
            } else {
                echo "✅ SUCCESS\n";
            }
        } elseif ($httpCode === 404) {
            echo "❌ FAILED - Route not found (404)\n";
        } elseif ($httpCode === 500) {
            echo "❌ FAILED - Server error (500)\n";
        } else {
            echo "❌ FAILED - HTTP $httpCode\n";
        }
    }
    echo "\n";
}

echo "=== DEBUG COMPLETE ===\n";
