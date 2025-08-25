<?php

/**
 * Announcement Error Fix Validation
 * Tests that the tenant announcement pages load without "Call to a member function any() on null" errors
 */

require_once 'vendor/autoload.php';

function testUrl($url, $description) {
    echo "Testing: $description\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Announcement Fix Test Script');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        // Check for specific error in response
        if (strpos($response, 'Call to a member function any() on null') !== false) {
            echo "❌ FAILED: $description - Error still present\n";
            return false;
        } else {
            echo "✅ SUCCESS: $description - Page loads correctly\n";
            return true;
        }
    } else {
        echo "❌ FAILED: $description - HTTP $httpCode\n";
        return false;
    }
}

echo "=== Announcement Error Fix Validation ===\n\n";

$testCases = [
    [
        'url' => 'http://127.0.0.1:8000/t/draft/test2/admin/announcements',
        'description' => 'Tenant Announcements Index Page'
    ],
    [
        'url' => 'http://127.0.0.1:8000/t/draft/test2/admin/announcements/create',
        'description' => 'Tenant Announcements Create Page'
    ],
    [
        'url' => 'http://127.0.0.1:8000/t/draft/artc/admin/announcements',
        'description' => 'ARTC Tenant Announcements Index Page'
    ],
    [
        'url' => 'http://127.0.0.1:8000/t/draft/artc/admin/announcements/create',
        'description' => 'ARTC Tenant Announcements Create Page'
    ]
];

$passed = 0;
$total = count($testCases);

foreach ($testCases as $test) {
    if (testUrl($test['url'], $test['description'])) {
        $passed++;
    }
    echo "\n";
}

echo "=== Test Results ===\n";
echo "Passed: $passed/$total\n";

if ($passed === $total) {
    echo "🎉 All tests passed! The announcement error fix is working correctly.\n";
    exit(0);
} else {
    echo "⚠️  Some tests failed. Please check the failing URLs.\n";
    exit(1);
}
