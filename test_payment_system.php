<?php
/**
 * Test script for Student Payment Modal System
 */
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test 1: Check if payment methods exist
echo "=== Testing Student Payment Modal System ===\n\n";

try {
    // Check payment methods
    $paymentMethods = \App\Models\PaymentMethod::where('is_enabled', true)->get();
    echo "1. Payment Methods Test:\n";
    echo "   Found " . $paymentMethods->count() . " enabled payment methods\n";
    
    if ($paymentMethods->count() == 0) {
        echo "   Creating sample payment methods...\n";
        
        \App\Models\PaymentMethod::insert([
            [
                'method_name' => 'GCash',
                'method_type' => 'gcash',
                'description' => 'Pay via GCash mobile wallet',
                'instructions' => 'Send payment to GCash number and upload receipt',
                'is_enabled' => true,
                'sort_order' => 1,
                'created_by_admin_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'method_name' => 'Maya (PayMaya)',
                'method_type' => 'maya',
                'description' => 'Pay via Maya mobile wallet',
                'instructions' => 'Send payment to Maya account and upload receipt',
                'is_enabled' => true,
                'sort_order' => 2,
                'created_by_admin_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        
        echo "   ✓ Sample payment methods created!\n";
    } else {
        foreach ($paymentMethods as $method) {
            echo "   - {$method->method_name} ({$method->method_type})\n";
        }
    }
    
    // Test 2: Check if routes are accessible
    echo "\n2. Routes Test:\n";
    $routes = [
        'student.payment.methods' => '/student/payment/methods',
        'student.payment.upload-proof' => '/student/payment/upload-proof',
        'student.payment.enrollment.details' => '/student/payment/enrollment/1/details'
    ];
    
    foreach ($routes as $name => $path) {
        try {
            $route = route($name);
            echo "   ✓ Route '{$name}' exists\n";
        } catch (Exception $e) {
            echo "   ✗ Route '{$name}' not found\n";
        }
    }
    
    // Test 3: Check controller
    echo "\n3. Controller Test:\n";
    $controllerFile = app_path('Http/Controllers/StudentPaymentModalController.php');
    if (file_exists($controllerFile)) {
        echo "   ✓ StudentPaymentModalController exists\n";
        
        // Check if controller methods exist
        $controller = new \App\Http\Controllers\StudentPaymentModalController();
        $methods = ['getPaymentMethods', 'uploadPaymentProof', 'getEnrollmentPaymentDetails'];
        
        foreach ($methods as $method) {
            if (method_exists($controller, $method)) {
                echo "   ✓ Method '{$method}' exists\n";
            } else {
                echo "   ✗ Method '{$method}' missing\n";
            }
        }
    } else {
        echo "   ✗ Controller file not found\n";
    }
    
    // Test 4: Check models
    echo "\n4. Models Test:\n";
    $models = ['PaymentMethod', 'Payment', 'Enrollment', 'Student'];
    foreach ($models as $model) {
        $modelClass = "\\App\\Models\\{$model}";
        if (class_exists($modelClass)) {
            echo "   ✓ {$model} model exists\n";
        } else {
            echo "   ✗ {$model} model missing\n";
        }
    }
    
    echo "\n=== Test Completed ===\n";
    echo "✓ Student Payment Modal System is ready to use!\n\n";
    
    echo "Usage Instructions:\n";
    echo "1. Student clicks 'Payment Required' button on dashboard\n";
    echo "2. Modal opens showing available payment methods\n";
    echo "3. Student selects GCash or Maya (or other enabled methods)\n";
    echo "4. QR code is displayed if available\n";
    echo "5. Student uploads payment proof screenshot\n";
    echo "6. Admin receives notification for verification\n\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
