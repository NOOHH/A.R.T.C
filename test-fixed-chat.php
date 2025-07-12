<?php
require_once './vendor/autoload.php';

// Start session
session_start();

// Set up test session data for student
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'student';
$_SESSION['logged_in'] = true;

// Test the chat API endpoints
$baseUrl = 'http://127.0.0.1:8080';

echo "=== Testing Chat API Endpoints ===\n\n";

// Test 1: Get users
echo "1. Testing /api/chat/session/users endpoint...\n";
$usersUrl = $baseUrl . '/api/chat/session/users';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/json\r\n" .
                   "Cookie: " . session_name() . "=" . session_id() . "\r\n"
    ]
]);

$response = file_get_contents($usersUrl, false, $context);
if ($response === false) {
    echo "Error: Could not fetch users\n";
} else {
    $data = json_decode($response, true);
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
}

echo "\n";

// Test 2: Send a message
echo "2. Testing /api/chat/session/send endpoint...\n";
$sendUrl = $baseUrl . '/api/chat/session/send';
$sendData = json_encode([
    'receiver_id' => 8,
    'message' => 'Test message from student - ' . date('Y-m-d H:i:s')
]);

$sendContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n" .
                   "Cookie: " . session_name() . "=" . session_id() . "\r\n",
        'content' => $sendData
    ]
]);

$sendResponse = file_get_contents($sendUrl, false, $sendContext);
if ($sendResponse === false) {
    echo "Error: Could not send message\n";
} else {
    $sendData = json_decode($sendResponse, true);
    echo "Response: " . json_encode($sendData, JSON_PRETTY_PRINT) . "\n";
}

echo "\n";

// Test 3: Get messages
echo "3. Testing /api/chat/session/messages endpoint...\n";
$messagesUrl = $baseUrl . '/api/chat/session/messages?with=8';
$messagesContext = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/json\r\n" .
                   "Cookie: " . session_name() . "=" . session_id() . "\r\n"
    ]
]);

$messagesResponse = file_get_contents($messagesUrl, false, $messagesContext);
if ($messagesResponse === false) {
    echo "Error: Could not fetch messages\n";
} else {
    $messagesData = json_decode($messagesResponse, true);
    echo "Response: " . json_encode($messagesData, JSON_PRETTY_PRINT) . "\n";
}

echo "\n=== Test Complete ===\n";
?>
