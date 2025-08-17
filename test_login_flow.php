<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Login Flow Test ===\n\n";

// Check if test user exists
$testUser = User::where('email', 'smartprep@test.com')->first();

if (!$testUser) {
    echo "Creating test user...\n";
    $testUser = User::create([
        'user_firstname' => 'SmartPrep',
        'user_lastname' => 'Test',
        'email' => 'smartprep@test.com',
        'password' => 'password123',
        'role' => 'student', // This should go to client dashboard
    ]);
    echo "Test user created: {$testUser->email} (role: {$testUser->role})\n";
} else {
    echo "Test user exists: {$testUser->email} (role: {$testUser->role})\n";
}

// Test the DashboardController logic
echo "\n=== Testing DashboardController Logic ===\n";

// Simulate admin user
$adminUser = new User();
$adminUser->email = 'admin@smartprep.com';
$adminUser->role = 'admin';

echo "Admin user check:\n";
echo "- Email: {$adminUser->email}\n";
echo "- Role: {$adminUser->role}\n";
echo "- Is admin by email: " . ($adminUser->email === 'admin@smartprep.com' ? 'YES' : 'NO') . "\n";
echo "- Is admin by role: " . ($adminUser->role === 'admin' ? 'YES' : 'NO') . "\n";

// Simulate regular user
$regularUser = new User();
$regularUser->email = 'smartprep@test.com';
$regularUser->role = 'student';

echo "\nRegular user check:\n";
echo "- Email: {$regularUser->email}\n";
echo "- Role: {$regularUser->role}\n";
echo "- Is admin by email: " . ($regularUser->email === 'admin@smartprep.com' ? 'YES' : 'NO') . "\n";
echo "- Is admin by role: " . ($regularUser->role === 'admin' ? 'YES' : 'NO') . "\n";

echo "\n=== Test Complete ===\n";
echo "You can now login with:\n";
echo "- Email: smartprep@test.com\n";
echo "- Password: password123\n";
echo "This should redirect to the client dashboard.\n";

