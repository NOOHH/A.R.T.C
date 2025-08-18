<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::createFromGlobals();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Auth;

echo "=== FINAL AUTHENTICATION & BRAND TEST ===\n\n";

// Test all guards
$guards = ['web', 'smartprep', 'admin'];
$authenticatedUser = null;
$authenticatedGuard = null;

foreach ($guards as $guardName) {
    $guard = Auth::guard($guardName);
    if ($guard->check()) {
        $authenticatedUser = $guard->user();
        $authenticatedGuard = $guardName;
        echo "✅ Authenticated via '$guardName' guard\n";
        echo "   User: {$authenticatedUser->name} ({$authenticatedUser->email})\n\n";
        break;
    }
}

if (!$authenticatedUser) {
    echo "❌ No authentication found in any guard\n\n";
}

// Test navbar brand name
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'smartprep';
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    $stmt = $pdo->prepare("SELECT setting_value FROM ui_settings WHERE section = 'navbar' AND setting_key = 'brand_name'");
    $stmt->execute();
    $brandName = $stmt->fetchColumn();
    
    echo "=== NAVBAR BRAND NAME ===\n";
    echo "Database value: '$brandName'\n\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n\n";
}

echo "=== STATUS SUMMARY ===\n";
echo "Authentication: " . ($authenticatedUser ? "✅ Working ($authenticatedGuard guard)" : "❌ Not working") . "\n";
echo "Brand Name: " . (isset($brandName) ? "✅ Set in database" : "❌ Not found") . "\n\n";

if (!$authenticatedUser) {
    echo "=== TO FIX AUTHENTICATION ===\n";
    echo "1. Go to: http://localhost:8000/smartprep/login\n";
    echo "2. Login with: test@admin.com / admin123\n";
    echo "3. You should be redirected to admin dashboard\n";
    echo "4. Check navbar - should show 'Test Admin' instead of 'Guest'\n";
    echo "5. Brand name should show: '$brandName'\n\n";
}

if ($authenticatedUser) {
    echo "=== AUTHENTICATION IS WORKING! ===\n";
    echo "User should see their name in navbar instead of 'Guest'\n";
    echo "Brand name should update when changed in SmartPrep admin settings\n\n";
}

$kernel->terminate($request, $response);
?>
