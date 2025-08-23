<?php
require_once 'vendor/autoload.php';

echo "=== Testing Professor Login Authentication ===\n\n";

// Test professor login endpoint 
$loginUrl = 'http://127.0.0.1:8000/login';

echo "Testing professor login with fake email...\n";

// Prepare POST data for login
$postData = http_build_query([
    'email' => 'fake.professor@test.com',
    'password' => 'testpassword123',
    '_token' => 'test'
]);

// Use curl to test the login endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json, text/plain, */*'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\nHTTP Status Code: $httpCode\n";
echo "Response:\n";
echo str_repeat("=", 80) . "\n";
echo $response;
echo "\n" . str_repeat("=", 80) . "\n";

echo "\n=== Test Complete ===\n";
?>
