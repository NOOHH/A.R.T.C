<?php

/**
 * Test all admin preview pages to ensure they're working with mock data
 */

echo "Testing all admin preview pages with mock data...\n\n";

$baseUrl = "http://localhost:8000/t/draft/test1";
$params = "?website=15&preview=true&t=" . time();

$pages = [
    'Admin Dashboard' => '/admin-dashboard',
    'Students' => '/admin/students', 
    'Professors' => '/admin/professors',
    'Programs' => '/admin/programs',
    'Modules' => '/admin/modules',
    'Announcements' => '/admin/announcements',
    'Batch Enrollment' => '/admin/batches',
    'Analytics' => '/admin/analytics',
    'Settings' => '/admin/settings',
    'Packages' => '/admin/packages',
    'Directors' => '/admin/directors',
    'Quiz Generator' => '/admin/quiz-generator',
    'Payments' => '/admin/payments'
];

$results = [];

foreach ($pages as $name => $path) {
    $url = $baseUrl . $path . $params;
    echo "Testing: {$name}\n";
    echo "URL: {$url}\n";
    
    // Test with cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AdminPreviewTester/1.0');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "  ❌ cURL Error: {$error}\n";
        $results[$name] = "cURL Error: {$error}";
    } elseif ($httpCode !== 200) {
        echo "  ❌ HTTP {$httpCode}\n";
        $results[$name] = "HTTP {$httpCode}";
    } elseif (strpos($response, 'Error rendering full view') !== false) {
        // Extract error message
        preg_match('/Error rendering full view: ([^<]+)/', $response, $matches);
        $errorMsg = isset($matches[1]) ? $matches[1] : 'Unknown error';
        echo "  ❌ View Error: {$errorMsg}\n";
        $results[$name] = "View Error: {$errorMsg}";
    } elseif (strpos($response, 'Test1') !== false || strpos($response, 'test1') !== false) {
        echo "  ✅ Working with Test1 branding\n";
        $results[$name] = "✅ Working";
    } else {
        echo "  ⚠️  Page loads but no Test1 branding found\n";
        $results[$name] = "⚠️ No branding";
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
$working = 0;
$errors = 0;
$warnings = 0;

foreach ($results as $page => $status) {
    echo "{$page}: {$status}\n";
    if (strpos($status, '✅') !== false) {
        $working++;
    } elseif (strpos($status, '⚠️') !== false) {
        $warnings++;
    } else {
        $errors++;
    }
}

echo "\nTotal: " . count($results) . " pages\n";
echo "Working: {$working}\n";
echo "Warnings: {$warnings}\n";
echo "Errors: {$errors}\n";

if ($errors === 0) {
    echo "\n🎉 All admin preview pages are working!\n";
} else {
    echo "\n⚠️  Some pages still have issues that need to be fixed.\n";
}
