use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Smartprep\User;
use Illuminate\Support\Facades\Hash;

Config::set('database.default', 'mysql');

echo "=== SmartPrep Admin Login Test ===\n";

$admin = User::where('email', 'admin@smartprep.com')->first();
if (!$admin) {
    echo "❌ Admin user not found\n";
} else {
    echo "✅ Admin user found: {$admin->name} (Role: {$admin->role})\n";
    
    if (Hash::check('admin123', $admin->password)) {
        echo "✅ Password verification successful\n";
        
        if ($admin->role === 'admin') {
            echo "✅ Admin role detected - should redirect to admin dashboard\n";
        } else {
            echo "✅ Non-admin role detected - should redirect to client dashboard\n";
        }
    } else {
        echo "❌ Password verification failed\n";
    }
}

echo "\nTest routes:\n";
echo "Admin dashboard: " . route('smartprep.admin.dashboard') . "\n";
echo "Client dashboard: " . route('smartprep.dashboard') . "\n";
echo "Login page: " . route('smartprep.login') . "\n";
