<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Encrypt existing payment data
        $payments = DB::table('payments')->get();
        
        foreach ($payments as $payment) {
            $updates = [];
            
            // Encrypt reference_number if it exists and is not already encrypted
            if ($payment->reference_number && !$this->isEncrypted($payment->reference_number)) {
                $updates['reference_number'] = Crypt::encryptString($payment->reference_number);
            }
            
            // Encrypt payment_details if it exists
            if ($payment->payment_details) {
                $details = json_decode($payment->payment_details, true);
                if (is_array($details)) {
                    $updated = false;
                    
                    // Encrypt qr_code_data if present and not already encrypted
                    if (isset($details['qr_code_data']) && !$this->isEncrypted($details['qr_code_data'])) {
                        $details['qr_code_data'] = Crypt::encryptString($details['qr_code_data']);
                        $updated = true;
                    }
                    
                    // Encrypt qr_code_path if present and not already encrypted
                    if (isset($details['qr_code_path']) && !$this->isEncrypted($details['qr_code_path'])) {
                        $details['qr_code_path'] = Crypt::encryptString($details['qr_code_path']);
                        $updated = true;
                    }
                    
                    // Encrypt reference_number in payment_details if present and not already encrypted
                    if (isset($details['reference_number']) && !$this->isEncrypted($details['reference_number'])) {
                        $details['reference_number'] = Crypt::encryptString($details['reference_number']);
                        $updated = true;
                    }
                    
                    if ($updated) {
                        $updates['payment_details'] = json_encode($details);
                    }
                }
            }
            
            // Update the payment if there are changes
            if (!empty($updates)) {
                DB::table('payments')
                    ->where('payment_id', $payment->payment_id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Note: Decrypting existing data is risky and not recommended
        // This migration will not be reversible for security reasons
    }
    
    /**
     * Check if a string is already encrypted
     */
    private function isEncrypted($value)
    {
        // Laravel encrypted strings start with 'eyJpdiI6'
        return is_string($value) && strpos($value, 'eyJpdiI6') === 0;
    }
};
