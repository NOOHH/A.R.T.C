<?php

// Test the chat system using web routes
$baseUrl = 'http://127.0.0.1:8080';

echo "=== Testing Chat System via Web Routes ===\n\n";

// Function to make HTTP request with session cookies
function makeRequest($url, $method = 'GET', $data = null, $cookies = '', $headers = []) {
    $defaultHeaders = [
        "Content-Type: application/json",
        "X-Requested-With: XMLHttpRequest"
    ];
    
    if ($cookies) {
        $defaultHeaders[] = "Cookie: $cookies";
    }
    
    $allHeaders = array_merge($defaultHeaders, $headers);
    
    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $allHeaders) . "\r\n",
            'content' => $data ? json_encode($data) : null,
            'ignore_errors' => true
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    return [
        'response' => $response,
        'headers' => $http_response_header ?? []
    ];
}

// Function to extract cookies from headers
function extractCookies($headers) {
    $cookies = [];
    foreach ($headers as $header) {
        if (stripos($header, 'Set-Cookie:') === 0) {
            $cookie = substr($header, 12);
            $cookies[] = explode(';', $cookie)[0];
        }
    }
    return implode('; ', $cookies);
}

// Get initial session
echo "1. Getting initial session...\n";
$result = makeRequest($baseUrl . '/');
$cookies = extractCookies($result['headers']);
echo "Session cookies: " . substr($cookies, 0, 100) . "...\n\n";

// Test the chat endpoints
echo "2. Testing /api/chat/session/users...\n";
$result = makeRequest($baseUrl . '/api/chat/session/users', 'GET', null, $cookies);
echo "Status: " . (strpos($result['headers'][0], '200') !== false ? 'SUCCESS' : 'FAILED') . "\n";
echo "Response: " . substr($result['response'], 0, 500) . "...\n\n";

echo "3. Testing /api/chat/session/messages?with=8...\n";
$result = makeRequest($baseUrl . '/api/chat/session/messages?with=8', 'GET', null, $cookies);
echo "Status: " . (strpos($result['headers'][0], '200') !== false ? 'SUCCESS' : 'FAILED') . "\n";
echo "Response: " . substr($result['response'], 0, 500) . "...\n\n";

echo "4. Testing /api/chat/session/send...\n";
$result = makeRequest($baseUrl . '/api/chat/session/send', 'POST', [
    'receiver_id' => 8,
    'message' => 'Test message from web route - ' . date('Y-m-d H:i:s')
], $cookies);
echo "Status: " . (strpos($result['headers'][0], '200') !== false ? 'SUCCESS' : 'FAILED') . "\n";
echo "Response: " . substr($result['response'], 0, 500) . "...\n\n";

echo "=== Test Complete ===\n";
?>
