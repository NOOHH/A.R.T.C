<?php
// Test payment method creation endpoint
$url = 'http://127.0.0.1:8000/admin/settings/payment-methods/';

// Create test data
$postData = [
    'method_name' => 'Test GCash',
    'method_type' => 'gcash',
    'description' => 'Test payment method',
    'instructions' => 'Test instructions',
    'is_enabled' => '1',
    '_token' => 'test-token' // This will fail CSRF but let's see other validation errors
];

// Create a simple test image
$testImagePath = sys_get_temp_dir() . '/test_qr.png';
$testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
file_put_contents($testImagePath, $testImageContent);

// Create cURL handle
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'method_name' => $postData['method_name'],
    'method_type' => $postData['method_type'], 
    'description' => $postData['description'],
    'instructions' => $postData['instructions'],
    'is_enabled' => $postData['is_enabled'],
    '_token' => $postData['_token'],
    'qr_code' => new CURLFile($testImagePath, 'image/png', 'test_qr.png')
]);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Display results
echo "HTTP Code: {$httpCode}\n";
if ($error) {
    echo "cURL Error: {$error}\n";
}
echo "Response: {$response}\n";

// Cleanup
if (file_exists($testImagePath)) {
    unlink($testImagePath);
}
