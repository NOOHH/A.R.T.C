<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    echo "=== CREATING MISSING ADMIN USER ===\n\n";
    
    // Switch to tenant database
    config(['database.default' => 'tenant']);
    config(['database.connections.tenant.database' => 'smartprep_artc']);
    DB::purge('tenant');
    
    echo "Switched to tenant database: smartprep_artc\n";
    
    // Check if admin@artc.com exists in directors table
    $existingDirector = DB::table('directors')->where('directors_email', 'admin@artc.com')->first();
    
    if ($existingDirector) {
        echo "✅ admin@artc.com already exists in directors table\n";
        echo "Director ID: {$existingDirector->id}\n";
        echo "Name: {$existingDirector->directors_first_name} {$existingDirector->directors_last_name}\n";
    } else {
        echo "❌ admin@artc.com does NOT exist in directors table\n";
        echo "Creating admin director...\n";
        
        // Create the admin director
        $directorId = DB::table('directors')->insertGetId([
            'directors_name' => 'Admin ARTC',
            'directors_first_name' => 'Admin',
            'directors_last_name' => 'ARTC',
            'directors_email' => 'admin@artc.com',
            'directors_password' => Hash::make('admin123'), // You should change this password
            'has_all_program_access' => 1,
            'directors_archived' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Admin director created with ID: {$directorId}\n";
    }
    
    // Also check if admin exists in admins table
    $existingAdmin = DB::table('admins')->where('email', 'admin@artc.com')->first();
    
    if ($existingAdmin) {
        echo "✅ admin@artc.com already exists in admins table\n";
    } else {
        echo "❌ admin@artc.com does NOT exist in admins table\n";
        echo "Creating admin user...\n";
        
        // Create the admin user
        $adminId = DB::table('admins')->insertGetId([
            'admin_name' => 'Admin ARTC',
            'email' => 'admin@artc.com',
            'password' => Hash::make('admin123'), // You should change this password
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Admin user created with ID: {$adminId}\n";
    }
    
    echo "\n=== TESTING LOGIN CREDENTIALS ===\n";
    
    // Test the director login
    $director = DB::table('directors')->where('directors_email', 'admin@artc.com')->first();
    if ($director) {
        echo "✅ Director found: {$director->directors_first_name} {$director->directors_last_name}\n";
        echo "   Email: {$director->directors_email}\n";
        echo "   Password hash exists: " . (!empty($director->directors_password) ? 'Yes' : 'No') . "\n";
    }
    
    // Test the admin login
    $admin = DB::table('admins')->where('email', 'admin@artc.com')->first();
    if ($admin) {
        echo "✅ Admin found: {$admin->admin_name}\n";
        echo "   Email: {$admin->email}\n";
        echo "   Password hash exists: " . (!empty($admin->password) ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n✅ Setup complete! You can now login with:\n";
    echo "Email: admin@artc.com\n";
    echo "Password: admin123\n";
    echo "\n⚠️  IMPORTANT: Change the password after first login!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
