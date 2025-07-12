<?php
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
