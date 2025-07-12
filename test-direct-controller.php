<?php

// Test by creating a simple controller test
require_once './vendor/autoload.php';

// Bootstrap Laravel
$app = require_once './bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;

echo "=== Direct Controller Test ===\n\n";

// Set Laravel session data
session(['user_id' => 1, 'user_role' => 'student', 'logged_in' => true]);

echo "Session data set: user_id=1, user_role=student, logged_in=true\n\n";

// Create controller instance and test
$controller = new ChatController();

// Test 1: Get users
echo "1. Testing getSessionUsers...\n";
$request = new Request();
$request->merge(['type' => 'all']);
$request->setMethod('GET');

try {
    $response = $controller->getSessionUsers($request);
    $data = json_decode($response->getContent(), true);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Get messages
echo "2. Testing getSessionMessages...\n";
$request = new Request();
$request->merge(['with' => '8']);
$request->setMethod('GET');

try {
    $response = $controller->getSessionMessages($request);
    $data = json_decode($response->getContent(), true);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Send message
echo "3. Testing sendSessionMessage...\n";
$request = new Request();
$request->merge([
    'receiver_id' => 8,
    'message' => 'Test message from direct controller test - ' . date('Y-m-d H:i:s')
]);
$request->setMethod('POST');

try {
    $response = $controller->sendSessionMessage($request);
    $data = json_decode($response->getContent(), true);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

echo "=== Test Complete ===\n";
?>
