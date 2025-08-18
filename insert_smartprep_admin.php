<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    echo "=== INSERTING SMARTPREP ADMIN USER ===\n\n";
    
    // Switch to main database (smartprep)
    config(['database.default' => 'mysql']);
    DB::purge('mysql');
    
    echo "Switched to main database: smartprep\n";
    
    // Check if smartprep@gmail.com exists in users table
    $existingUser = DB::table('users')->where('email', 'smartprep@gmail.com')->first();
    
    if ($existingUser) {
        echo "✅ smartprep@gmail.com already exists in users table\n";
        echo "User ID: {$existingUser->id}\n";
        echo "Name: {$existingUser->name}\n";
        echo "Role: {$existingUser->role}\n";
        
        // Update password if needed
        if (!Hash::check('admin123', $existingUser->password)) {
            echo "Updating password...\n";
            DB::table('users')
                ->where('email', 'smartprep@gmail.com')
                ->update([
                    'password' => Hash::make('admin123'),
                    'updated_at' => now()
                ]);
            echo "✅ Password updated successfully\n";
        } else {
            echo "✅ Password is already correct\n";
        }
    } else {
        echo "❌ smartprep@gmail.com does NOT exist in users table\n";
        echo "Creating admin user...\n";
        
        // Create the admin user
        $userId = DB::table('users')->insertGetId([
            'name' => 'SmartPrep Admin',
            'email' => 'smartprep@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Admin user created with ID: {$userId}\n";
    }
    
    // Also check if admin exists in admins table (if it exists)
    try {
        $existingAdmin = DB::table('admins')->where('email', 'smartprep@gmail.com')->first();
        
        if ($existingAdmin) {
            echo "✅ smartprep@gmail.com already exists in admins table\n";
            echo "Admin ID: {$existingAdmin->id}\n";
            echo "Name: {$existingAdmin->admin_name}\n";
            
            // Update password if needed
            if (!Hash::check('admin123', $existingAdmin->password)) {
                echo "Updating admin password...\n";
                DB::table('admins')
                    ->where('email', 'smartprep@gmail.com')
                    ->update([
                        'password' => Hash::make('admin123'),
                        'updated_at' => now()
                    ]);
                echo "✅ Admin password updated successfully\n";
            } else {
                echo "✅ Admin password is already correct\n";
            }
        } else {
            echo "❌ smartprep@gmail.com does NOT exist in admins table\n";
            echo "Creating admin record...\n";
            
            // Create the admin record
            $adminId = DB::table('admins')->insertGetId([
                'admin_name' => 'SmartPrep Admin',
                'email' => 'smartprep@gmail.com',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "✅ Admin record created with ID: {$adminId}\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Admins table might not exist or have different structure: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== TESTING LOGIN CREDENTIALS ===\n";
    
    // Test the user login
    $user = DB::table('users')->where('email', 'smartprep@gmail.com')->first();
    if ($user) {
        echo "✅ User found: {$user->name}\n";
        echo "   Email: {$user->email}\n";
        echo "   Role: {$user->role}\n";
        echo "   Password hash exists: " . (!empty($user->password) ? 'Yes' : 'No') . "\n";
        
        // Test password verification
        if (Hash::check('admin123', $user->password)) {
            echo "   ✅ Password verification successful\n";
        } else {
            echo "   ❌ Password verification failed\n";
        }
    }
    
    echo "\n✅ Setup complete! You can now login with:\n";
    echo "Email: smartprep@gmail.com\n";
    echo "Password: admin123\n";
    echo "\n⚠️  IMPORTANT: Change the password after first login!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
