<?php
echo "=== TESTING DATABASE CONNECTION FIX ===\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Admin model with explicit connection...\n";
    $admin = \App\Models\Admin::first();
    if ($admin) {
        echo "✅ Admin model working: {$admin->name} (ID: {$admin->id})\n";
    } else {
        echo "❌ No admin found\n";
    }
    
    echo "\nTesting SmartPrep User model with explicit connection...\n";
    $user = \App\Models\Smartprep\User::first();
    if ($user) {
        echo "✅ SmartPrep User model working: {$user->name} (ID: {$user->id})\n";
    } else {
        echo "❌ No SmartPrep user found\n";
    }
    
    echo "\nTesting authentication guards...\n";
    $adminGuard = \Illuminate\Support\Facades\Auth::guard('admin');
    $smartprepGuard = \Illuminate\Support\Facades\Auth::guard('smartprep');
    
    echo "✅ Admin guard initialized successfully\n";
    echo "✅ SmartPrep guard initialized successfully\n";
    
    echo "\n✅ Database connection fix appears to be working!\n";
    echo "Try accessing the homepage now: http://127.0.0.1:8000/\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
