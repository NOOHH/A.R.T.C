<?php

// Test encryption functionality
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Models\Payment;

try {
    echo "=== Testing Payment Data Encryption ===\n\n";
    
    // Get a payment record
    $payment = Payment::find(29);
    
    if ($payment) {
        echo "Payment ID: " . $payment->payment_id . "\n";
        echo "Payment Status: " . $payment->payment_status . "\n";
        echo "Amount: " . $payment->amount . "\n";
        echo "Payment Method: " . $payment->payment_method . "\n";
        
        // Test encrypted fields (these should show decrypted values)
        echo "\n--- Encrypted Fields (showing decrypted values) ---\n";
        echo "Reference Number: " . ($payment->reference_number ?? 'NULL') . "\n";
        echo "Notes: " . ($payment->notes ?? 'NULL') . "\n";
        
        // Test payment details
        echo "\n--- Payment Details (decrypted) ---\n";
        $details = $payment->payment_details;
        if ($details) {
            foreach ($details as $key => $value) {
                echo "$key: $value\n";
            }
        } else {
            echo "No payment details found\n";
        }
        
        // Check raw database values (should be encrypted)
        echo "\n--- Raw Database Values (should be encrypted) ---\n";
        $rawPayment = \DB::table('payments')->where('payment_id', 29)->first();
        if ($rawPayment) {
            echo "Raw Reference Number: " . ($rawPayment->reference_number ?? 'NULL') . "\n";
            echo "Raw Notes: " . ($rawPayment->notes ?? 'NULL') . "\n";
            echo "Raw Payment Details: " . ($rawPayment->payment_details ?? 'NULL') . "\n";
        }
        
    } else {
        echo "Payment with ID 29 not found\n";
        
        // List available payment IDs
        $payments = Payment::take(5)->get(['payment_id', 'payment_status']);
        echo "\nAvailable payments:\n";
        foreach ($payments as $p) {
            echo "ID: {$p->payment_id}, Status: {$p->payment_status}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
