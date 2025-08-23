<?php
/**
 * Test admin preview sidebar navigation functionality
 */

// Base URL for testing
$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';

echo "=== Testing Admin Preview Sidebar Navigation ===\n\n";

// Test navigation flow: dashboard -> students -> professors -> programs -> modules -> back to dashboard
$navigationFlow = [
    ['from' => 'Dashboard', 'to' => 'Students', 'start_url' => "/t/draft/{$tenant}/admin-dashboard", 'target_path' => "/t/draft/{$tenant}/admin/students"],
    ['from' => 'Students', 'to' => 'Professors', 'start_url' => "/t/draft/{$tenant}/admin/students", 'target_path' => "/t/draft/{$tenant}/admin/professors"],
    ['from' => 'Professors', 'to' => 'Programs', 'start_url' => "/t/draft/{$tenant}/admin/professors", 'target_path' => "/t/draft/{$tenant}/admin/programs"],
    ['from' => 'Programs', 'to' => 'Modules', 'start_url' => "/t/draft/{$tenant}/admin/programs", 'target_path' => "/t/draft/{$tenant}/admin/modules"],
    ['from' => 'Modules', 'to' => 'Analytics', 'start_url' => "/t/draft/{$tenant}/admin/modules", 'target_path' => "/t/draft/{$tenant}/admin/analytics"],
    ['from' => 'Analytics', 'to' => 'Settings', 'start_url' => "/t/draft/{$tenant}/admin/analytics", 'target_path' => "/t/draft/{$tenant}/admin/settings"],
    ['from' => 'Settings', 'to' => 'Dashboard', 'start_url' => "/t/draft/{$tenant}/admin/settings", 'target_path' => "/t/draft/{$tenant}/admin-dashboard"],
];

$successCount = 0;
$totalTests = count($navigationFlow);

foreach ($navigationFlow as $index => $test) {
    $startUrl = $baseUrl . $test['start_url'];
    $targetPath = $test['target_path'];
    
    echo "Test " . ($index + 1) . ": Navigate from {$test['from']} to {$test['to']}\n";
    echo "Start URL: {$startUrl}\n";
    echo "Expected target: {$targetPath}\n";
    
    // Get the page content to check if navigation links are tenant-aware
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $startUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ CURL Error: {$error}\n";
    } elseif ($httpCode !== 200) {
        echo "❌ HTTP Error: {$httpCode}\n";
    } else {
        // Check if the response contains the tenant-aware navigation link
        if (strpos($response, $targetPath) !== false) {
            echo "✅ PASS: Tenant-aware navigation link found\n";
            $successCount++;
            
            // Quick test: try to access the target URL
            $targetUrl = $baseUrl . $targetPath;
            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $targetUrl);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch2, CURLOPT_NOBODY, true);
            
            $targetCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
            curl_close($ch2);
            
            if ($targetCode === 200) {
                echo "✅ Target URL is accessible\n";
            } else {
                echo "⚠️  Target URL returned {$targetCode}\n";
            }
        } else {
            echo "❌ FAIL: Tenant-aware navigation link NOT found\n";
            
            // Check if old-style links exist
            $oldStylePattern = str_replace("/t/draft/{$tenant}/admin", "/admin", $targetPath);
            $oldStylePattern = str_replace("/t/draft/{$tenant}/admin-dashboard", "/admin-dashboard", $oldStylePattern);
            
            if (strpos($response, $oldStylePattern) !== false) {
                echo "⚠️  Found old-style link: {$oldStylePattern}\n";
            } else {
                echo "❓ No recognizable navigation link found\n";
            }
        }
    }
    
    echo "---\n";
}

echo "\n=== NAVIGATION TEST SUMMARY ===\n";
echo "Tests passed: {$successCount}/{$totalTests}\n";
echo "Success rate: " . round(($successCount / $totalTests) * 100, 1) . "%\n\n";

if ($successCount === $totalTests) {
    echo "🎉 ALL NAVIGATION TESTS PASSED!\n";
    echo "✅ Admin preview sidebar navigation is fully tenant-aware\n";
} else {
    echo "⚠️  Some navigation links may not be tenant-aware\n";
    echo "📝 Check admin sidebar template for missed routes\n";
}

echo "\n=== ADDITIONAL VERIFICATION ===\n";

// Test a few more key routes
$additionalRoutes = [
    'Announcements' => "/t/draft/{$tenant}/admin/announcements",
    'Batches' => "/t/draft/{$tenant}/admin/batches", 
    'Packages' => "/t/draft/{$tenant}/admin/packages"
];

echo "Testing additional admin preview routes:\n";
foreach ($additionalRoutes as $name => $path) {
    $url = $baseUrl . $path;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "✅ {$name}: Working\n";
    } else {
        echo "❌ {$name}: Failed ({$httpCode})\n";
    }
}

echo "\n🚀 Admin preview system testing complete!\n";
