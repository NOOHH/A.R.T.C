<?php
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Users table structure:\n";
    $columns = DB::select('DESCRIBE users');
    foreach ($columns as $column) {
        echo $column->Field . ' | ' . $column->Type . ' | ' . $column->Key . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
