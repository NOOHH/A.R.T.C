<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request
$request = Illuminate\Http\Request::createFromGlobals();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Auth;

echo "=== SMARTPREP AUTH TEST ===\n\n";

// Test smartprep guard specifically
$smartprepGuard = Auth::guard('smartprep');
$user = $smartprepGuard->user();
$isAuthenticated = $smartprepGuard->check();

echo "SmartPrep Guard:\n";
echo "Authenticated: " . ($isAuthenticated ? 'YES' : 'NO') . "\n";

if ($user) {
    echo "User ID: " . ($user->id ?? 'N/A') . "\n";
    echo "User Name: " . ($user->name ?? 'N/A') . "\n";
    echo "User Email: " . ($user->email ?? 'N/A') . "\n";
    echo "User Role: " . ($user->role ?? 'N/A') . "\n";
} else {
    echo "No user found\n";
}

echo "\n=== INSTRUCTIONS ===\n";
echo "If no user found:\n";
echo "1. Go to /smartprep/login\n";
echo "2. Login with: smartprep@gmail.com or admin@smartprep.com\n";
echo "3. Check if you get redirected to admin dashboard\n";
echo "4. Then refresh the homepage to see if authentication works\n\n";

$kernel->terminate($request, $response);
?>
