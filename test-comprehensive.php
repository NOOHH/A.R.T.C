<?php

// Test the chat system by simulating a logged-in student
$baseUrl = 'http://127.0.0.1:8080';

echo "=== Testing Chat System with Simulated Authentication ===\n\n";

// Function to make HTTP request with session cookies
function makeRequest($url, $method = 'GET', $data = null, $cookies = '', $headers = []) {
    $defaultHeaders = [
        "Content-Type: application/json",
        "X-Requested-With: XMLHttpRequest",
        "Accept: application/json"
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

// First get a session
echo "1. Getting initial session...\n";
$result = makeRequest($baseUrl . '/');
$cookies = extractCookies($result['headers']);
echo "Session established\n\n";

// Now let's try to access student login page first
echo "2. Accessing student login page...\n";
$result = makeRequest($baseUrl . '/student/login', 'GET', null, $cookies);
$cookies = extractCookies($result['headers']) ?: $cookies;
echo "Student login page accessed\n\n";

// Let's examine what the ChatController is expecting for authentication
echo "3. Testing chat API with detailed debugging...\n";
$result = makeRequest($baseUrl . '/api/chat/session/users', 'GET', null, $cookies);
echo "Response: " . $result['response'] . "\n\n";

// Let's also test the direct database approach by creating a custom endpoint
echo "4. Let's try bypassing authentication temporarily...\n";

// Create a test endpoint file
$testEndpointContent = '<?php
// Test endpoint for chat functionality
require_once "./vendor/autoload.php";
$app = require_once "./bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Chat;
use App\Models\Admin;
use App\Models\Professor;

header("Content-Type: application/json");

// Manually set session data for testing
session_start();
$_SESSION["user_id"] = 1;
$_SESSION["user_role"] = "student";
$_SESSION["logged_in"] = true;

// Also try Laravel session
session([
    "user_id" => 1,
    "user_role" => "student", 
    "logged_in" => true
]);

// Now test the ChatController
$controller = new App\Http\Controllers\ChatController();
$request = new Illuminate\Http\Request();
$request->merge(["type" => "all"]);

try {
    $response = $controller->getSessionUsers($request);
    echo $response->getContent();
} catch (Exception $e) {
    echo json_encode([
        "error" => "Exception occurred",
        "message" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
}
?>';

file_put_contents('test-endpoint.php', $testEndpointContent);

$result = makeRequest($baseUrl . '/test-endpoint.php', 'GET', null, $cookies);
echo "Test endpoint response: " . $result['response'] . "\n\n";

echo "=== Test Complete ===\n";
?>
