<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Users ===\n";

$users = App\Models\User::all(['email', 'role']);
foreach ($users as $user) {
    echo $user->email . ' - ' . ($user->role ?? 'no role') . "\n";
}

// Check if there's an admin user
$adminUser = App\Models\User::where('role', 'admin')->first();
if (!$adminUser) {
    echo "\n=== Creating Admin User ===\n";
    
    // Check if there's a user we can promote to admin
    $firstUser = App\Models\User::first();
    if ($firstUser) {
        $firstUser->update(['role' => 'admin']);
        echo "✅ Promoted {$firstUser->email} to admin\n";
    } else {
        echo "❌ No users found to promote\n";
    }
} else {
    echo "\n✅ Admin user exists: {$adminUser->email}\n";
}

?>
