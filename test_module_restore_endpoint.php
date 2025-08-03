<?php
// Test the module restore endpoint directly
echo "=== Testing Module Restore Endpoint ===\n";

// Simulate AJAX request to toggle archive
$data = [
    'is_archived' => false,
    '_token' => 'test-token' // We'll bypass CSRF for testing
];

$url = 'http://127.0.0.1:8000/admin/modules/1/toggle-archive';

// Use curl to test the endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

// Check if we can connect to the Laravel server
$healthCheck = curl_init();
curl_setopt($healthCheck, CURLOPT_URL, 'http://127.0.0.1:8000');
curl_setopt($healthCheck, CURLOPT_RETURNTRANSFER, true);
curl_setopt($healthCheck, CURLOPT_TIMEOUT, 5);
$health = curl_exec($healthCheck);
$healthCode = curl_getinfo($healthCheck, CURLINFO_HTTP_CODE);
curl_close($healthCheck);

echo "\nHealth Check - HTTP Code: $healthCode\n";
if ($healthCode === 200) {
    echo "✓ Laravel server is running\n";
} else {
    echo "✗ Laravel server is not responding properly\n";
}
