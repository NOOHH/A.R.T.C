<?php
// Check the actual database structure for professors table
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h2>Database Structure and Data Check</h2>";

// Test 1: Check if professors table exists
if (Schema::hasTable('professors')) {
    echo "<h3>✓ Professors table exists</h3>";
    
    // Test 2: Get table structure
    $columns = Schema::getColumnListing('professors');
    echo "<h3>Table Columns:</h3>";
    echo "<pre>";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    echo "</pre>";
    
    // Test 3: Get actual data from professors table
    try {
        $professors = DB::select('SELECT * FROM professors LIMIT 10');
        echo "<h3>Sample Professor Data:</h3>";
        echo "<pre>";
        foreach ($professors as $professor) {
            echo "Data: " . json_encode($professor, JSON_PRETTY_PRINT) . "\n";
        }
        echo "</pre>";
    } catch (Exception $e) {
        echo "Error fetching professor data: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Search for "robert" in all text fields
    echo "<h3>Search for 'robert' in professors table:</h3>";
    try {
        $searchResults = DB::select("
            SELECT * FROM professors 
            WHERE professor_name LIKE '%robert%' 
            OR professor_first_name LIKE '%robert%' 
            OR professor_last_name LIKE '%robert%'
            OR professor_email LIKE '%robert%'
        ");
        
        echo "<pre>";
        if (empty($searchResults)) {
            echo "No results found for 'robert'\n";
        } else {
            foreach ($searchResults as $professor) {
                echo "Found: " . json_encode($professor, JSON_PRETTY_PRINT) . "\n";
            }
        }
        echo "</pre>";
    } catch (Exception $e) {
        echo "Error searching: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "<h3>❌ Professors table does not exist</h3>";
    
    // Check what tables do exist
    $tables = DB::select('SHOW TABLES');
    echo "<h3>Available Tables:</h3>";
    echo "<pre>";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- $tableName\n";
    }
    echo "</pre>";
}
?>
