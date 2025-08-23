<?php
$tenant = 'test11';
$website_param = '15';
$preview_param = 'true';
$pending_url = "http://127.0.0.1:8000/t/draft/$tenant/admin-student-registration/pending?website=$website_param&preview=$preview_param";
echo "Testing corrected URL: $pending_url\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0\r\n",
        'timeout' => 10
    ]
]);

$response = @file_get_contents($pending_url, false, $context);

if ($response === false) {
    echo "❌ FAILED\n";
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
} else {
    echo "✅ SUCCESS - Page loads\n";
    if (strpos($response, 'TEST11') !== false) {
        echo "✅ SUCCESS - Tenant branding present\n";
    }
    if (strpos($response, 'Method') === false && strpos($response, 'does not exist') === false) {
        echo "✅ SUCCESS - No method error\n";
    } else {
        echo "❌ FAILED - Method error present\n";
    }
}
?>
