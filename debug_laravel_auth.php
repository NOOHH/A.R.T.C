<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request
$request = Illuminate\Http\Request::createFromGlobals();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Auth;

echo "=== LARAVEL AUTH DEBUG ===\n\n";

// Check all guards
$guards = ['web', 'smartprep', 'admin', 'student', 'professor', 'director'];

foreach ($guards as $guardName) {
    echo "=== GUARD: $guardName ===\n";
    
    try {
        $guard = Auth::guard($guardName);
        $user = $guard->user();
        $isAuthenticated = $guard->check();
        
        echo "Authenticated: " . ($isAuthenticated ? 'YES' : 'NO') . "\n";
        
        if ($user) {
            echo "User ID: " . ($user->id ?? 'N/A') . "\n";
            echo "User Name: " . ($user->name ?? 'N/A') . "\n";
            echo "User Email: " . ($user->email ?? 'N/A') . "\n";
            echo "User Role: " . ($user->role ?? $user->user_type ?? 'N/A') . "\n";
        } else {
            echo "No user found\n";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Check default guard
echo "=== DEFAULT GUARD ===\n";
try {
    $defaultGuard = Auth::getDefaultDriver();
    echo "Default guard: $defaultGuard\n";
    
    $user = Auth::user();
    $isAuthenticated = Auth::check();
    
    echo "Authenticated: " . ($isAuthenticated ? 'YES' : 'NO') . "\n";
    
    if ($user) {
        echo "User ID: " . ($user->id ?? 'N/A') . "\n";
        echo "User Name: " . ($user->name ?? 'N/A') . "\n";
        echo "User Email: " . ($user->email ?? 'N/A') . "\n";
    } else {
        echo "No user found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== SESSION DATA ===\n";
echo "Session started: " . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "\n";

if (session_status() === PHP_SESSION_ACTIVE) {
    $sessionData = session()->all();
    print_r($sessionData);
} else {
    echo "Session not active\n";
}

$kernel->terminate($request, $response);
?>
