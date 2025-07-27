<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Log;

echo "=== Comprehensive Search Debug ===\n";

// Start session for the user
session(['user_id' => 179, 'user_role' => 'student']);

// Enable Laravel's error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test individual methods
$searchController = new SearchController();

// Create reflection to access private methods
$reflection = new ReflectionClass($searchController);

// Create a mock user
$user = (object) [
    'user_id' => 179,
    'role' => 'student'
];

$query = 'Nursing';
$limit = 10;

echo "Testing searchAllProgramsForStudent method...\n";
try {
    $method = $reflection->getMethod('searchAllProgramsForStudent');
    $method->setAccessible(true);
    $result = $method->invoke($searchController, $query, $limit, $user);
    echo "SUCCESS: searchAllProgramsForStudent returned " . count($result) . " results\n";
    foreach ($result as $item) {
        echo "  - {$item['type']}: {$item['name']}\n";
    }
} catch (Exception $e) {
    echo "ERROR in searchAllProgramsForStudent: " . $e->getMessage() . "\n";
}

echo "\nTesting searchAllProfessorsForStudent method...\n";
try {
    $method = $reflection->getMethod('searchAllProfessorsForStudent');
    $method->setAccessible(true);
    $result = $method->invoke($searchController, $query, $limit, $user);
    echo "SUCCESS: searchAllProfessorsForStudent returned " . count($result) . " results\n";
    foreach ($result as $item) {
        echo "  - {$item['type']}: {$item['name']}\n";
    }
} catch (Exception $e) {
    echo "ERROR in searchAllProfessorsForStudent: " . $e->getMessage() . "\n";
}

echo "\nTesting searchAll method...\n";
try {
    $method = $reflection->getMethod('searchAll');
    $method->setAccessible(true);
    $result = $method->invoke($searchController, $query, $limit, $user);
    echo "SUCCESS: searchAll returned " . count($result) . " results\n";
} catch (Exception $e) {
    echo "ERROR in searchAll: " . $e->getMessage() . "\n";
}
