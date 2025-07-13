<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Debug Chat API ===\n";

// Test database connection
try {
    $connectionName = config('database.default');
    echo "Database Connection: $connectionName\n";
    
    // Test raw query
    $users = DB::table('users')->take(5)->get();
    echo "Found " . count($users) . " users in database\n";
    
    if (count($users) > 0) {
        echo "Sample user data:\n";
        foreach ($users as $user) {
            echo "- ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}, Role: {$user->role}, Email: {$user->email}\n";
        }
    }
    
    // Test User model
    echo "\n=== Using User Model ===\n";
    $userModel = User::take(5)->get();
    echo "Found " . count($userModel) . " users via User model\n";
    
    if (count($userModel) > 0) {
        foreach ($userModel as $user) {
            echo "- ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}, Role: {$user->role}, Email: {$user->email}\n";
        }
    }
    
    // Test filtering by role
    echo "\n=== Test Role Filtering ===\n";
    $roles = ['student', 'professor', 'admin', 'director'];
    
    foreach ($roles as $role) {
        $count = User::where('role', $role)->count();
        echo "- {$role}: {$count} users\n";
    }
    
    // Test search functionality
    echo "\n=== Test Search Functionality ===\n";
    $searchTerm = 'test';
    $searchResults = User::where('user_firstname', 'like', "%{$searchTerm}%")
        ->orWhere('user_lastname', 'like', "%{$searchTerm}%")
        ->orWhere('email', 'like', "%{$searchTerm}%")
        ->get();
    
    echo "Search for '{$searchTerm}' found " . count($searchResults) . " users\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== End Debug ===\n";
?>
