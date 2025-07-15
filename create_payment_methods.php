<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PaymentMethod;

echo "Creating default payment methods...\n";

try {
    $methods = [
        [
            'method_name' => 'GCash',
            'method_type' => 'gcash',
            'description' => 'Pay via GCash mobile wallet',
            'instructions' => 'Send payment to GCash number and upload receipt',
            'is_enabled' => 1,
            'sort_order' => 1,
            'created_by_admin_id' => 1
        ],
        [
            'method_name' => 'Maya (PayMaya)',
            'method_type' => 'maya',
            'description' => 'Pay via Maya mobile wallet',
            'instructions' => 'Send payment to Maya account and upload receipt',
            'is_enabled' => 1,
            'sort_order' => 2,
            'created_by_admin_id' => 1
        ],
        [
            'method_name' => 'Bank Transfer',
            'method_type' => 'bank_transfer',
            'description' => 'Pay via bank transfer',
            'instructions' => 'Transfer to our bank account and upload receipt',
            'is_enabled' => 1,
            'sort_order' => 3,
            'created_by_admin_id' => 1
        ],
        [
            'method_name' => 'Credit Card',
            'method_type' => 'credit_card',
            'description' => 'Pay via credit card',
            'instructions' => 'Contact office for credit card payment processing',
            'is_enabled' => 1,
            'sort_order' => 4,
            'created_by_admin_id' => 1
        ],
        [
            'method_name' => 'Cash',
            'method_type' => 'cash',
            'description' => 'Pay in cash at our office',
            'instructions' => 'Visit our office during business hours for cash payment',
            'is_enabled' => 1,
            'sort_order' => 5,
            'created_by_admin_id' => 1
        ]
    ];

    foreach ($methods as $method) {
        PaymentMethod::create($method);
        echo "✓ Created: {$method['method_name']}\n";
    }

    echo "\n✅ Successfully created " . count($methods) . " default payment methods!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
?>
