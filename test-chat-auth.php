<?php

// Test the chat system with proper authentication
$baseUrl = 'http://127.0.0.1:8080';

echo "=== Testing Chat API with Authentication ===\n\n";

// Function to make HTTP request with cookies
function makeRequest($url, $method = 'GET', $data = null, $cookies = '') {
    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json\r\n" .
                       "Cookie: $cookies\r\n",
            'content' => $data ? json_encode($data) : null,
            'ignore_errors' => true
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    $headers = $http_response_header;
    
    return [
        'response' => $response,
        'headers' => $headers
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

// First, get the CSRF token and session
echo "1. Getting CSRF token and session...\n";
$result = makeRequest($baseUrl . '/login');
$cookies = extractCookies($result['headers']);
echo "Cookies: $cookies\n";

// Now test the chat endpoints directly with session simulation
echo "\n2. Testing chat endpoints with manual session...\n";

// Create a simple test to check if the ChatController methods work
$testUrl = $baseUrl . '/test-chat-direct.php';

// Let me create a direct test file
file_put_contents('test-chat-direct.php', '<?php
// Direct test of ChatController methods
require_once "./vendor/autoload.php";

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;

// Bootstrap Laravel application
$app = require_once "./bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Mock session data
session(["user_id" => 1, "user_role" => "student", "logged_in" => true]);

$controller = new ChatController();

// Test getSessionUsers
echo "Testing getSessionUsers...\n";
$request = new Request(["type" => "all"]);
$response = $controller->getSessionUsers($request);
echo "Response: " . $response->getContent() . "\n\n";

// Test getSessionMessages
echo "Testing getSessionMessages...\n";
$request = new Request(["with" => "8"]);
$response = $controller->getSessionMessages($request);
echo "Response: " . $response->getContent() . "\n\n";

// Test sendSessionMessage
echo "Testing sendSessionMessage...\n";
$request = new Request();
$request->merge(["receiver_id" => 8, "message" => "Test message from controller"]);
$response = $controller->sendSessionMessage($request);
echo "Response: " . $response->getContent() . "\n";
');

echo "Running direct controller test...\n";
$result = makeRequest($baseUrl . '/test-chat-direct.php');
echo $result['response'];

echo "\n=== Test Complete ===\n";
?>
