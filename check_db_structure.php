<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Check if tables exist
    $tables = ['users', 'students', 'professors', 'directors', 'chats'];
    
    foreach ($tables as $table) {
        try {
            $exists = DB::select("SHOW TABLES LIKE '{$table}'");
            if ($exists) {
                echo "Table '{$table}' exists" . PHP_EOL;
                
                // Get table structure
                $columns = DB::select("DESCRIBE {$table}");
                echo "  Columns: ";
                foreach ($columns as $column) {
                    echo $column->Field . " ";
                }
                echo PHP_EOL;
                
                // Count records
                $count = DB::table($table)->count();
                echo "  Records: {$count}" . PHP_EOL;
                
            } else {
                echo "Table '{$table}' does not exist" . PHP_EOL;
            }
        } catch (Exception $e) {
            echo "Error checking table '{$table}': " . $e->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . PHP_EOL;
}
?>
