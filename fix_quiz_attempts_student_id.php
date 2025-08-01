<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Fixing quiz_attempts table student_id column type...\n";

try {
    // Check current structure
    echo "Current quiz_attempts structure:\n";
    $columns = DB::select('DESCRIBE quiz_attempts');
    foreach($columns as $col) {
        if ($col->Field === 'student_id') {
            echo "Current: " . $col->Field . ' (' . $col->Type . ')' . "\n";
        }
    }
    
    // Modify the column to match students table
    DB::statement('ALTER TABLE quiz_attempts MODIFY COLUMN student_id VARCHAR(30)');
    
    echo "Modified quiz_attempts.student_id to VARCHAR(30)\n";
    
    // Verify the change
    echo "New quiz_attempts structure:\n";
    $columns = DB::select('DESCRIBE quiz_attempts');
    foreach($columns as $col) {
        if ($col->Field === 'student_id') {
            echo "New: " . $col->Field . ' (' . $col->Type . ')' . "\n";
        }
    }
    
    echo "Fix completed successfully!\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
