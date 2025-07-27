<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Student Search API Directly ===\n\n";

try {
    // Simulate a student user
    $student = \App\Models\User::where('role', 'student')->first();
    
    if (!$student) {
        echo "No student user found in database!\n";
        exit;
    }
    
    echo "Testing with student: {$student->user_firstname} {$student->user_lastname} (ID: {$student->user_id})\n";
    
    // Create a search controller instance
    $searchController = new \App\Http\Controllers\SearchController();
    
    // Create a mock request
    $request = new \Illuminate\Http\Request(['query' => 'nursing']);
    
    // Simulate authentication
    \Illuminate\Support\Facades\Auth::login($student);
    
    // Call the search method
    $response = $searchController->search($request);
    $data = $response->getData(true);
    
    echo "\nSearch Response:\n";
    echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "Results count: " . count($data['results'] ?? []) . "\n";
    
    if (isset($data['results']) && count($data['results']) > 0) {
        foreach ($data['results'] as $result) {
            echo "- {$result['type']}: {$result['name']}\n";
            if (isset($result['description'])) {
                echo "  Description: {$result['description']}\n";
            }
        }
    } else {
        echo "No results found!\n";
        if (isset($data['message'])) {
            echo "Message: {$data['message']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
