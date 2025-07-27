<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\SearchController;

echo "=== Testing Various Search Queries ===\n";

$searchController = new SearchController();

// Start session for the user
session(['user_id' => 179, 'user_role' => 'student']);

$testQueries = [
    'Nursing',
    'Mechanical',
    'Engineer',
    'robert',  // Professor name
    'ME',      // Short query  
    'Civil'    // Non-existent program
];

foreach ($testQueries as $query) {
    echo "\n--- Testing query: '$query' ---\n";
    
    try {
        $request = new \Illuminate\Http\Request();
        $request->merge(['query' => $query]);
        
        $response = $searchController->search($request);
        $responseData = $response->getData(true);
        
        if ($responseData['success']) {
            echo "✅ SUCCESS: Found " . count($responseData['results']) . " results\n";
            foreach ($responseData['results'] as $result) {
                echo "   - {$result['type']}: {$result['name']}\n";
                if (isset($result['professors']) && !empty($result['professors'])) {
                    echo "     Professors: " . implode(', ', $result['professors']) . "\n";
                }
            }
        } else {
            echo "❌ FAILED: " . $responseData['message'] . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Test Complete ===\n";
