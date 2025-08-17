<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== Resetting SmartPrep Admin Password ===" . PHP_EOL;

// Force main DB
config(['database.default' => 'mysql']);
DB::purge('mysql');
DB::connection('mysql');

// Find admin user
$user = DB::table('users')->where('email', 'admin@smartprep.com')->first();

if ($user) {
    echo "Found admin user: " . $user->email . PHP_EOL;
    
    // Reset password to 'admin123'
    $newPassword = Hash::make('admin123');
    
    $updated = DB::table('users')
        ->where('email', 'admin@smartprep.com')
        ->update(['password' => $newPassword]);
    
    if ($updated) {
        echo "Password updated successfully!" . PHP_EOL;
        echo "New password: admin123" . PHP_EOL;
        echo "Login URL: http://127.0.0.1:8000/smartprep/login" . PHP_EOL;
        echo "Admin Dashboard: http://127.0.0.1:8000/smartprep/admin/dashboard" . PHP_EOL;
    } else {
        echo "Failed to update password." . PHP_EOL;
    }
} else {
    echo "Admin user not found! Creating new admin user..." . PHP_EOL;
    
    // Create new admin user
    $userId = DB::table('users')->insertGetId([
        'name' => 'SmartPrep Admin',
        'email' => 'admin@smartprep.com',
        'role' => 'admin',
        'password' => Hash::make('admin123'),
        'email_verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Created new admin user with ID: " . $userId . PHP_EOL;
    echo "Email: admin@smartprep.com" . PHP_EOL;
    echo "Password: admin123" . PHP_EOL;
    echo "Login URL: http://127.0.0.1:8000/smartprep/login" . PHP_EOL;
}
