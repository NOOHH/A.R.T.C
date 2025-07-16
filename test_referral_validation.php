<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Referral Code Validation ===\n";

// Test with a known referral code
$testCode = 'DIR07ALEK';

try {
    echo "Testing referral code: $testCode\n";
    
    $validation = App\Helpers\ReferralCodeGenerator::validateReferralCode($testCode, null);
    
    echo "Validation result:\n";
    echo "Valid: " . ($validation['valid'] ? 'YES' : 'NO') . "\n";
    echo "Message: " . $validation['message'] . "\n";
    
    if ($validation['valid'] && isset($validation['referral_info'])) {
        echo "Referrer info:\n";
        foreach($validation['referral_info'] as $key => $value) {
            echo "  $key: $value\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error during validation: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
