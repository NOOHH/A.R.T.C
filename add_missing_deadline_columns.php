<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    echo "Adding missing deadline columns to quizzes table...\n\n";
    
    // Check if columns already exist
    $columns = DB::select('DESCRIBE quizzes');
    $existingColumns = array_column($columns, 'Field');
    
    $columnsToAdd = [];
    
    if (!in_array('has_deadline', $existingColumns)) {
        $columnsToAdd[] = 'has_deadline';
    }
    
    if (!in_array('due_date', $existingColumns)) {
        $columnsToAdd[] = 'due_date';
    }
    
    if (empty($columnsToAdd)) {
        echo "✅ All deadline columns already exist!\n";
    } else {
        echo "Missing columns found: " . implode(', ', $columnsToAdd) . "\n";
        echo "Adding columns...\n\n";
        
        // Add has_deadline column
        if (in_array('has_deadline', $columnsToAdd)) {
            DB::statement('ALTER TABLE quizzes ADD COLUMN has_deadline TINYINT(1) DEFAULT 0 AFTER max_attempts');
            echo "✅ Added has_deadline column (TINYINT(1) DEFAULT 0)\n";
        }
        
        // Add due_date column
        if (in_array('due_date', $columnsToAdd)) {
            DB::statement('ALTER TABLE quizzes ADD COLUMN due_date DATETIME NULL AFTER has_deadline');
            echo "✅ Added due_date column (DATETIME NULL)\n";
        }
        
        echo "\n🎉 Successfully added missing deadline columns!\n";
    }
    
    echo "\nUpdated quizzes table structure:\n";
    echo "================================\n";
    $updatedColumns = DB::select('DESCRIBE quizzes');
    foreach ($updatedColumns as $column) {
        $highlight = in_array($column->Field, ['has_deadline', 'due_date']) ? '>>> ' : '    ';
        echo $highlight . $column->Field . " - " . $column->Type . " " . $column->Null . " " . $column->Default . "\n";
    }
    
    // Test the new columns with sample data
    echo "\nTesting new columns with sample data:\n";
    echo "====================================\n";
    
    $sampleQuiz = DB::table('quizzes')->first();
    if ($sampleQuiz) {
        echo "Sample quiz data:\n";
        echo "  Quiz ID: {$sampleQuiz->quiz_id}\n";
        echo "  Title: {$sampleQuiz->quiz_title}\n";
        echo "  Has Deadline: " . (isset($sampleQuiz->has_deadline) ? ($sampleQuiz->has_deadline ? 'Yes' : 'No') : 'Column missing') . "\n";
        echo "  Due Date: " . (isset($sampleQuiz->due_date) ? ($sampleQuiz->due_date ?? 'NULL') : 'Column missing') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
