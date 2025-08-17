<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Fix User Creation ===\n\n";

// Check users table structure
echo "Users table structure:\n";
$columns = DB::select("SHOW COLUMNS FROM users");
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

echo "\n=== Creating Test User ===\n";

// Check if test user exists
$testUser = User::where('email', 'smartprep@test.com')->first();

if (!$testUser) {
    echo "Creating test user...\n";
    
    // Create user with required fields
    $userData = [
        'name' => 'SmartPrep Test',
        'email' => 'smartprep@test.com',
        'password' => 'password123',
        'role' => 'student',
    ];
    
    try {
        $testUser = User::create($userData);
        echo "✓ Test user created: {$testUser->email} (role: {$testUser->role})\n";
    } catch (Exception $e) {
        echo "✗ Error creating user: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ Test user exists: {$testUser->email} (role: {$testUser->role})\n";
}

echo "\n=== Test Complete ===\n";
echo "You can now login with:\n";
echo "- Email: smartprep@test.com\n";
echo "- Password: password123\n";
echo "This should redirect to the client dashboard.\n";
