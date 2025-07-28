<?php
// Test file to debug professor search functionality
session_start();

// Simulate professor session using the correct session variables
$_SESSION['user_id'] = 1; // Professor ID
$_SESSION['user_name'] = 'Test Professor';
$_SESSION['user_type'] = 'professor';
$_SESSION['user_role'] = 'professor';
$_SESSION['role'] = 'professor';
$_SESSION['logged_in'] = true;

// Also set Laravel session variables
session([
    'user_id' => 1,
    'user_name' => 'Test Professor',
    'user_type' => 'professor',
    'user_role' => 'professor',
    'role' => 'professor',
    'logged_in' => true
]);

// Test the search endpoint
$url = 'http://127.0.0.1:8000/api/chat/session/search';
$data = [
    'query' => 'student' // Search for students
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'laravel_session=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

// Also test with a different query
$data2 = [
    'query' => 'john' // Search for a specific name
];

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data2));
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_COOKIE, 'laravel_session=' . session_id());

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "\nSecond Test (searching for 'john'):\n";
echo "HTTP Code: " . $httpCode2 . "\n";
echo "Response: " . $response2 . "\n";
?> 