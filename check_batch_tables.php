<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Batch-Related Tables ===\n";
$tables = DB::select('SHOW TABLES');
foreach($tables as $table) {
    $tableName = array_values((array)$table)[0];
    if (strpos($tableName, 'batch') !== false || strpos($tableName, 'program') !== false) {
        echo "Found: $tableName\n";
        try {
            $count = DB::table($tableName)->count();
            echo "  Records: $count\n";
            
            // Show structure for key tables
            if ($tableName === 'student_batches' || $tableName === 'programs') {
                $structure = DB::select("DESCRIBE $tableName");
                echo "  Columns: ";
                foreach($structure as $column) {
                    echo $column->Field . " ";
                }
                echo "\n";
            }
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }
}
