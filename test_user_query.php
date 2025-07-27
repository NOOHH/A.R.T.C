<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Testing User Model Query ===\n";

// Test the exact query used in searchAllProfessorsForStudent
$query = 'Nursing';

echo "Searching for professors with query: $query\n\n";

$professors = User::where('role', 'professor')
    ->where(function($userQuery) use ($query) {
        $userQuery->where('user_firstname', 'like', "%{$query}%")
            ->orWhere('user_lastname', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%");
    })
    ->limit(5)
    ->get();

echo "Found " . count($professors) . " professors\n\n";

foreach ($professors as $professor) {
    echo "Professor ID: {$professor->user_id}\n";
    echo "Type: " . gettype($professor) . "\n";
    echo "Class: " . get_class($professor) . "\n";
    
    // Test property access
    try {
        $firstName = $professor->user_firstname;
        echo "First Name: {$firstName}\n";
    } catch (Exception $e) {
        echo "Error accessing user_firstname: " . $e->getMessage() . "\n";
    }
    
    try {
        $lastName = $professor->user_lastname;
        echo "Last Name: {$lastName}\n";
    } catch (Exception $e) {
        echo "Error accessing user_lastname: " . $e->getMessage() . "\n";
    }
    
    // Show all available attributes
    echo "Available attributes: " . implode(', ', array_keys($professor->getAttributes())) . "\n";
    echo "Raw object properties: " . implode(', ', array_keys((array)$professor)) . "\n";
    
    echo "---\n";
    break; // Just test the first one
}

// Also test a direct search like in the error
echo "\n=== Testing Direct Database Query ===\n";

$results = DB::select("SELECT * FROM users WHERE role = 'professor' LIMIT 1");

if (!empty($results)) {
    $result = $results[0];
    echo "Result type: " . gettype($result) . "\n";
    echo "Result class: " . get_class($result) . "\n";
    
    // Try accessing user_firstname
    try {
        $firstName = $result->user_firstname;
        echo "First Name: {$firstName}\n";
    } catch (Exception $e) {
        echo "Error accessing user_firstname on raw result: " . $e->getMessage() . "\n";
    }
    
    echo "Available properties: " . implode(', ', array_keys((array)$result)) . "\n";
}
