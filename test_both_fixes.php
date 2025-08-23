<?php
echo "🔧 TESTING BOTH ISSUE FIXES\n";
echo "===========================\n\n";

$tenant = 'test11';
$website_param = '15';
$preview_param = 'true';

echo "Issue 1: Testing modules_id property fix\n";
echo "----------------------------------------\n";
$modules_url = "http://127.0.0.1:8000/t/draft/$tenant/admin/modules?website=$website_param&preview=$preview_param";
echo "Testing: $modules_url\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0\r\n",
        'timeout' => 10
    ]
]);

$response1 = @file_get_contents($modules_url, false, $context);

if ($response1 === false) {
    echo "❌ FAILED - Modules page failed to load\n";
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
} else {
    echo "✅ SUCCESS - Modules page loads\n";
    
    // Check for the specific error
    if (strpos($response1, 'Undefined property') !== false) {
        echo "❌ FAILED - Still has undefined property error\n";
    } else {
        echo "✅ SUCCESS - No undefined property errors detected\n";
    }
    
    // Check if modules_id is present in the HTML
    if (strpos($response1, 'modules_id') !== false) {
        echo "✅ SUCCESS - modules_id property found in output\n";
    } else {
        echo "❓ INFO - modules_id not found (may be normal for mock data)\n";
    }
}

echo "\nIssue 2: Testing student registration pending UI consistency\n";
echo "-----------------------------------------------------------\n";
$pending_url = "http://127.0.0.1:8000/t/draft/$tenant/admin-student-registration/pending?website=$website_param&preview=$preview_param";
echo "Testing: $pending_url\n";

$response2 = @file_get_contents($pending_url, false, $context);

if ($response2 === false) {
    echo "❌ FAILED - Student registration pending page failed to load\n";
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
} else {
    echo "✅ SUCCESS - Student registration pending page loads\n";
    
    // Check if it's using the hardcoded HTML (old way)
    if (strpos($response2, '<div class="header">') !== false && strpos($response2, '<div class="pending-item">') !== false) {
        echo "❌ FAILED - Still using hardcoded HTML instead of Blade template\n";
    } else {
        echo "✅ SUCCESS - Now using proper Blade template\n";
    }
    
    // Check for tenant-aware navbar/layout
    if (strpos($response2, 'admin-layouts') !== false || strpos($response2, 'navbar') !== false) {
        echo "✅ SUCCESS - Proper admin layout detected\n";
    } else {
        echo "❓ INFO - Could not detect admin layout (may need verification)\n";
    }
    
    // Check for TEST11 branding
    if (strpos($response2, 'TEST11') !== false) {
        echo "✅ SUCCESS - Tenant branding (TEST11) present\n";
    } else {
        echo "❓ INFO - No tenant branding found\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 SUMMARY:\n";

$modules_fixed = $response1 && strpos($response1, 'Undefined property') === false;
$pending_ui_fixed = $response2 && strpos($response2, '<div class="header">') === false;

if ($modules_fixed && $pending_ui_fixed) {
    echo "🎉 BOTH ISSUES RESOLVED!\n\n";
    echo "✅ Issue 1: modules_id property error fixed\n";
    echo "✅ Issue 2: Student registration pending now uses proper Blade template\n\n";
    echo "The student registration pending page should now have:\n";
    echo "- Proper tenant-aware navbar like other admin pages\n";
    echo "- Consistent UI/UX with the rest of the admin interface\n";
    echo "- Dynamic tenant branding instead of hardcoded content\n";
} else {
    echo "⚠️  SOME ISSUES REMAIN:\n\n";
    echo ($modules_fixed ? "✅" : "❌") . " Issue 1: modules_id property fix\n";
    echo ($pending_ui_fixed ? "✅" : "❌") . " Issue 2: Student registration pending UI fix\n";
}

echo "\nPlease manually verify that the student registration pending page\n";
echo "now has the same navbar and layout as other admin pages.\n";
?>
