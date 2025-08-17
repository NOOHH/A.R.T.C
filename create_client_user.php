<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating client user...\n";

try {
    // Create the client user
    $user = \App\Models\User::create([
        'name' => 'A.R.T.C Client',
        'email' => 'artc@gmail.com',
        'password' => bcrypt('artc12345678'),
        'role' => 'client',
        'email_verified_at' => now(),
    ]);
    
    echo "✓ Client user created successfully!\n";
    echo "Email: artc@gmail.com\n";
    echo "Password: artc12345678\n";
    echo "Role: client\n";
    
} catch (Exception $e) {
    echo "✗ Error creating user: " . $e->getMessage() . "\n";
}
