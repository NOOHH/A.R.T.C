<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Structure of website_requests table:\n";
try {
    $columns = DB::select('DESCRIBE website_requests');
    foreach($columns as $column) {
        echo "- {$column->Field} ({$column->Type}) - " . ($column->Null === 'YES' ? 'Nullable' : 'Not Null') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
