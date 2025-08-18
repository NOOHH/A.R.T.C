<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Smartprep\User;
use Illuminate\Support\Facades\Hash;

echo "=== LOGIN PROCESS SIMULATION ===\n\n";

try {
    // Simulate admin login
    echo "=== TESTING ADMIN LOGIN ===\n";
    $adminEmail = 'admin@smartprep.com';
    $adminPassword = 'admin123';
    
    echo "Attempting to login admin: $adminEmail\n";
    
    // Find admin user
    $admin = Admin::where('email', $adminEmail)->first();
    if (!$admin) {
        echo "❌ Admin not found in database\n";
    } else {
        echo "✅ Admin found: {$admin->name}\n";
        
        // Check password
        $passwordValid = Hash::check($adminPassword, $admin->password);
        echo "Password check: " . ($passwordValid ? "✅ VALID" : "❌ INVALID") . "\n";
        
        if ($passwordValid) {
            // Test login
            Auth::guard('admin')->login($admin);
            $loggedInUser = Auth::guard('admin')->user();
            
            if ($loggedInUser) {
                echo "✅ LOGIN SUCCESSFUL!\n";
                echo "Logged in as: {$loggedInUser->name} (ID: {$loggedInUser->id})\n";
                echo "Guard check: " . (Auth::guard('admin')->check() ? "AUTHENTICATED" : "NOT AUTHENTICATED") . "\n";
                
                // Test what happens when we redirect
                echo "Redirect should go to: smartprep.admin.dashboard\n";
                
                // Logout for next test
                Auth::guard('admin')->logout();
                echo "✅ Logout successful\n";
            } else {
                echo "❌ Login failed - no user returned after login\n";
            }
        }
    }
    
    echo "\n=== TESTING CLIENT LOGIN ===\n";
    $clientEmail = 'robert@gmail.com';
    $clientPassword = 'robert123'; // This is what's in your reset script
    
    echo "Attempting to login client: $clientEmail\n";
    
    // Find client user
    $client = User::where('email', $clientEmail)->first();
    if (!$client) {
        echo "❌ Client not found in database\n";
    } else {
        echo "✅ Client found: {$client->name} (Role: {$client->role})\n";
        
        // Check password (we need to see if robert123 is the right password)
        $passwordValid = Hash::check($clientPassword, $client->password);
        echo "Password 'robert123' check: " . ($passwordValid ? "✅ VALID" : "❌ INVALID") . "\n";
        
        if (!$passwordValid) {
            // Let's check what the actual password might be
            echo "\nTrying other common passwords...\n";
            $commonPasswords = ['password', '123456', 'robert', 'test123', 'client123'];
            foreach ($commonPasswords as $testPass) {
                if (Hash::check($testPass, $client->password)) {
                    echo "✅ Password '$testPass' is VALID!\n";
                    $clientPassword = $testPass;
                    $passwordValid = true;
                    break;
                }
            }
        }
        
        if ($passwordValid) {
            // Test login
            Auth::guard('smartprep')->login($client);
            $loggedInUser = Auth::guard('smartprep')->user();
            
            if ($loggedInUser) {
                echo "✅ CLIENT LOGIN SUCCESSFUL!\n";
                echo "Logged in as: {$loggedInUser->name} (ID: {$loggedInUser->id})\n";
                echo "Guard check: " . (Auth::guard('smartprep')->check() ? "AUTHENTICATED" : "NOT AUTHENTICATED") . "\n";
                echo "Redirect should go to: smartprep.dashboard\n";
                
                // Logout for cleanup
                Auth::guard('smartprep')->logout();
                echo "✅ Logout successful\n";
            } else {
                echo "❌ Login failed - no user returned after login\n";
            }
        } else {
            echo "❌ Could not find valid password for client\n";
            echo "Need to reset client password. Current hash: " . substr($client->password, 0, 30) . "...\n";
        }
    }
    
    echo "\n=== CHECKING LOGIN CONTROLLER ===\n";
    // Check if login controller exists
    if (class_exists('App\Http\Controllers\Smartprep\Auth\LoginController')) {
        echo "✅ LoginController class exists\n";
    } else {
        echo "❌ LoginController class not found\n";
    }
    
    echo "\n=== RECOMMENDATIONS ===\n";
    if ($admin && $passwordValid) {
        echo "Admin login should work. If it doesn't:\n";
        echo "1. Check if you're going to the right URL: http://localhost:8000/smartprep/login\n";
        echo "2. Check browser console for JavaScript errors\n";
        echo "3. Check Laravel logs for errors\n";
        echo "4. Clear browser cache and try incognito mode\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
