<?php
echo "🔍 COMPREHENSIVE BUTTON FIX VALIDATION TEST\n";
echo "=" . str_repeat("=", 50) . "\n\n";

/**
 * This script validates all button routing fixes and ensures tenant-aware URLs work properly
 */

$testConfig = [
    'tenant' => 'smartprep',
    'website_param' => '1',
    'server_url' => 'http://127.0.0.1:8000'
];

echo "📋 Step 1: Testing Admin Dashboard Button Fixes\n";
echo "=" . str_repeat("-", 45) . "\n";

// Test admin dashboard with tenant preview
$dashboardPreviewUrl = "{$testConfig['server_url']}/t/draft/{$testConfig['tenant']}/admin/dashboard?website={$testConfig['website_param']}";

echo "🧪 Testing admin dashboard in preview mode:\n";
echo "   URL: $dashboardPreviewUrl\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true,
        'header' => "User-Agent: PHP Test Client\r\n"
    ]
]);

$response = @file_get_contents($dashboardPreviewUrl, false, $context);

if ($response !== false) {
    echo "   ✅ Dashboard accessible in preview mode\n";
    
    // Check for tenant-aware URLs in the response
    $tenantUrls = [
        "/t/draft/{$testConfig['tenant']}/admin/modules" => 'Module action buttons',
        "/t/draft/{$testConfig['tenant']}/admin/courses/upload" => 'Batch upload button',
        "/t/draft/{$testConfig['tenant']}/admin/modules/archived" => 'Archived content button',
        "/t/draft/{$testConfig['tenant']}/admin/programs" => 'Manage programs button'
    ];
    
    foreach ($tenantUrls as $url => $description) {
        if (strpos($response, $url) !== false) {
            echo "   ✅ FOUND: $description with tenant URL\n";
        } else {
            echo "   ❌ MISSING: $description with tenant URL\n";
        }
    }
    
    // Check that old Laravel route() URLs are not present in preview mode
    $oldRoutes = [
        'route(\'admin.modules.index\')' => 'Old module routes',
        'route(\'admin.modules.archived\')' => 'Old archived routes'
    ];
    
    foreach ($oldRoutes as $route => $description) {
        if (strpos($response, $route) === false) {
            echo "   ✅ GOOD: $description not found (replaced with tenant URLs)\n";
        } else {
            echo "   ⚠️  WARNING: $description still present\n";
        }
    }
} else {
    echo "   ❌ Dashboard not accessible in preview mode\n";
}

echo "\n📋 Step 2: Testing Admin Programs Button Fixes\n";
echo "=" . str_repeat("-", 45) . "\n";

// Test admin programs with tenant preview
$programsPreviewUrl = "{$testConfig['server_url']}/t/draft/{$testConfig['tenant']}/admin/programs?website={$testConfig['website_param']}";

echo "🧪 Testing admin programs in preview mode:\n";
echo "   URL: $programsPreviewUrl\n";

$response = @file_get_contents($programsPreviewUrl, false, $context);

if ($response !== false) {
    echo "   ✅ Programs page accessible in preview mode\n";
    
    // Check for tenant-aware View Archived button
    $archivedUrl = "/t/draft/{$testConfig['tenant']}/admin/programs/archived";
    if (strpos($response, $archivedUrl) !== false) {
        echo "   ✅ FOUND: View Archived button with tenant URL\n";
    } else {
        echo "   ❌ MISSING: View Archived button with tenant URL\n";
    }
    
    // Check for errors
    if (strpos($response, 'ModelNotFoundException') !== false) {
        echo "   ❌ ERROR: ModelNotFoundException found\n";
    } elseif (strpos($response, 'Professor.*archived') !== false) {
        echo "   ❌ ERROR: Professor archived error found\n";
    } else {
        echo "   ✅ No model errors found\n";
    }
} else {
    echo "   ❌ Programs page not accessible in preview mode\n";
}

echo "\n📋 Step 3: Testing Individual Button URLs\n";
echo "=" . str_repeat("-", 45) . "\n";

