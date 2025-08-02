<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    // Get current table columns
    $columns = DB::select('DESCRIBE quizzes');
    $tableColumns = array_column($columns, 'Field');
    
    // Get fillable columns from model
    $quiz = new App\Models\Quiz();
    $fillableColumns = $quiz->getFillable();
    
    echo "Checking for missing columns based on Quiz model fillable array...\n\n";
    
    echo "Fillable columns in model:\n";
    $missingColumns = [];
    foreach ($fillableColumns as $column) {
        $exists = in_array($column, $tableColumns);
        $status = $exists ? '✅' : '❌';
        echo "  $status $column\n";
        if (!$exists) {
            $missingColumns[] = $column;
        }
    }
    
    if (!empty($missingColumns)) {
        echo "\n❌ Missing columns that need to be added:\n";
        foreach ($missingColumns as $column) {
            echo "  • $column\n";
        }
    } else {
        echo "\n🎉 All fillable columns exist in the database table!\n";
    }
    
    echo "\nTable columns not in fillable (system columns):\n";
    foreach ($tableColumns as $column) {
        if (!in_array($column, $fillableColumns)) {
            echo "  ℹ️  $column (system column)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
