<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking payment_history table structure ===\n\n";

try {
    $columns = DB::select("DESCRIBE payment_history");
    
    echo "Current table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type} (Null: {$column->Null}, Key: {$column->Key})\n";
    }
    
    echo "\n=== Fixing payment_method column ===\n";
    
    // Fix the payment_method column size
    DB::statement("ALTER TABLE payment_history MODIFY payment_method VARCHAR(100)");
    echo "✅ payment_method column expanded to VARCHAR(100)\n";
    
    // Also fix payment_status if needed
    DB::statement("ALTER TABLE payment_history MODIFY payment_status VARCHAR(100)");
    echo "✅ payment_status column expanded to VARCHAR(100)\n";
    
    echo "\nUpdated table structure:\n";
    $columns = DB::select("DESCRIBE payment_history");
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
