<?php
echo "🎯 COMPREHENSIVE FIX VERIFICATION TEST\n";
echo "=====================================\n\n";

// Test parameters
$tenant = 'test1';
$website_param = '15';
$preview_param = 'true';
$base_url = "http://127.0.0.1:8000/t/draft/{$tenant}";

echo "Testing all three reported issues:\n";
echo "1. Method error fix (loadTenantCustomization)\n";
echo "2. Module redirect fix (Select Program to View/Manage Modules)\n";
echo "3. Sidebar cleanup (remove Course Content Upload)\n\n";

// Issue 1: Test the method error fix
echo "=== ISSUE 1: METHOD ERROR FIX ===\n";
$pending_url = "{$base_url}/admin/students/registration-pending?website={$website_param}&preview={$preview_param}";
echo "Testing: {$pending_url}\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0\r\n",
        'timeout' => 10
    ]
]);

$response = @file_get_contents($pending_url, false, $context);

if ($response === false) {
    echo "❌ FAILED - Student registration pending page failed to load\n";
} else {
    echo "✅ SUCCESS - Student registration pending page loads\n";
    
    // Check for TEST1 branding
    if (strpos($response, 'TEST1') !== false) {
        echo "✅ SUCCESS - Tenant branding (TEST1) present\n";
    } else {
        echo "❓ INFO - No specific tenant branding found\n";
    }
    
    // Check that it's not an error page
    if (strpos($response, 'Method') === false && strpos($response, 'does not exist') === false) {
        echo "✅ SUCCESS - No method error detected\n";
    } else {
        echo "❌ FAILED - Method error still present\n";
    }
}

echo "\n=== ISSUE 2: MODULE REDIRECT FIX ===\n";
$modules_url = "{$base_url}/admin/modules?website={$website_param}&preview={$preview_param}";
echo "Testing: {$modules_url}\n";

$response2 = @file_get_contents($modules_url, false, $context);

if ($response2 === false) {
    echo "❌ FAILED - Module management page failed to load\n";
} else {
    echo "✅ SUCCESS - Module management page loads\n";
    
    // Check for dropdown
    if (strpos($response2, 'Select Program to View/Manage Modules') !== false) {
        echo "✅ SUCCESS - Program selection dropdown found\n";
    } else {
        echo "❌ FAILED - Program selection dropdown missing\n";
    }
    
    // Check for tenant-aware functions
    if (strpos($response2, 'function getTenantFromPath()') !== false) {
        echo "✅ SUCCESS - getTenantFromPath function implemented\n";
    } else {
        echo "❌ FAILED - getTenantFromPath function missing\n";
    }
    
    if (strpos($response2, 'function getApiUrl(') !== false) {
        echo "✅ SUCCESS - getApiUrl function implemented\n";
    } else {
        echo "❌ FAILED - getApiUrl function missing\n";
    }
    
    // Check for fixed redirect
    if (strpos($response2, 'getApiUrl(`modules?program_id=${programId}`)') !== false) {
        echo "✅ SUCCESS - Tenant-aware redirect URL implemented\n";
    } else if (strpos($response2, '/admin/modules?program_id=') !== false) {
        echo "❌ FAILED - Still using hardcoded redirect URL\n";
    } else {
        echo "❓ INFO - Could not detect redirect URL pattern\n";
    }
}

echo "\n=== ISSUE 3: SIDEBAR CLEANUP ===\n";
// Test any admin page to check sidebar
$admin_home_url = "{$base_url}/admin?website={$website_param}&preview={$preview_param}";
echo "Testing sidebar on: {$admin_home_url}\n";

$response3 = @file_get_contents($admin_home_url, false, $context);

if ($response3 === false) {
    echo "❌ FAILED - Admin page failed to load for sidebar test\n";
} else {
    echo "✅ SUCCESS - Admin page loads for sidebar test\n";
    
    // Check if Course Content Upload is removed from sidebar
    if (strpos($response3, 'Course Content Upload') === false) {
        echo "✅ SUCCESS - Course Content Upload removed from sidebar\n";
    } else {
        echo "❌ FAILED - Course Content Upload still present in sidebar\n";
    }
    
    // Check that other sidebar items are still there
    if (strpos($response3, 'Assignment Submissions') !== false) {
        echo "✅ SUCCESS - Other sidebar items remain intact\n";
    } else {
        echo "❓ INFO - Could not verify other sidebar items\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 FINAL SUMMARY:\n";

$issue1_fixed = $response && strpos($response, 'Method') === false;
$issue2_fixed = $response2 && strpos($response2, 'getApiUrl(`modules?program_id=${programId}`)') !== false;
$issue3_fixed = $response3 && strpos($response3, 'Course Content Upload') === false;

$all_fixed = $issue1_fixed && $issue2_fixed && $issue3_fixed;

if ($all_fixed) {
    echo "🎉 ALL ISSUES RESOLVED SUCCESSFULLY!\n\n";
    echo "✅ Issue 1: Method error fixed (loadTenantCustomization)\n";
    echo "✅ Issue 2: Module redirect fixed (tenant-aware program selection)\n";
    echo "✅ Issue 3: Sidebar cleaned up (Course Content Upload removed)\n\n";
    echo "Users can now:\n";
    echo "- Access student registration pending without method errors\n";
    echo "- Select programs in module management without redirecting to ARTC\n";
    echo "- Use cleaner sidebar without Course Content Upload clutter\n";
} else {
    echo "⚠️  SOME ISSUES REMAIN:\n\n";
    echo ($issue1_fixed ? "✅" : "❌") . " Issue 1: Method error fix\n";
    echo ($issue2_fixed ? "✅" : "❌") . " Issue 2: Module redirect fix\n";
    echo ($issue3_fixed ? "✅" : "❌") . " Issue 3: Sidebar cleanup\n";
}

echo "\nTest completed. Please verify manually by:\n";
echo "1. Visiting the student registration pending page\n";
echo "2. Testing program selection in module management\n";
echo "3. Checking that sidebar no longer shows Course Content Upload\n";
?>
