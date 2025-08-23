<?php
require_once 'vendor/autoload.php';

// Start Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Testing Professor Database Error Scenarios ===\n\n";

try {
    // Test direct Professor model query
    echo "1. Testing Professor model query directly:\n";
    $professors = \App\Models\Professor::where('professor_email', 'test@example.com')->exists();
    echo "   - Professor::where() query result: " . ($professors ? 'true' : 'false') . "\n";
    
} catch (Exception $e) {
    echo "   - ERROR in Professor model query: " . $e->getMessage() . "\n";
}

try {
    // Test direct DB table query (like in search route)
    echo "\n2. Testing direct DB::table('professors') query:\n";
    $directQuery = \Illuminate\Support\Facades\DB::table('professors')
        ->where('professor_email', 'test@example.com')
        ->exists();
    echo "   - DB::table('professors') query result: " . ($directQuery ? 'true' : 'false') . "\n";
    
} catch (Exception $e) {
    echo "   - ERROR in direct DB query: " . $e->getMessage() . "\n";
}

try {
    // Test the exact search query from routes/web.php
    echo "\n3. Testing exact search query from routes/web.php:\n";
    $searchQuery = \Illuminate\Support\Facades\DB::table('professors')
        ->select('professor_id as id', 'professor_first_name', 'professor_last_name', 'professor_email')
        ->where(function($q) {
            $q->where('professor_first_name', 'LIKE', "%test%")
              ->orWhere('professor_last_name', 'LIKE', "%test%")
              ->orWhere('professor_email', 'LIKE', "%test%");
        })
        ->limit(10)
        ->get();
    echo "   - Search query returned " . count($searchQuery) . " results\n";
    
} catch (Exception $e) {
    echo "   - ERROR in search query: " . $e->getMessage() . "\n";
}

try {
    // Test connection to smartprep database specifically
    echo "\n4. Testing database connection:\n";
    $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    echo "   - Current database: $dbName\n";
    
    // List all tables to confirm professors table doesn't exist
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    echo "   - Total tables found: " . count($tables) . "\n";
    
    $professorTableExists = false;
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if ($tableName === 'professors') {
            $professorTableExists = true;
            break;
        }
    }
    echo "   - Professors table exists: " . ($professorTableExists ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "   - ERROR in database connection test: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
