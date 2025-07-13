<?php
// Test script for chat API endpoints
session_start();

// Simulate user session (like the logged-in student)
$_SESSION['user_id'] = 112;
$_SESSION['user_role'] = 'student';
$_SESSION['user_name'] = 'bryan justimbaste';
$_SESSION['logged_in'] = true;

echo "=== Chat API Test ===\n";
echo "User ID: 112 (bryan justimbaste)\n";
echo "Role: student\n\n";

// Test 1: Get professors
echo "Test 1: Get professors\n";
$url = "http://127.0.0.1:8000/api/chat/session/users?type=professor";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/json\r\n" .
                   "Cookie: " . session_name() . "=" . session_id() . "\r\n"
    ]
]);

$response = file_get_contents($url, false, $context);
echo "Response: " . $response . "\n\n";

// Test 2: Get messages with user ID 111 (professor)
echo "Test 2: Get messages with professor ID 111\n";
$url = "http://127.0.0.1:8000/api/chat/session/messages?with=111";
$response = file_get_contents($url, false, $context);
echo "Response: " . $response . "\n\n";

// Test 3: Search for professors
echo "Test 3: Search for professors with 'robert'\n";
$url = "http://127.0.0.1:8000/api/chat/session/users?type=professor&q=robert";
$response = file_get_contents($url, false, $context);
echo "Response: " . $response . "\n\n";

echo "=== Test Complete ===\n";
?>
