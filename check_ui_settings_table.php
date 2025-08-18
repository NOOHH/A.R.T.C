<?php
require_once 'vendor/autoload.php';

// Properly initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING UI_SETTINGS TABLE STRUCTURE ===\n\n";

try {
    $columns = DB::select('DESCRIBE ui_settings');
    echo "Table structure:\n";
    foreach ($columns as $column) {
        echo "  {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "Error checking table structure: " . $e->getMessage() . "\n";
}

echo "\n=== CHECKING TABLE DATA ===\n";
try {
    $data = DB::select('SELECT * FROM ui_settings LIMIT 5');
    if (empty($data)) {
        echo "No data found in ui_settings table\n";
    } else {
        echo "Sample data:\n";
        foreach ($data as $row) {
            $rowData = (array) $row;
            echo "  " . json_encode($rowData) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error checking table data: " . $e->getMessage() . "\n";
}
?>
