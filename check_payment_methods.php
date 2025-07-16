<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PaymentMethod;

echo "Checking payment methods in database...\n\n";

try {
    $methods = PaymentMethod::all(['id', 'method_name', 'method_type', 'is_enabled', 'created_by_admin_id']);
    
    if ($methods->count() > 0) {
        echo "Found " . $methods->count() . " payment methods:\n";
        echo str_repeat('-', 80) . "\n";
        printf("%-4s %-20s %-15s %-10s %-10s\n", "ID", "Method Name", "Type", "Enabled", "Admin ID");
        echo str_repeat('-', 80) . "\n";
        
        foreach ($methods as $method) {
            printf("%-4s %-20s %-15s %-10s %-10s\n", 
                $method->id,
                $method->method_name,
                $method->method_type,
                $method->is_enabled ? 'Yes' : 'No',
                $method->created_by_admin_id
            );
        }
    } else {
        echo "❌ No payment methods found in database.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
?>
