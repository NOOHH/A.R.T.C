<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== Checking content_items table structure ===\n";
    
    // Check if table exists
    $tableExists = Schema::hasTable('content_items');
    echo "Table exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
    
    if ($tableExists) {
        // Get column listing
        $columns = Schema::getColumnListing('content_items');
        echo "\nColumns in content_items table:\n";
        foreach ($columns as $column) {
            echo "- $column\n";
        }
        
        // Check for specific columns
        echo "\nChecking specific columns:\n";
        echo "Has 'id': " . (Schema::hasColumn('content_items', 'id') ? 'YES' : 'NO') . "\n";
        echo "Has 'content_id': " . (Schema::hasColumn('content_items', 'content_id') ? 'YES' : 'NO') . "\n";
        
        // Get table info using raw query
        echo "\n=== Raw table structure ===\n";
        $structure = DB::select('DESCRIBE content_items');
        foreach ($structure as $col) {
            echo "Field: {$col->Field}, Type: {$col->Type}, Key: {$col->Key}\n";
        }
    }
    
    echo "\n=== Checking assignment_submissions table ===\n";
    $submissionTableExists = Schema::hasTable('assignment_submissions');
    echo "Table exists: " . ($submissionTableExists ? 'YES' : 'NO') . "\n";
    
    if ($submissionTableExists) {
        $submissionColumns = Schema::getColumnListing('assignment_submissions');
        echo "\nColumns in assignment_submissions table:\n";
        foreach ($submissionColumns as $column) {
            echo "- $column\n";
        }
        
        echo "\nChecking content_id in assignment_submissions:\n";
        echo "Has 'content_id': " . (Schema::hasColumn('assignment_submissions', 'content_id') ? 'YES' : 'NO') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
