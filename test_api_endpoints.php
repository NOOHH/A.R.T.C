<?php
// Direct test of the API endpoints
echo "Testing Chat API endpoints directly:" . PHP_EOL;

// Test 1: Test the users search endpoint
echo "Test 1: Search users endpoint" . PHP_EOL;

$url = "http://localhost:8000/api/chat/session/users?type=all&q=a";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: test-token'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}" . PHP_EOL;
echo "Response: {$response}" . PHP_EOL;

// Test 2: Test the messages endpoint  
echo PHP_EOL . "Test 2: Messages endpoint" . PHP_EOL;

$url = "http://localhost:8000/api/chat/session/messages?with=8";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: test-token'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}" . PHP_EOL;
echo "Response: {$response}" . PHP_EOL;

// Test 3: Test sending a message
echo PHP_EOL . "Test 3: Send message endpoint" . PHP_EOL;

$url = "http://localhost:8000/api/chat/session/send";
$data = json_encode([
    'receiver_id' => 8,
    'message' => 'Test message from API'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: test-token'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}" . PHP_EOL;
echo "Response: {$response}" . PHP_EOL;
?>
