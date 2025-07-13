<?php
// Comprehensive test of the chat search functionality
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Professor;
use App\Models\User;
use App\Models\Admin;
use App\Models\Director;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h2>Comprehensive Chat Search Test</h2>";

// Test 1: Check what tables exist
echo "<h3>Available Tables:</h3>";
try {
    $tables = DB::select('SHOW TABLES');
    echo "<pre>";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- $tableName\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "Error getting tables: " . $e->getMessage() . "\n";
}

// Test 2: Check each user type table
$userTypes = [
    'users' => ['name', 'email', 'role'],
    'professors' => ['professor_name', 'professor_email', 'professor_first_name', 'professor_last_name'],
    'admins' => ['admin_name', 'email', 'name'],
    'directors' => ['director_name', 'email', 'name']
];

foreach ($userTypes as $tableName => $fields) {
    echo "<h3>Table: $tableName</h3>";
    
    // Check if table exists
    try {
        $exists = DB::select("SHOW TABLES LIKE '$tableName'");
        if (empty($exists)) {
            echo "❌ Table $tableName does not exist\n";
            continue;
        }
        
        echo "✓ Table $tableName exists\n";
        
        // Get table structure
        $columns = DB::select("SHOW COLUMNS FROM $tableName");
        echo "<strong>Columns:</strong> ";
        $columnNames = [];
        foreach ($columns as $column) {
            $columnNames[] = $column->Field;
        }
        echo implode(', ', $columnNames) . "\n";
        
        // Get sample data
        $sampleData = DB::select("SELECT * FROM $tableName LIMIT 3");
        echo "<strong>Sample Data:</strong>\n";
        echo "<pre>";
        foreach ($sampleData as $row) {
            echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
        }
        echo "</pre>";
        
        // Search for "robert" in this table
        $searchQuery = "SELECT * FROM $tableName WHERE ";
        $searchConditions = [];
        foreach ($fields as $field) {
            if (in_array($field, $columnNames)) {
                $searchConditions[] = "$field LIKE '%robert%'";
            }
        }
        
        if (!empty($searchConditions)) {
            $searchQuery .= implode(' OR ', $searchConditions);
            echo "<strong>Search for 'robert':</strong>\n";
            echo "<pre>";
            try {
                $searchResults = DB::select($searchQuery);
                if (empty($searchResults)) {
                    echo "No results found for 'robert'\n";
                } else {
                    foreach ($searchResults as $result) {
                        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
                    }
                }
            } catch (Exception $e) {
                echo "Error searching: " . $e->getMessage() . "\n";
            }
            echo "</pre>";
        }
        
        echo "<hr>";
        
    } catch (Exception $e) {
        echo "Error with table $tableName: " . $e->getMessage() . "\n";
    }
}

// Test 3: Direct model access test
echo "<h3>Direct Model Access Test</h3>";

// Test Professor model
echo "<strong>Professor Model Test:</strong>\n";
try {
    $professorCount = Professor::count();
    echo "Total professors: $professorCount\n";
    
    if ($professorCount > 0) {
        $sampleProfessor = Professor::first();
        echo "Sample professor attributes:\n";
        echo "<pre>";
        foreach ($sampleProfessor->getAttributes() as $key => $value) {
            echo "$key: $value\n";
        }
        echo "</pre>";
        
        // Try the search that should work
        $searchResults = Professor::where('professor_name', 'like', '%robert%')
                                ->orWhere('professor_email', 'like', '%robert%')
                                ->get();
        
        echo "Search results for 'robert':\n";
        echo "<pre>";
        if ($searchResults->isEmpty()) {
            echo "No results found\n";
        } else {
            foreach ($searchResults as $professor) {
                echo "Found: " . json_encode($professor->getAttributes(), JSON_PRETTY_PRINT) . "\n";
            }
        }
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "Error with Professor model: " . $e->getMessage() . "\n";
}

?>
