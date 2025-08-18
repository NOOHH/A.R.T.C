<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::createFromGlobals();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Smartprep\User;

echo "=== LARAVEL AUTHENTICATION SYSTEM CHECK ===\n\n";

try {
    // 1. Test Admin Model
    echo "=== ADMIN MODEL TEST ===\n";
    $admin = Admin::where('email', 'admin@smartprep.com')->first();
    if ($admin) {
        echo "✅ Admin model works - Found: {$admin->name} ({$admin->email})\n";
        echo "Admin ID: {$admin->id}\n";
        
        // Test password hash
        $passwordTest = password_verify('admin123', $admin->password);
        echo "Password verification: " . ($passwordTest ? "✅ VALID" : "❌ INVALID") . "\n";
    } else {
        echo "❌ Admin model failed - Could not find admin@smartprep.com\n";
    }
    echo "\n";
    
    // 2. Test User Model  
    echo "=== USER MODEL TEST ===\n";
    $user = User::where('email', 'robert@gmail.com')->first();
    if ($user) {
        echo "✅ User model works - Found: {$user->name} ({$user->email})\n";
        echo "User role: {$user->role}\n";
        echo "User ID: {$user->id}\n";
    } else {
        echo "❌ User model failed - Could not find robert@gmail.com\n";
    }
    echo "\n";
    
    // 3. Test Auth Guards
    echo "=== AUTH GUARDS TEST ===\n";
    $guards = ['web', 'admin', 'smartprep'];
    foreach ($guards as $guardName) {
        try {
            $guard = Auth::guard($guardName);
            echo "✅ Guard '$guardName' initialized successfully\n";
            
            // Test if guard has a user
            $guardUser = $guard->user();
            if ($guardUser) {
                echo "  - Has authenticated user: {$guardUser->name}\n";
            } else {
                echo "  - No authenticated user (expected)\n";
            }
        } catch (Exception $e) {
            echo "❌ Guard '$guardName' failed: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
    
    // 4. Check Auth Configuration
    echo "=== AUTH CONFIGURATION ===\n";
    $authConfig = config('auth');
    echo "Default guard: " . $authConfig['defaults']['guard'] . "\n";
    echo "Available guards: " . implode(', ', array_keys($authConfig['guards'])) . "\n";
    echo "Available providers: " . implode(', ', array_keys($authConfig['providers'])) . "\n";
    echo "\n";
    
    // 5. Test manual authentication
    echo "=== MANUAL AUTHENTICATION TEST ===\n";
    
    // Test admin login
    echo "Testing admin authentication:\n";
    $adminForAuth = Admin::where('email', 'admin@smartprep.com')->first();
    if ($adminForAuth && password_verify('admin123', $adminForAuth->password)) {
        Auth::guard('admin')->login($adminForAuth);
        $loggedInAdmin = Auth::guard('admin')->user();
        if ($loggedInAdmin) {
            echo "✅ Admin authentication successful: {$loggedInAdmin->name}\n";
            Auth::guard('admin')->logout(); // Clean up
        } else {
            echo "❌ Admin authentication failed after login\n";
        }
    } else {
        echo "❌ Admin credentials invalid\n";
    }
    
    // Test user login
    echo "Testing user authentication:\n";
    $userForAuth = User::where('email', 'robert@gmail.com')->first();
    if ($userForAuth) {
        // Note: We don't know robert's password, so just test the model
        echo "✅ User found for authentication: {$userForAuth->name}\n";
        echo "  User has password hash: " . (strlen($userForAuth->password) > 0 ? "YES" : "NO") . "\n";
    } else {
        echo "❌ User not found for authentication\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Laravel Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
?>
