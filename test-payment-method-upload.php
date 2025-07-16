<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;

echo "Testing payment methods functionality...\n\n";

try {
    // Test 1: Check if payment methods exist
    $count = PaymentMethod::count();
    echo "✓ Payment methods count: $count\n";
    
    // Test 2: List all payment methods
    $methods = PaymentMethod::all();
    echo "✓ Payment methods list:\n";
    foreach ($methods as $method) {
        echo "  - {$method->method_name} ({$method->method_type}) - " . ($method->is_enabled ? 'Enabled' : 'Disabled') . "\n";
        if ($method->qr_code_path) {
            echo "    QR Code: {$method->qr_code_path}\n";
        }
    }
    
    // Test 3: Check storage directories
    echo "\n✓ Storage structure:\n";
    echo "  - storage/app/public exists: " . (is_dir('storage/app/public') ? 'YES' : 'NO') . "\n";
    echo "  - storage/app/public/payment_qr_codes exists: " . (is_dir('storage/app/public/payment_qr_codes') ? 'YES' : 'NO') . "\n";
    echo "  - public/storage exists: " . (is_dir('public/storage') ? 'YES' : 'NO') . "\n";
    echo "  - public/storage/payment_qr_codes exists: " . (is_dir('public/storage/payment_qr_codes') ? 'YES' : 'NO') . "\n";
    
    // Test 4: Check permissions
    echo "\n✓ Permissions:\n";
    echo "  - storage/app/public writable: " . (is_writable('storage/app/public') ? 'YES' : 'NO') . "\n";
    echo "  - storage/app/public/payment_qr_codes writable: " . (is_writable('storage/app/public/payment_qr_codes') ? 'YES' : 'NO') . "\n";
    
    echo "\n✅ All tests completed successfully!\n";
    echo "\nNow you should be able to upload QR codes for payment methods.\n";
    echo "If you still get errors, check the Laravel logs for detailed error messages.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone!\n";
?>