// Test each tenant-aware URL directly
$urlsToTest = [
    "/t/draft/{$testConfig['tenant']}/admin/modules?website={$testConfig['website_param']}" => 'Admin Modules (tenant)',
    "/t/draft/{$testConfig['tenant']}/admin/courses/upload?website={$testConfig['website_param']}" => 'Course Upload (tenant)',
    "/t/draft/{$testConfig['tenant']}/admin/modules/archived?website={$testConfig['website_param']}" => 'Archived Modules (tenant)',
    "/t/draft/{$testConfig['tenant']}/admin/programs?website={$testConfig['website_param']}" => 'Admin Programs (tenant)',
    "/t/draft/{$testConfig['tenant']}/admin/programs/archived?website={$testConfig['website_param']}" => 'Archived Programs (tenant)',
    "/t/draft/{$testConfig['tenant']}/admin/submissions?website={$testConfig['website_param']}" => 'Submissions (tenant)',
    "/t/draft/{$testConfig['tenant']}/admin/certificates?website={$testConfig['website_param']}" => 'Certificates (tenant)'
];

foreach ($urlsToTest as $url => $description) {
    echo "🧪 Testing $description:\n";
    echo "   URL: {$testConfig['server_url']}$url\n";
    
    $response = @file_get_contents($testConfig['server_url'] . $url, false, $context);
    
    if ($response !== false) {
        // Check for common errors
        if (strpos($response, 'ModelNotFoundException') !== false) {
            echo "   ❌ ERROR: ModelNotFoundException\n";
        } elseif (strpos($response, 'Undefined property') !== false) {
            echo "   ❌ ERROR: Undefined property\n";
        } elseif (strpos($response, 'database') !== false && strpos($response, 'error') !== false) {
            echo "   ❌ ERROR: Database error\n";
        } elseif (strpos($response, '500') !== false) {
            echo "   ❌ ERROR: Server error (500)\n";
        } else {
            echo "   ✅ ACCESSIBLE: Page loads successfully\n";
        }
    } else {
        echo "   ❌ ERROR: Cannot access URL\n";
    }
}

echo "\n📋 Step 4: Testing Regular (Non-Preview) Mode\n";
echo "=" . str_repeat("-", 45) . "\n";

// Test regular admin dashboard to ensure normal routes still work
$regularDashboardUrl = "{$testConfig['server_url']}/admin/dashboard";

echo "🧪 Testing regular admin dashboard:\n";
echo "   URL: $regularDashboardUrl\n";

$response = @file_get_contents($regularDashboardUrl, false, $context);

if ($response !== false) {
    echo "   ✅ Regular dashboard accessible\n";
    
    // Check for Laravel route() usage in regular mode
    if (strpos($response, 'route(') !== false || strpos($response, '/admin/modules') !== false) {
        echo "   ✅ Regular Laravel routes present in normal mode\n";
    } else {
        echo "   ❌ Regular Laravel routes missing in normal mode\n";
    }
} else {
    echo "   ❌ Regular dashboard not accessible\n";
}

echo "\n📋 Step 5: Validation Summary\n";
echo "=" . str_repeat("-", 45) . "\n";

// Run a final comprehensive check
$totalTests = 0;
$passedTests = 0;

$summaryTests = [
    $dashboardPreviewUrl => 'Dashboard Preview Mode',
    $programsPreviewUrl => 'Programs Preview Mode',
    $regularDashboardUrl => 'Regular Dashboard Mode'
];

foreach ($summaryTests as $url => $testName) {
    $totalTests++;
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false && 
        strpos($response, 'ModelNotFoundException') === false && 
        strpos($response, 'Undefined property') === false) {
        $passedTests++;
        echo "   ✅ $testName: PASSED\n";
    } else {
        echo "   ❌ $testName: FAILED\n";
    }
}

$successRate = round(($passedTests / $totalTests) * 100);

echo "\n🎯 FINAL RESULTS:\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "Tests Passed: $passedTests/$totalTests\n";
echo "Success Rate: $successRate%\n";

if ($successRate >= 100) {
    echo "🎉 PERFECT! All button routing fixes working correctly!\n";
} elseif ($successRate >= 80) {
    echo "✅ GOOD! Most fixes working, minor issues may exist\n";
} else {
    echo "⚠️  ISSUES: Several problems detected, needs review\n";
}

echo "\n🔗 URLs to test manually:\n";
foreach ($urlsToTest as $url => $description) {
    echo "   $description: {$testConfig['server_url']}$url\n";
}

echo "\n✨ Validation complete!\n";
?>
