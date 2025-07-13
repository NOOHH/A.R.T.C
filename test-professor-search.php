<?php
// Test script to check professor search functionality
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Professor;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h2>Testing Professor Search</h2>";

// Test 1: Check if professors table exists and has data
try {
    $professors = Professor::all();
    echo "<h3>All Professors in Database:</h3>";
    echo "<pre>";
    foreach ($professors as $professor) {
        echo "ID: {$professor->professor_id}, Name: {$professor->professor_name}, Email: {$professor->email}\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "Error fetching professors: " . $e->getMessage() . "\n";
}

// Test 2: Search for "robert"
echo "<h3>Search Results for 'robert':</h3>";
try {
    $searchResults = Professor::where('professor_name', 'like', '%robert%')
                             ->orWhere('email', 'like', '%robert%')
                             ->get();
    
    echo "<pre>";
    if ($searchResults->isEmpty()) {
        echo "No results found for 'robert'\n";
    } else {
        foreach ($searchResults as $professor) {
            echo "Found: ID: {$professor->professor_id}, Name: {$professor->professor_name}, Email: {$professor->email}\n";
        }
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "Error searching for professors: " . $e->getMessage() . "\n";
}

// Test 3: Check for case-insensitive search
echo "<h3>Case-insensitive search for 'ROBERT':</h3>";
try {
    $searchResults = Professor::where('professor_name', 'like', '%ROBERT%')
                             ->orWhere('email', 'like', '%ROBERT%')
                             ->get();
    
    echo "<pre>";
    if ($searchResults->isEmpty()) {
        echo "No results found for 'ROBERT'\n";
    } else {
        foreach ($searchResults as $professor) {
            echo "Found: ID: {$professor->professor_id}, Name: {$professor->professor_name}, Email: {$professor->email}\n";
        }
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "Error searching for professors: " . $e->getMessage() . "\n";
}

// Test 4: Check Professor model fields
echo "<h3>Professor Model Fields:</h3>";
try {
    $professor = Professor::first();
    if ($professor) {
        echo "<pre>";
        echo "Sample professor attributes:\n";
        foreach ($professor->getAttributes() as $key => $value) {
            echo "$key: $value\n";
        }
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "Error getting professor attributes: " . $e->getMessage() . "\n";
}
?>
