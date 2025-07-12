<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Create Test Users ===\n";

// Create test users if they don't exist
$testUsers = [
    [
        'user_firstname' => 'Test',
        'user_lastname' => 'Student1',
        'email' => 'student1@test.com',
        'password' => Hash::make('password'),
        'role' => 'student'
    ],
    [
        'user_firstname' => 'Test',
        'user_lastname' => 'Student2',
        'email' => 'student2@test.com',
        'password' => Hash::make('password'),
        'role' => 'student'
    ],
    [
        'user_firstname' => 'Test',
        'user_lastname' => 'Professor1',
        'email' => 'professor1@test.com',
        'password' => Hash::make('password'),
        'role' => 'professor'
    ],
    [
        'user_firstname' => 'Test',
        'user_lastname' => 'Professor2',
        'email' => 'professor2@test.com',
        'password' => Hash::make('password'),
        'role' => 'professor'
    ]
];

foreach ($testUsers as $userData) {
    $existingUser = User::where('email', $userData['email'])->first();
    
    if (!$existingUser) {
        $user = User::create($userData);
        echo "✅ Created {$userData['role']}: {$userData['email']} (ID: {$user->user_id})\n";
    } else {
        echo "ℹ️  User {$userData['email']} already exists (ID: {$existingUser->user_id})\n";
    }
}

echo "\n=== Test Login Credentials ===\n";
echo "Student1: student1@test.com / password\n";
echo "Student2: student2@test.com / password\n";
echo "Professor1: professor1@test.com / password\n";
echo "Professor2: professor2@test.com / password\n";

echo "\n=== All Users Summary ===\n";
$allUsers = User::all();
foreach ($allUsers as $user) {
    echo "- {$user->role}: {$user->user_firstname} {$user->user_lastname} ({$user->email}) - ID: {$user->user_id}\n";
}
?>
