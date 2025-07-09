<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Enrollment;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== Testing Mark as Paid Functionality ===\n\n";

try {
    // Test 1: Check if payment_history table exists
    echo "1. Checking payment_history table:\n";
    $tables = DB::select("SHOW TABLES LIKE 'payment_history'");
    if (empty($tables)) {
        echo "   ❌ payment_history table does not exist!\n";
        echo "   Creating payment_history table...\n";
        
        // Create the table
        DB::statement("
            CREATE TABLE payment_history (
                payment_history_id INT AUTO_INCREMENT PRIMARY KEY,
                enrollment_id INT,
                user_id INT,
                student_id VARCHAR(255),
                program_id INT,
                package_id INT,
                amount DECIMAL(10,2),
                payment_status VARCHAR(50),
                payment_method VARCHAR(50),
                payment_notes TEXT,
                payment_date DATETIME,
                processed_by_admin_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        echo "   ✅ payment_history table created\n";
    } else {
        echo "   ✅ payment_history table exists\n";
    }
    
    // Test 2: Check PaymentHistory model
    echo "\n2. Testing PaymentHistory model:\n";
    $testHistory = new PaymentHistory();
    echo "   ✅ PaymentHistory model can be instantiated\n";
    
    // Test 3: Find an enrollment to test with
    echo "\n3. Finding test enrollment:\n";
    $enrollment = Enrollment::where('payment_status', 'pending')->first();
    if (!$enrollment) {
        echo "   ❌ No pending enrollments found for testing\n";
        echo "   Creating test enrollment...\n";
        
        $enrollment = Enrollment::create([
            'user_id' => 104, // Use existing user
            'program_id' => 1,
            'package_id' => 1,
            'enrollment_status' => 'approved',
            'payment_status' => 'pending',
            'enrollment_type' => 'full',
            'learning_mode' => 'synchronous'
        ]);
        echo "   ✅ Test enrollment created: {$enrollment->enrollment_id}\n";
    } else {
        echo "   ✅ Found enrollment {$enrollment->enrollment_id} with pending payment\n";
    }
    
    // Test 4: Simulate the markAsPaid method
    echo "\n4. Testing markAsPaid functionality:\n";
    
    DB::beginTransaction();
    
    try {
        // Check if enrollment exists
        $testEnrollment = Enrollment::where('enrollment_id', $enrollment->enrollment_id)->first();
        if (!$testEnrollment) {
            throw new Exception("Enrollment not found");
        }
        echo "   ✅ Enrollment found\n";
        
        // Check if already paid
        if ($testEnrollment->payment_status === 'paid') {
            echo "   ⚠️  Enrollment already marked as paid\n";
        } else {
            echo "   ✅ Enrollment is pending payment\n";
        }
        
        // Test PaymentHistory creation
        $paymentData = [
            'enrollment_id' => $testEnrollment->enrollment_id,
            'user_id' => $testEnrollment->user_id,
            'student_id' => $testEnrollment->student_id,
            'program_id' => $testEnrollment->program_id,
            'package_id' => $testEnrollment->package_id,
            'payment_status' => 'paid',
            'payment_method' => 'manual',
            'payment_notes' => 'Test payment by administrator',
            'payment_date' => now(),
            'processed_by_admin_id' => 1,
        ];
        
        echo "   Creating payment history record...\n";
        $paymentHistory = PaymentHistory::create($paymentData);
        echo "   ✅ Payment history created: {$paymentHistory->payment_history_id}\n";
        
        // Update enrollment
        echo "   Updating enrollment payment status...\n";
        $testEnrollment->update([
            'payment_status' => 'paid',
            'updated_at' => now()
        ]);
        echo "   ✅ Enrollment updated successfully\n";
        
        DB::commit();
        echo "   ✅ Transaction committed successfully\n";
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . "\n";
        echo "   Line: " . $e->getLine() . "\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
