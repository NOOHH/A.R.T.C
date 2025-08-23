<?php
/**
 * Comprehensive test for all admin preview routes
 */

// Base URL for testing
$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';

// Admin preview routes to test
$routes = [
    'Dashboard' => "/t/draft/{$tenant}/admin-dashboard",
    'Students' => "/t/draft/{$tenant}/admin/students", 
    'Professors' => "/t/draft/{$tenant}/admin/professors",
    'Programs' => "/t/draft/{$tenant}/admin/programs",
    'Modules' => "/t/draft/{$tenant}/admin/modules",
    'Announcements' => "/t/draft/{$tenant}/admin/announcements",
    'Batches' => "/t/draft/{$tenant}/admin/batches",
    'Analytics' => "/t/draft/{$tenant}/admin/analytics",
    'Settings' => "/t/draft/{$tenant}/admin/settings",
    'Packages' => "/t/draft/{$tenant}/admin/packages"
];

echo "=== Testing All Admin Preview Routes ===\n\n";

$totalRoutes = count($routes);
$successCount = 0;
$failureCount = 0;

foreach ($routes as $name => $path) {
    $url = $baseUrl . $path;
    echo "Testing: {$name}\n";
    echo "URL: {$url}\n";
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ Status: CURL ERROR - {$error}\n";
        $failureCount++;
    } elseif ($httpCode === 200) {
        echo "✅ Status: SUCCESS (200)\n";
        $successCount++;
        
        // Check for common error indicators
        if (strpos($response, 'Error') !== false || strpos($response, 'error') !== false) {
            echo "⚠️  Warning: Response contains 'Error' - may have issues\n";
        } elseif (strpos($response, 'Preview') !== false) {
            echo "✅ Contains preview content - likely working correctly\n";
        }
    } else {
        echo "❌ Status: FAILED ({$httpCode})\n";
        $failureCount++;
        
        // Show first 200 chars of error response for debugging
        if ($response && strlen($response) > 0) {
            $preview = substr(strip_tags($response), 0, 200);
            echo "Error preview: " . $preview . "...\n";
        }
    }
    
    echo "---\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total routes tested: {$totalRoutes}\n";
echo "✅ Successful: {$successCount}\n";
echo "❌ Failed: {$failureCount}\n";
echo "Success rate: " . round(($successCount / $totalRoutes) * 100, 1) . "%\n\n";

if ($successCount === $totalRoutes) {
    echo "🎉 ALL ADMIN PREVIEW ROUTES ARE WORKING!\n\n";
    echo "Next steps:\n";
    echo "1. ✅ Update admin sidebar for tenant-aware routing\n";
    echo "2. ✅ Add admin middleware bypass for preview mode\n";
    echo "3. ✅ Test all admin functionality in preview mode\n";
} else {
    echo "⚠️  Some routes need attention. Check failed routes above.\n\n";
    echo "Debugging steps:\n";
    echo "1. Check Laravel logs for detailed errors\n";
    echo "2. Verify controller methods exist\n";
    echo "3. Check view files and data structures\n";
}
