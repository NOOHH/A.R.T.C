<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\SearchController;
use App\Models\User;
use App\Models\Professor;
use App\Models\Program;

echo "=== Testing Search Functionality ===\n";

// Create a mock user (student)
$user = (object) [
    'user_id' => 179,
    'role' => 'student'
];

// Test searching for "Nursing" which exists in programs
$searchController = new SearchController();

echo "Testing search for 'Nursing'...\n";

try {
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge(['query' => 'Nursing']);
    
    // Start a session for the user
    session(['user_id' => 179, 'user_role' => 'student']);
    
    $response = $searchController->search($request);
    $responseData = $response->getData(true);
    
    echo "Response status: " . ($responseData['success'] ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($responseData['success']) {
        echo "Results count: " . count($responseData['results']) . "\n";
        foreach ($responseData['results'] as $result) {
            echo "- {$result['type']}: {$result['name']}\n";
        }
    } else {
        echo "Error: " . $responseData['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Testing Professor Model Direct Access ===\n";

$professors = Professor::with('programs')->limit(3)->get();
echo "Found " . $professors->count() . " professors\n";

foreach ($professors as $prof) {
    echo "Professor: {$prof->professor_first_name} {$prof->professor_last_name}\n";
    echo "Programs: " . $prof->programs->pluck('program_name')->implode(', ') . "\n";
    echo "---\n";
}
