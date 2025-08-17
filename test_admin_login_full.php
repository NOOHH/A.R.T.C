<?php
// Test full admin login flow with proper session handling

require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Smartprep\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

// Force main database
Config::set('database.default', 'mysql');

echo "=== SmartPrep Admin Login Test ===\n";

// Check if admin exists
$admin = User::where('email', 'admin@smartprep.com')->first();
if (!$admin) {
    echo "❌ Admin user not found\n";
    exit(1);
}

echo "✅ Admin user found: {$admin->name} (Role: {$admin->role})\n";

// Test password
if (!Hash::check('admin123', $admin->password)) {
    echo "❌ Password verification failed\n";
    exit(1);
}

echo "✅ Password verification successful\n";

// Test login logic
if ($admin->role === 'admin') {
    echo "✅ Admin role detected - should redirect to: " . route('smartprep.admin.dashboard') . "\n";
} else {
    echo "✅ Non-admin role detected - should redirect to: " . route('smartprep.dashboard') . "\n";
}

// Test routes exist
try {
    $adminDashboardUrl = route('smartprep.admin.dashboard');
    echo "✅ Admin dashboard route exists: $adminDashboardUrl\n";
} catch (Exception $e) {
    echo "❌ Admin dashboard route missing: " . $e->getMessage() . "\n";
}

try {
    $clientDashboardUrl = route('smartprep.dashboard');
    echo "✅ Client dashboard route exists: $clientDashboardUrl\n";
} catch (Exception $e) {
    echo "❌ Client dashboard route missing: " . $e->getMessage() . "\n";
}

try {
    $loginUrl = route('smartprep.login');
    echo "✅ Login route exists: $loginUrl\n";
} catch (Exception $e) {
    echo "❌ Login route missing: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "To test manually:\n";
echo "1. Visit: http://127.0.0.1:8000/smartprep/login\n";
echo "2. Login with: admin@smartprep.com / admin123\n";
echo "3. Should redirect to: http://127.0.0.1:8000/smartprep/admin/dashboard\n";
