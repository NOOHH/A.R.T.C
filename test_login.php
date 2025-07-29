<?php

// Test script to simulate professor login and test session
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Session;
use App\Models\Professor;

echo "Testing Professor Login and Session...\n\n";

// 1. Test professor login
echo "1. Testing professor login...\n";
$professor = Professor::find(8);
if ($professor) {
    echo "Professor found: " . $professor->professor_name . "\n";
    echo "Professor email: " . $professor->professor_email . "\n";
    
    // Simulate login by setting session
    session(['logged_in' => true]);
    session(['professor_id' => $professor->professor_id]);
    session(['user_role' => 'professor']);
    session(['user_type' => 'professor']);
    session(['user_id' => $professor->professor_id]);
    session(['user_name' => $professor->full_name]);
    session(['user_email' => $professor->professor_email]);
    
    echo "Session set successfully\n";
} else {
    echo "Professor not found!\n";
    exit(1);
}

// 2. Test session persistence
echo "\n2. Testing session persistence...\n";
echo "logged_in: " . (session('logged_in') ? 'true' : 'false') . "\n";
echo "professor_id: " . session('professor_id') . "\n";
echo "user_role: " . session('user_role') . "\n";

// 3. Test the modules endpoint with session
echo "\n3. Testing modules endpoint with session...\n";
$request = \Illuminate\Http\Request::create('/professor/modules/by-program?program_id=40', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
$content = $response->getContent();
$status = $response->getStatusCode();

echo "Status: $status\n";
if ($status == 200) {
    $data = json_decode($content, true);
    echo "Modules count: " . count($data) . "\n";
} else {
    echo "Content: $content\n";
}

echo "\nTest completed.\n";
?> 