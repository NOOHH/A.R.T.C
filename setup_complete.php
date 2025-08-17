<?php

echo "=== A.R.T.C Multi-Tenant Setup ===\n\n";

// Step 1: Check if databases exist
echo "Step 1: Checking databases...\n";
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    
    // Check if smartprep database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'smartprep'");
    if ($stmt->rowCount() > 0) {
        echo "✓ smartprep database exists\n";
    } else {
        echo "Creating smartprep database...\n";
        $pdo->exec("CREATE DATABASE smartprep");
        echo "✓ smartprep database created\n";
    }
    
    // Check if smartprep_artc database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'smartprep_artc'");
    if ($stmt->rowCount() > 0) {
        echo "✓ smartprep_artc database exists\n";
    } else {
        echo "Creating smartprep_artc database...\n";
        $pdo->exec("CREATE DATABASE smartprep_artc");
        echo "✓ smartprep_artc database created\n";
    }
    
} catch (Exception $e) {
    echo "✗ Database check failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Bootstrap Laravel
echo "\nStep 2: Bootstrapping Laravel...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✓ Laravel bootstrapped successfully\n";
} catch (Exception $e) {
    echo "✗ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 3: Run migrations on main database
echo "\nStep 3: Running migrations on main database...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--database' => 'mysql']);
    echo "✓ Main database migrations completed\n";
} catch (Exception $e) {
    echo "✗ Main database migrations failed: " . $e->getMessage() . "\n";
}

// Step 4: Run seeders on main database
echo "\nStep 4: Running seeders on main database...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--database' => 'mysql']);
    echo "✓ Main database seeders completed\n";
} catch (Exception $e) {
    echo "✗ Main database seeders failed: " . $e->getMessage() . "\n";
}

// Step 5: Run migrations on tenant database
echo "\nStep 5: Running migrations on tenant database...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--database' => 'tenant']);
    echo "✓ Tenant database migrations completed\n";
} catch (Exception $e) {
    echo "✗ Tenant database migrations failed: " . $e->getMessage() . "\n";
}

// Step 6: Create client user
echo "\nStep 6: Creating client user...\n";
try {
    $user = \App\Models\User::create([
        'name' => 'A.R.T.C Client',
        'email' => 'artc@gmail.com',
        'password' => bcrypt('artc12345678'),
        'role' => 'client',
        'email_verified_at' => now(),
    ]);
    echo "✓ Client user created successfully\n";
    echo "  Email: artc@gmail.com\n";
    echo "  Password: artc12345678\n";
} catch (Exception $e) {
    echo "✗ Client user creation failed: " . $e->getMessage() . "\n";
}

// Step 7: Clear caches
echo "\nStep 7: Clearing caches...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✓ Caches cleared successfully\n";
} catch (Exception $e) {
    echo "✗ Cache clearing failed: " . $e->getMessage() . "\n";
}

echo "\n=== Setup Complete ===\n";
echo "Main database (smartprep): Landing page and admin management\n";
echo "Tenant database (smartprep_artc): Client/tenant data\n";
echo "Access tenant at: /t/artc\n";
echo "Client login: artc@gmail.com / artc12345678\n";
