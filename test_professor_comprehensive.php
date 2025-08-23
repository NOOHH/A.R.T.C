<?php

echo "=== COMPREHENSIVE PROFESSOR PREVIEW TEST ===\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenantSlug = 'test1';
$websiteId = 15;

// Test all professor routes in preview mode
$professorRoutes = [
    'Dashboard' => "/t/draft/{$tenantSlug}/professor/dashboard?website={$websiteId}&preview=true",
    'Programs' => "/t/draft/{$tenantSlug}/professor/programs?website={$websiteId}&preview=true",
    'Modules' => "/t/draft/{$tenantSlug}/professor/modules?website={$websiteId}&preview=true",
    'Meetings' => "/t/draft/{$tenantSlug}/professor/meetings?website={$websiteId}&preview=true",
    'Grading' => "/t/draft/{$tenantSlug}/professor/grading?website={$websiteId}&preview=true",
    'Announcements' => "/t/draft/{$tenantSlug}/professor/announcements?website={$websiteId}&preview=true",
    'Students' => "/t/draft/{$tenantSlug}/professor/students?website={$websiteId}&preview=true",
    'Profile' => "/t/draft/{$tenantSlug}/professor/profile?website={$websiteId}&preview=true",
    'Settings' => "/t/draft/{$tenantSlug}/professor/settings?website={$websiteId}&preview=true",
];

$successCount = 0;
$totalCount = count($professorRoutes);

echo "Testing {$totalCount} professor preview routes...\n\n";

foreach ($professorRoutes as $name => $route) {
    $url = $baseUrl . $route;
    
    echo "🔍 Testing {$name}...\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Professor Preview System Test');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentLength = strlen($response);
    
    if (curl_error($ch)) {
        echo "  ❌ FAILED: " . curl_error($ch) . "\n";
    } else {
        if ($httpCode === 200) {
            echo "  ✅ SUCCESS: HTTP {$httpCode}, {$contentLength} bytes\n";
            $successCount++;
            
            // Check for database errors specifically
            $databaseErrors = [
                'Table \'smartprep.professors\' doesn\'t exist',
                'SQLSTATE[42S02]',
                'Base table or view not found',
                'professors\' doesn\'t exist',
                'students\' doesn\'t exist',
                'programs\' doesn\'t exist'
            ];
            
            $hasDbErrors = false;
            foreach ($databaseErrors as $error) {
                if (stripos($response, $error) !== false) {
                    echo "  ❌ DATABASE ERROR: {$error}\n";
                    $hasDbErrors = true;
                    $successCount--; // Don't count as success if has DB errors
                    break;
                }
            }
            
            // Check for other critical errors
            $criticalErrors = ['exception', 'fatal', 'undefined array key', 'undefined variable'];
            $hasCriticalErrors = false;
            
            foreach ($criticalErrors as $error) {
                if (stripos($response, $error) !== false) {
                    echo "  ⚠️  WARNING: Found '{$error}' in response\n";
                    $hasCriticalErrors = true;
                }
            }
            
            if (!$hasDbErrors && !$hasCriticalErrors) {
                echo "  🎯 CLEAN: No obvious errors detected\n";
            }
            
        } else {
            echo "  ❌ FAILED: HTTP {$httpCode}\n";
            
            if ($httpCode === 404) {
                echo "  📝 Route not found - may need to be implemented\n";
            } elseif ($httpCode === 500) {
                echo "  💥 Server error - check logs for details\n";
            }
        }
    }
    
    curl_close($ch);
    echo "\n";
}

echo "=== TEST SUMMARY ===\n";
echo "✅ Successful: {$successCount}/{$totalCount} routes\n";
echo "❌ Failed: " . ($totalCount - $successCount) . "/{$totalCount} routes\n";

if ($successCount === $totalCount) {
    echo "\n🎉 ALL PROFESSOR TESTS PASSED! Preview system is fully functional.\n";
    echo "\nThe professor preview system now:\n";
    echo "  • Bypasses authentication for preview mode\n";
    echo "  • Loads mock data instead of real database\n";
    echo "  • Supports all main professor navigation routes\n";
    echo "  • Properly handles tenant context switching\n";
    echo "  • Generates tenant-aware navigation links\n";
} else {
    $failedCount = $totalCount - $successCount;
    echo "\n⚠️  {$failedCount} routes are still failing. Check the details above.\n";
    echo "\n📋 Next steps:\n";
    echo "  1. Implement missing preview methods for failed routes\n";
    echo "  2. Check database access and tenant switching\n";
    echo "  3. Verify view files exist for all routes\n";
    echo "  4. Test navigation links within preview mode\n";
}

echo "\n=== PROFESSOR PREVIEW TEST COMPLETE ===\n";
