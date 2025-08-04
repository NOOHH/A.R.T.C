<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Illuminate\Support\Facades\Crypt;

class TestPaymentEncryption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:payment-encryption';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test payment encryption functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing Payment Encryption...');
        
        // Test 1: Check all existing payments
        $existingPayments = Payment::all();
        $this->info('Found ' . $existingPayments->count() . ' existing payments:');
        
        foreach ($existingPayments as $index => $payment) {
            $this->line('Payment ID: ' . $payment->payment_id);
            $this->line('  Reference Number: ' . ($payment->reference_number ?? 'NULL'));
            $this->line('  Raw DB Value: ' . ($payment->getRawOriginal('reference_number') ?? 'NULL'));
            $this->line('  Reference Is Encrypted: ' . (strpos($payment->getRawOriginal('reference_number') ?? '', 'eyJpdiI6') === 0 ? 'YES' : 'NO'));
            
            // Check payment_details
            $paymentDetails = $payment->payment_details;
            $rawPaymentDetails = $payment->getRawOriginal('payment_details');
            $this->line('  Payment Details: ' . ($paymentDetails ? json_encode($paymentDetails) : 'NULL'));
            $this->line('  Raw Payment Details: ' . ($rawPaymentDetails ?? 'NULL'));
            $this->line('  Payment Details Is Encrypted: ' . (strpos($rawPaymentDetails ?? '', 'eyJpdiI6') !== false ? 'YES' : 'NO'));
            
            // Check if there's sensitive data that should be encrypted
            $hasSensitiveData = false;
            if ($paymentDetails && is_array($paymentDetails)) {
                if (isset($paymentDetails['qr_code_data']) || isset($paymentDetails['qr_code_path'])) {
                    $hasSensitiveData = true;
                }
            }
            $this->line('  Has Sensitive Data: ' . ($hasSensitiveData ? 'YES' : 'NO'));
            $this->newLine();
        }
        
        // Test 2: Create a new payment with encryption
        $this->info('Creating new test payment with encryption...');
        try {
            $testPayment = Payment::create([
                'enrollment_id' => 1,
                'student_id' => 1,
                'program_id' => 1,
                'package_id' => 1,
                'payment_method' => 'gcash',
                'amount' => 1000.00,
                'payment_status' => 'pending',
                'reference_number' => 'TEST_REF_' . time(),
                'payment_details' => [
                    'qr_code_data' => 'QR_TEST_DATA_' . time(),
                    'reference_number' => 'TEST_REF_' . time(),
                    'payment_method_name' => 'GCash Test',
                    'uploaded_at' => now()->toISOString()
                ],
                'notes' => 'Test payment for encryption verification'
            ]);
            
            $this->info('Test payment created successfully!');
            $this->line('Payment ID: ' . $testPayment->payment_id);
            $this->line('Reference Number (decrypted): ' . $testPayment->reference_number);
            $this->line('Raw DB Value (encrypted): ' . $testPayment->getRawOriginal('reference_number'));
            $this->line('Is Encrypted: ' . (strpos($testPayment->getRawOriginal('reference_number'), 'eyJpdiI6') === 0 ? 'YES' : 'NO'));
            
            // Test payment_details encryption
            $this->line('Payment Details (decrypted): ' . json_encode($testPayment->payment_details));
            $this->line('Raw Payment Details (encrypted): ' . $testPayment->getRawOriginal('payment_details'));
            $this->line('Payment Details Is Encrypted: ' . (strpos($testPayment->getRawOriginal('payment_details'), 'eyJpdiI6') !== false ? 'YES' : 'NO'));
            
            // Test decryption
            $decrypted = Crypt::decryptString($testPayment->getRawOriginal('reference_number'));
            $this->line('Manual Decryption Test: ' . $decrypted);
            $this->line('Decryption Match: ' . ($decrypted === $testPayment->reference_number ? 'YES' : 'NO'));
            
            // Clean up test payment
            $testPayment->delete();
            $this->info('Test payment cleaned up.');
            
        } catch (\Exception $e) {
            $this->error('Error creating test payment: ' . $e->getMessage());
        }
        
        $this->info('Encryption test completed!');
        
        return 0;
    }
}
