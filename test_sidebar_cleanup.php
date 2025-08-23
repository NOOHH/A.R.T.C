<?php
$tenant = 'test11';
$website_param = '15';
$preview_param = 'true';
$dashboard_url = "http://127.0.0.1:8000/t/draft/$tenant/admin-dashboard?website=$website_param&preview=$preview_param";
echo "Testing admin dashboard for sidebar: $dashboard_url\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0\r\n",
        'timeout' => 10
    ]
]);

$response = @file_get_contents($dashboard_url, false, $context);

if ($response === false) {
    echo "❌ FAILED - Admin dashboard failed to load\n";
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
} else {
    echo "✅ SUCCESS - Admin dashboard loads\n";
    
    // Check if Course Content Upload is removed from sidebar
    if (strpos($response, 'Course Content Upload') === false) {
        echo "✅ SUCCESS - Course Content Upload removed from sidebar\n";
    } else {
        echo "❌ FAILED - Course Content Upload still present in sidebar\n";
    }
    
    // Check that other sidebar items are still there
    if (strpos($response, 'Assignment Submissions') !== false) {
        echo "✅ SUCCESS - Other sidebar items remain intact\n";
    } else {
        echo "❓ INFO - Could not verify other sidebar items\n";
    }
}
?>
