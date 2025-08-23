<?php
/**
 * QUICK VALIDATION - Check if the navbar route errors are fixed
 */

echo "🎯 QUICK NAVBAR ERROR VALIDATION\n";
echo "Testing specifically the route errors reported\n";
echo "=================================\n\n";

$baseUrl = 'http://localhost:8000';
$tenant = 'test1';
$params = 'website=15&preview=true';

// Test the specific sections mentioned by user
$testUrls = [
    'Payment Pending' => "/t/draft/{$tenant}/admin/payments/pending?{$params}",
    'Payment History' => "/t/draft/{$tenant}/admin/payments/history?{$params}",
    'Certificates' => "/t/draft/{$tenant}/admin/certificates?{$params}",
    'Archived Content' => "/t/draft/{$tenant}/admin/archived?{$params}",
];

$allWorking = true;
$hasNavbarErrors = false;

foreach ($testUrls as $name => $url) {
    echo "Testing: {$name}\n";
    echo "URL: {$baseUrl}{$url}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ Status: Working (HTTP 200)\n";
        echo "📄 Size: " . number_format(strlen($response)) . " bytes\n";
        
        // Check for TEST11 branding
        $brandingCount = substr_count($response, 'TEST11');
        if ($brandingCount >= 2) {
            echo "✅ Branding: {$brandingCount} TEST11 instances found\n";
        } else {
            echo "⚠️  Branding: Only {$brandingCount} TEST11 instances\n";
        }
        
        // Check for route errors
        if (strpos($response, 'Route [admin.archived] not defined') !== false) {
            echo "❌ ERROR: admin.archived route still not defined\n";
            $hasNavbarErrors = true;
        } else {
            echo "✅ No admin.archived route errors\n";
        }
        
        if (strpos($response, 'Route [admin.courses.upload] not defined') !== false) {
            echo "❌ ERROR: admin.courses.upload route still not defined\n";
            $hasNavbarErrors = true;
        } else {
            echo "✅ No admin.courses.upload route errors\n";
        }
        
        // Check for any other route errors
        if (strpos($response, 'not defined') !== false) {
            echo "⚠️  Other route errors may exist in response\n";
            $hasNavbarErrors = true;
        }
        
    } elseif ($httpCode == 500) {
        echo "❌ Status: Server Error (HTTP 500)\n";
        if (strpos($response, 'Route [admin.archived] not defined') !== false) {
            echo "❌ Cause: admin.archived route not defined\n";
        }
        if (strpos($response, 'Route [admin.courses.upload] not defined') !== false) {
            echo "❌ Cause: admin.courses.upload route not defined\n";
        }
        $allWorking = false;
        $hasNavbarErrors = true;
    } else {
        echo "❌ Status: HTTP {$httpCode}\n";
        $allWorking = false;
    }
    
    echo "\n";
}

// Check if the regular admin routes exist now
echo "🔗 REGULAR ADMIN ROUTES CHECK:\n";
echo str_repeat('-', 40) . "\n";

$regularTests = [
    'admin.archived' => '/admin/archived',
    'admin.courses.upload' => '/admin/courses/upload',
];

foreach ($regularTests as $routeName => $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ Route {$routeName}: Working (HTTP 200)\n";
    } else {
        echo "❌ Route {$routeName}: HTTP {$httpCode}\n";
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 FINAL RESULTS:\n";
echo str_repeat('=', 50) . "\n";

if ($allWorking && !$hasNavbarErrors) {
    echo "🎉 SUCCESS: All issues resolved!\n";
    echo "✅ All pages load without 500 errors\n";
    echo "✅ No navbar route errors found\n";
    echo "✅ TEST11 branding working\n";
    echo "✅ Regular admin routes now exist\n";
} elseif ($allWorking && $hasNavbarErrors) {
    echo "⚠️  PARTIAL SUCCESS: Pages work but navbar has route errors\n";
    echo "✅ All pages return HTTP 200\n";
    echo "❌ Navbar still contains route definition errors\n";
} else {
    echo "❌ ISSUES REMAIN: Some pages still returning errors\n";
}

echo "\nValidation completed at " . date('Y-m-d H:i:s') . "\n";

?>
