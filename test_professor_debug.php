<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Checking Available Professors ===\n";

$professors = User::where('role', 'professor')->limit(5)->get();

echo "Found " . count($professors) . " professors\n\n";

foreach ($professors as $professor) {
    echo "Professor ID: {$professor->user_id}\n";
    echo "Name: {$professor->user_firstname} {$professor->user_lastname}\n";
    echo "Email: {$professor->email}\n";
    echo "---\n";
}

// Now test with a real professor name if any exist
if (count($professors) > 0) {
    $testQuery = $professors[0]->user_firstname;
    echo "\n=== Testing with real professor name: $testQuery ===\n";
    
    $searchResult = User::where('role', 'professor')
        ->where(function($userQuery) use ($testQuery) {
            $userQuery->where('user_firstname', 'like', "%{$testQuery}%")
                ->orWhere('user_lastname', 'like', "%{$testQuery}%")
                ->orWhere('email', 'like', "%{$testQuery}%");
        })
        ->limit(5)
        ->get();
    
    echo "Found " . count($searchResult) . " matching professors\n\n";
    
    foreach ($searchResult as $professor) {
        echo "Professor ID: {$professor->user_id}\n";
        echo "Type: " . gettype($professor) . "\n";
        echo "Class: " . get_class($professor) . "\n";
        
        // Test the problematic access pattern from the search controller
        try {
            $name = (isset($professor->user_firstname) ? $professor->user_firstname : '') . ' ' . (isset($professor->user_lastname) ? $professor->user_lastname : '');
            echo "Constructed Name: '{$name}'\n";
        } catch (Exception $e) {
            echo "Error constructing name: " . $e->getMessage() . "\n";
        }
        
        echo "---\n";
        break;
    }
}

// Test with users table in general
echo "\n=== Testing General Users ===\n";
$users = User::limit(5)->get();
echo "Found " . count($users) . " users\n\n";

foreach ($users as $user) {
    echo "User ID: {$user->user_id}\n";
    echo "Role: {$user->role}\n";
    echo "Name: {$user->user_firstname} {$user->user_lastname}\n";
    echo "---\n";
}
