<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Specific Referral Code PROF08ROBERT ===\n";

try {
    $testCode = 'PROF08ROBERT';
    echo "Testing referral code: $testCode\n";
    
    // Check if the code exists in database
    echo "\n1. Checking if referral code exists in database:\n";
    $professor = DB::table('professors')->where('referral_code', $testCode)->first();
    if ($professor) {
        echo "✅ Found professor: {$professor->professor_name} (ID: {$professor->professor_id})\n";
        echo "   Email: {$professor->professor_email}\n";
        echo "   Referral Code: {$professor->referral_code}\n";
    } else {
        echo "❌ No professor found with referral code: $testCode\n";
    }
    
    echo "\n2. Testing ReferralCodeGenerator::getReferralInfo():\n";
    $referralInfo = App\Helpers\ReferralCodeGenerator::getReferralInfo($testCode);
    if ($referralInfo) {
        echo "✅ getReferralInfo() returned:\n";
        foreach($referralInfo as $key => $value) {
            echo "   $key: $value\n";
        }
    } else {
        echo "❌ getReferralInfo() returned null\n";
    }
    
    echo "\n3. Testing full validation process:\n";
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
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
