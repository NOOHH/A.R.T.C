<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fixing payment_history ENUM columns ===\n\n";

try {
    // Fix payment_method ENUM to include 'manual'
    echo "Adding 'manual' to payment_method ENUM...\n";
    DB::statement("ALTER TABLE payment_history MODIFY payment_method ENUM('cash','card','bank_transfer','gcash','manual','other')");
    echo "✅ payment_method ENUM updated\n";
    
    // Fix payment_status ENUM to include more statuses
    echo "Updating payment_status ENUM...\n";
    DB::statement("ALTER TABLE payment_history MODIFY payment_status ENUM('pending','paid','failed','refunded','cancelled','processing')");
    echo "✅ payment_status ENUM updated\n";
    
    echo "\nUpdated ENUM values:\n";
    $columns = DB::select("SHOW COLUMNS FROM payment_history WHERE Field IN ('payment_method', 'payment_status')");
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
