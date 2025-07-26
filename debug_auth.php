<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SESSION DEBUG ===\n";

echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";

echo "\nLaravel Session Data:\n";
echo "session('user_id'): " . (session('user_id') ?? 'null') . "\n";
echo "session('user_name'): " . (session('user_name') ?? 'null') . "\n";
echo "session('user_role'): " . (session('user_role') ?? 'null') . "\n";
echo "session('logged_in'): " . (session('logged_in') ? 'true' : 'false') . "\n";

// Start PHP session to check $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "\nPHP Session Data (\$_SESSION):\n";
echo "\$_SESSION['user_id']: " . ($_SESSION['user_id'] ?? 'null') . "\n";
echo "\$_SESSION['user_name']: " . ($_SESSION['user_name'] ?? 'null') . "\n";
echo "\$_SESSION['user_type']: " . ($_SESSION['user_type'] ?? 'null') . "\n";
echo "\$_SESSION['logged_in']: " . (($_SESSION['logged_in'] ?? false) ? 'true' : 'false') . "\n";

echo "\nAuth::check(): " . (\Illuminate\Support\Facades\Auth::check() ? 'true' : 'false') . "\n";
if (\Illuminate\Support\Facades\Auth::check()) {
    $authUser = \Illuminate\Support\Facades\Auth::user();
    echo "Auth::user()->id: " . ($authUser->id ?? 'null') . "\n";
    echo "Auth::user()->name: " . ($authUser->name ?? 'null') . "\n";
}

echo "\n=== END DEBUG ===\n";
