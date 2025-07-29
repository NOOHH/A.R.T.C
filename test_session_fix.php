<?php

// Test script to manually set session and test endpoints
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

echo "Testing Session Fix...\n\n";

// 1. Set session manually
echo "1. Setting session manually...\n";
session(['logged_in' => true]);
session(['professor_id' => 8]);
session(['user_role' => 'professor']);
session(['user_type' => 'professor']);
session(['user_id' => 8]);

echo "Session set. Current session:\n";
echo "logged_in: " . (session('logged_in') ? 'true' : 'false') . "\n";
echo "professor_id: " . session('professor_id') . "\n";
echo "user_role: " . session('user_role') . "\n\n";

// 2. Test the session endpoint
echo "2. Testing session endpoint...\n";
$request = \Illuminate\Http\Request::create('/professor/professor/test-session', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
$content = $response->getContent();
$status = $response->getStatusCode();

echo "Status: $status\n";
echo "Content: $content\n\n";

// 3. Test the modules endpoint
echo "3. Testing modules endpoint...\n";
$request = \Illuminate\Http\Request::create('/professor/modules/by-program?program_id=40', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
$content = $response->getContent();
$status = $response->getStatusCode();

echo "Status: $status\n";
echo "Content: $content\n\n";

// 4. Test the batches endpoint
echo "4. Testing batches endpoint...\n";
$request = \Illuminate\Http\Request::create('/professor/modules/batches?program_id=40', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
$content = $response->getContent();
$status = $response->getStatusCode();

echo "Status: $status\n";
echo "Content: $content\n\n";

// 5. Test the courses endpoint
echo "5. Testing courses endpoint...\n";
$request = \Illuminate\Http\Request::create('/professor/modules/78/courses', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
$content = $response->getContent();
$status = $response->getStatusCode();

echo "Status: $status\n";
echo "Content: $content\n\n";

// 6. Test the module content endpoint
echo "6. Testing module content endpoint...\n";
$request = \Illuminate\Http\Request::create('/professor/modules/78/content', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
$content = $response->getContent();
$status = $response->getStatusCode();

echo "Status: $status\n";
echo "Content: $content\n\n";

// 7. Test the course content endpoint
echo "7. Testing course content endpoint...\n";
$request = \Illuminate\Http\Request::create('/professor/courses/50/content', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
$content = $response->getContent();
$status = $response->getStatusCode();

echo "Status: $status\n";
echo "Content: $content\n\n";

echo "Test completed.\n";
?> 