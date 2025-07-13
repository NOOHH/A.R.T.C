<?php
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
?>