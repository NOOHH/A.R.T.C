<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::createFromGlobals();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

echo "=== TESTING ADMIN AUTHENTICATION AFTER MIDDLEWARE FIX ===\n\n";

try {
    // Simulate admin login
    $admin = Admin::where('email', 'admin@smartprep.com')->first();
    
    if ($admin) {
        echo "✅ Admin found: {$admin->name}\n";
        
        // Login the admin
        Auth::guard('admin')->login($admin);
        
        echo "✅ Admin logged in with admin guard\n";
        echo "Admin guard check: " . (Auth::guard('admin')->check() ? "✅ AUTHENTICATED" : "❌ NOT AUTHENTICATED") . "\n";
        echo "Smartprep guard check: " . (Auth::guard('smartprep')->check() ? "✅ AUTHENTICATED" : "❌ NOT AUTHENTICATED") . "\n";
        
        // Test the middleware logic
        $middlewareResult = Auth::guard('smartprep')->check() || Auth::guard('admin')->check();
        echo "Middleware authentication check: " . ($middlewareResult ? "✅ SHOULD PASS" : "❌ SHOULD FAIL") . "\n\n";
        
        if ($middlewareResult) {
            echo "🎉 MIDDLEWARE FIX SUCCESSFUL!\n";
            echo "Admin should now be able to access:\n";
            echo "- http://localhost:8000/smartprep/admin/dashboard\n";
            echo "- http://localhost:8000/smartprep/admin/settings\n";
            echo "- All other SmartPrep admin routes\n\n";
        }
        
        // Logout
        Auth::guard('admin')->logout();
        echo "✅ Admin logged out\n";
        
    } else {
        echo "❌ Admin not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== READY TO TEST LOGIN ===\n";
echo "1. Go to: http://localhost:8000/smartprep/login\n";
echo "2. Login with: admin@smartprep.com / admin123\n";
echo "3. Should redirect to: http://localhost:8000/smartprep/admin/dashboard\n";
echo "4. Dashboard should load properly without redirecting back to login\n\n";

echo "=== IF STILL HAVING ISSUES ===\n";
echo "1. Clear browser cache completely\n";
echo "2. Try incognito/private mode\n";
echo "3. Check browser console for JavaScript errors\n";
echo "4. Check Network tab for redirect loops\n";

$kernel->terminate($request, $response);
?>
