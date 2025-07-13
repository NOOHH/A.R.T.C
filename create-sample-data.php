<?php
// Create sample data for testing
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Professor;
use App\Models\Admin;
use App\Models\Director;
use App\Models\User;

echo "<h2>Creating Sample Data for Chat Testing</h2>";

// Create sample professors
try {
    $professor1 = Professor::create([
        'professor_name' => 'Robert San',
        'professor_email' => 'robert.san@example.com',
        'professor_first_name' => 'Robert',
        'professor_last_name' => 'San',
        'professor_password' => bcrypt('password'),
        'admin_id' => 1,
        'professor_archived' => false
    ]);
    echo "✓ Created professor: Robert San<br>";
} catch (Exception $e) {
    echo "Professor creation error: " . $e->getMessage() . "<br>";
}

try {
    $professor2 = Professor::create([
        'professor_name' => 'Jane Smith',
        'professor_email' => 'jane.smith@example.com',
        'professor_first_name' => 'Jane',
        'professor_last_name' => 'Smith',
        'professor_password' => bcrypt('password'),
        'admin_id' => 1,
        'professor_archived' => false
    ]);
    echo "✓ Created professor: Jane Smith<br>";
} catch (Exception $e) {
    echo "Professor creation error: " . $e->getMessage() . "<br>";
}

// Create sample admin
try {
    $admin = Admin::create([
        'admin_name' => 'Test Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password')
    ]);
    echo "✓ Created admin: Test Admin<br>";
} catch (Exception $e) {
    echo "Admin creation error: " . $e->getMessage() . "<br>";
}

// Create sample director
try {
    $director = Director::create([
        'director_name' => 'Test Director',
        'email' => 'director@example.com',
        'password' => bcrypt('password')
    ]);
    echo "✓ Created director: Test Director<br>";
} catch (Exception $e) {
    echo "Director creation error: " . $e->getMessage() . "<br>";
}

// Create sample user
try {
    $user = User::create([
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'password' => bcrypt('password'),
        'role' => 'student'
    ]);
    echo "✓ Created user: Test Student<br>";
} catch (Exception $e) {
    echo "User creation error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Sample Data Created Successfully!</h3>";
echo "<p><a href='/final-chat-test.html'>Go to Chat Test</a></p>";
?>
