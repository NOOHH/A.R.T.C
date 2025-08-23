<?php
/**
 * Comprehensive Final Test - Check all admin preview functionality
 * Test all reported issues and new API endpoints
 */

echo "🧪 COMPREHENSIVE FINAL TEST - ALL ADMIN PREVIEW FUNCTIONALITY\n";
echo "============================================================\n";

// Test configuration
$tenant = 'test11';
$website_param = '15';
$preview_param = 'true';
$base_url = "http://127.0.0.1:8000/t/draft/{$tenant}";

// All tests to run
$tests = [
    // Original working pages
    ['url' => "{$base_url}/admin-dashboard?website={$website_param}&preview={$preview_param}", 'name' => 'Admin Dashboard', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/professors?website={$website_param}&preview={$preview_param}", 'name' => 'Professors Management', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/students?website={$website_param}&preview={$preview_param}", 'name' => 'Students Management', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/courses?website={$website_param}&preview={$preview_param}", 'name' => 'Courses Management', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/batches?website={$website_param}&preview={$preview_param}", 'name' => 'Batches Management', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/announcements?website={$website_param}&preview={$preview_param}", 'name' => 'Announcements', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/payments?website={$website_param}&preview={$preview_param}", 'name' => 'Payment Management', 'expect' => 'TEST11'],
    
    // Previously fixed routes
    ['url' => "{$base_url}/admin/archived/courses?website={$website_param}&preview={$preview_param}", 'name' => 'Archived Courses', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/archived/materials?website={$website_param}&preview={$preview_param}", 'name' => 'Archived Materials', 'expect' => 'TEST11'],
    ['url' => "{$base_url}/admin/student-registration?website={$website_param}&preview={$preview_param}", 'name' => 'Student Registration', 'expect' => 'TEST11'],
    
    // Course content upload page
    ['url' => "{$base_url}/admin/courses/upload?website={$website_param}&preview={$preview_param}", 'name' => 'Course Content Upload', 'expect' => 'TEST11'],
];

// API endpoint tests
$api_tests = [
    ['url' => "{$base_url}/admin/modules/by-program?program_id=1", 'name' => 'Modules by Program API', 'expect' => 'TEST11', 'type' => 'json'],
    ['url' => "{$base_url}/admin/modules/1/courses", 'name' => 'Courses by Module API', 'expect' => 'TEST11', 'type' => 'json'],
];

$results = [];
$total_tests = count($tests) + count($api_tests);
$passed = 0;
$failed = 0;

// Test HTML pages
echo "\n📄 TESTING HTML PAGES:\n";
echo "----------------------\n";

foreach ($tests as $test) {
    echo "Testing: {$test['name']}... ";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($test['url'], false, $context);
    
    if ($response === false) {
        echo "❌ FAILED - Could not fetch URL\n";
        $results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => 'Could not fetch URL'];
        $failed++;
        continue;
    }
    
    // Check for TEST11 branding
    if (strpos($response, $test['expect']) !== false) {
        echo "✅ PASSED - {$test['expect']} branding found\n";
        $results[] = ['test' => $test['name'], 'status' => 'PASSED', 'reason' => "{$test['expect']} branding present"];
        $passed++;
    } else {
        echo "❌ FAILED - {$test['expect']} branding missing\n";
        $results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => "{$test['expect']} branding not found"];
        $failed++;
    }
}

// Test API endpoints
echo "\n🔌 TESTING API ENDPOINTS:\n";
echo "-------------------------\n";

foreach ($api_tests as $test) {
    echo "Testing: {$test['name']}... ";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true,
            'header' => 'Accept: application/json'
        ]
    ]);
    
    $response = @file_get_contents($test['url'], false, $context);
    
    if ($response === false) {
        echo "❌ FAILED - Could not fetch API endpoint\n";
        $results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => 'Could not fetch API endpoint'];
        $failed++;
        continue;
    }
    
    $json_data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ FAILED - Invalid JSON response\n";
        $results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => 'Invalid JSON response'];
        $failed++;
        continue;
    }
    
    // Check for success and TEST11 branding in API response
    if (isset($json_data['success']) && $json_data['success'] && strpos(json_encode($json_data), $test['expect']) !== false) {
        echo "✅ PASSED - API working with {$test['expect']} branding\n";
        $results[] = ['test' => $test['name'], 'status' => 'PASSED', 'reason' => "API success with {$test['expect']} branding"];
        $passed++;
    } else {
        echo "❌ FAILED - API error or missing {$test['expect']} branding\n";
        $results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => "API error or missing {$test['expect']} branding"];
        $failed++;
    }
}

// Test module redirect behavior
echo "\n🔄 TESTING MODULE REDIRECT BEHAVIOR:\n";
echo "------------------------------------\n";

$module_url = "{$base_url}/admin/courses/upload?website={$website_param}&preview={$preview_param}";
echo "Testing course content upload page for tenant context preservation...\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($module_url, false, $context);

if ($response !== false) {
    // Check if JavaScript uses tenant-aware URLs
    if (strpos($response, 'getApiUrl(') !== false && strpos($response, 'getTenantFromPath()') !== false) {
        echo "✅ PASSED - JavaScript uses tenant-aware API URLs\n";
        $results[] = ['test' => 'Module Redirect Fix', 'status' => 'PASSED', 'reason' => 'JavaScript uses tenant-aware API URLs'];
        $passed++;
    } else {
        echo "❌ FAILED - JavaScript still uses hardcoded URLs\n";
        $results[] = ['test' => 'Module Redirect Fix', 'status' => 'FAILED', 'reason' => 'JavaScript uses hardcoded URLs'];
        $failed++;
    }
} else {
    echo "❌ FAILED - Could not fetch course content upload page\n";
    $results[] = ['test' => 'Module Redirect Fix', 'status' => 'FAILED', 'reason' => 'Could not fetch page'];
    $failed++;
}

$total_tests++;

// Summary
echo "\n📊 TEST SUMMARY:\n";
echo "================\n";
echo "Total Tests: {$total_tests}\n";
echo "✅ Passed: {$passed}\n";
echo "❌ Failed: {$failed}\n";
echo "Success Rate: " . round(($passed / $total_tests) * 100, 1) . "%\n";

echo "\n📋 DETAILED RESULTS:\n";
echo "====================\n";

foreach ($results as $result) {
    $status_icon = $result['status'] === 'PASSED' ? '✅' : '❌';
    echo "{$status_icon} {$result['test']}: {$result['status']} - {$result['reason']}\n";
}

if ($failed === 0) {
    echo "\n🎉 ALL TESTS PASSED! The comprehensive fix is complete.\n";
    echo "✅ All admin preview pages work with TEST11 branding\n";
    echo "✅ All API endpoints work correctly\n";
    echo "✅ Module redirect issue has been fixed\n";
} else {
    echo "\n⚠️  SOME TESTS FAILED - Need further investigation\n";
    
    if ($failed <= 2) {
        echo "Only {$failed} test(s) failed - likely minor issues\n";
    } else {
        echo "Multiple tests failed - may need significant fixes\n";
    }
}

echo "\n🏁 Test completed!\n";
?>
