<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Structure of ui_settings table:\n";
try {
    $columns = DB::select('DESCRIBE ui_settings');
    foreach($columns as $column) {
        echo "- {$column->Field} ({$column->Type}) - " . ($column->Null === 'YES' ? 'Nullable' : 'Not Null') . "\n";
    }
    
    echo "\nCurrent data in ui_settings table:\n";
    $data = DB::select('SELECT * FROM ui_settings');
    if (count($data) > 0) {
        foreach($data[0] as $key => $value) {
            echo "- {$key}: " . ($value ?? 'NULL') . "\n";
        }
    } else {
        echo "No data found in ui_settings table\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
