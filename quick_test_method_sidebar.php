<?php
$tenant = 'test11';
$website_param = '15';
$preview_param = 'true';
$base_url = "http://127.0.0.1:8000/t/draft/$tenant";

// Test student registration pending
echo "Testing student registration pending with test11...\n";
$pending_url = "$base_url/admin/students/registration-pending?website=$website_param&preview=$preview_param";
echo "URL: $pending_url\n";

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
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
} else {
    echo "✅ SUCCESS - Student registration pending page loads\n";
    
    // Check for TEST11 branding
    if (strpos($response, 'TEST11') !== false) {
        echo "✅ SUCCESS - Tenant branding (TEST11) present\n";
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

// Test sidebar on admin home
echo "\nTesting sidebar cleanup...\n";
$admin_url = "$base_url/admin?website=$website_param&preview=$preview_param";
echo "URL: $admin_url\n";

$response2 = @file_get_contents($admin_url, false, $context);

if ($response2 === false) {
    echo "❌ FAILED - Admin page failed to load\n";
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
} else {
    echo "✅ SUCCESS - Admin page loads\n";
    
    // Check if Course Content Upload is removed from sidebar
    if (strpos($response2, 'Course Content Upload') === false) {
        echo "✅ SUCCESS - Course Content Upload removed from sidebar\n";
    } else {
        echo "❌ FAILED - Course Content Upload still present in sidebar\n";
    }
}
?>
