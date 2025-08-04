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
        // Get all payments that need encryption
        $payments = DB::table('payments')->get();

        foreach ($payments as $payment) {
            $updates = [];
            
            // Encrypt reference_number if not already encrypted
            if ($payment->reference_number && !$this->isEncrypted($payment->reference_number)) {
                try {
                    $updates['reference_number'] = Crypt::encryptString($payment->reference_number);
                } catch (\Exception $e) {
                    // Skip if encryption fails
                }
            }

            // Encrypt notes if not already encrypted
            if ($payment->notes && !$this->isEncrypted($payment->notes)) {
                try {
                    $updates['notes'] = Crypt::encryptString($payment->notes);
                } catch (\Exception $e) {
                    // Skip if encryption fails
                }
            }

            // Encrypt rejection_reason if not already encrypted
            if ($payment->rejection_reason && !$this->isEncrypted($payment->rejection_reason)) {
                try {
                    $updates['rejection_reason'] = Crypt::encryptString($payment->rejection_reason);
                } catch (\Exception $e) {
                    // Skip if encryption fails
                }
            }

            // Encrypt payment_details fields
            if ($payment->payment_details) {
                $paymentDetails = json_decode($payment->payment_details, true);
                if (is_array($paymentDetails)) {
                    $modified = false;

                    // Encrypt payment_proof_path if not already encrypted
                    if (isset($paymentDetails['payment_proof_path']) && !$this->isEncrypted($paymentDetails['payment_proof_path'])) {
                        try {
                            $paymentDetails['payment_proof_path'] = Crypt::encryptString($paymentDetails['payment_proof_path']);
                            $modified = true;
                        } catch (\Exception $e) {
                            // Skip if encryption fails
                        }
                    }

                    // Encrypt reference_number in payment_details if not already encrypted
                    if (isset($paymentDetails['reference_number']) && !$this->isEncrypted($paymentDetails['reference_number'])) {
                        try {
                            $paymentDetails['reference_number'] = Crypt::encryptString($paymentDetails['reference_number']);
                            $modified = true;
                        } catch (\Exception $e) {
                            // Skip if encryption fails
                        }
                    }

                    // Encrypt payment_method_name if not already encrypted
                    if (isset($paymentDetails['payment_method_name']) && !$this->isEncrypted($paymentDetails['payment_method_name'])) {
                        try {
                            $paymentDetails['payment_method_name'] = Crypt::encryptString($paymentDetails['payment_method_name']);
                            $modified = true;
                        } catch (\Exception $e) {
                            // Skip if encryption fails
                        }
                    }

                    // Encrypt transaction_id if present and not already encrypted
                    if (isset($paymentDetails['transaction_id']) && !$this->isEncrypted($paymentDetails['transaction_id'])) {
                        try {
                            $paymentDetails['transaction_id'] = Crypt::encryptString($paymentDetails['transaction_id']);
                            $modified = true;
                        } catch (\Exception $e) {
                            // Skip if encryption fails
                        }
                    }

                    // Encrypt qr_code_data if present and not already encrypted
                    if (isset($paymentDetails['qr_code_data']) && !$this->isEncrypted($paymentDetails['qr_code_data'])) {
                        try {
                            $paymentDetails['qr_code_data'] = Crypt::encryptString($paymentDetails['qr_code_data']);
                            $modified = true;
                        } catch (\Exception $e) {
                            // Skip if encryption fails
                        }
                    }

                    // Encrypt qr_code_path if present and not already encrypted
                    if (isset($paymentDetails['qr_code_path']) && !$this->isEncrypted($paymentDetails['qr_code_path'])) {
                        try {
                            $paymentDetails['qr_code_path'] = Crypt::encryptString($paymentDetails['qr_code_path']);
                            $modified = true;
                        } catch (\Exception $e) {
                            // Skip if encryption fails
                        }
                    }

                    if ($modified) {
                        $updates['payment_details'] = json_encode($paymentDetails);
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
        // Note: This migration cannot be easily reversed without risking data loss
        // The decryption would require the same app key that was used for encryption
        // In a production environment, you should backup the database before running this migration
        
        $payments = DB::table('payments')->get();

        foreach ($payments as $payment) {
            $updates = [];
            
            // Attempt to decrypt reference_number
            if ($payment->reference_number && $this->isEncrypted($payment->reference_number)) {
                try {
                    $updates['reference_number'] = Crypt::decryptString($payment->reference_number);
                } catch (\Exception $e) {
                    // Skip if decryption fails
                }
            }

            // Attempt to decrypt notes
            if ($payment->notes && $this->isEncrypted($payment->notes)) {
                try {
                    $updates['notes'] = Crypt::decryptString($payment->notes);
                } catch (\Exception $e) {
                    // Skip if decryption fails
                }
            }

            // Attempt to decrypt rejection_reason
            if ($payment->rejection_reason && $this->isEncrypted($payment->rejection_reason)) {
                try {
                    $updates['rejection_reason'] = Crypt::decryptString($payment->rejection_reason);
                } catch (\Exception $e) {
                    // Skip if decryption fails
                }
            }

            // Decrypt payment_details fields
            if ($payment->payment_details) {
                $paymentDetails = json_decode($payment->payment_details, true);
                if (is_array($paymentDetails)) {
                    $modified = false;

                    foreach (['payment_proof_path', 'reference_number', 'payment_method_name', 'transaction_id', 'qr_code_data', 'qr_code_path'] as $field) {
                        if (isset($paymentDetails[$field]) && $this->isEncrypted($paymentDetails[$field])) {
                            try {
                                $paymentDetails[$field] = Crypt::decryptString($paymentDetails[$field]);
                                $modified = true;
                            } catch (\Exception $e) {
                                // Skip if decryption fails
                            }
                        }
                    }

                    if ($modified) {
                        $updates['payment_details'] = json_encode($paymentDetails);
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
     * Check if a string is already encrypted by Laravel's Crypt facade
     */
    private function isEncrypted($value)
    {
        if (empty($value)) {
            return false;
        }

        try {
            // Try to decode the base64 payload
            $payload = json_decode(base64_decode($value), true);
            
            // Check if it has the Laravel encryption structure
            return is_array($payload) && 
                   isset($payload['iv']) && 
                   isset($payload['value']) && 
                   isset($payload['mac']);
        } catch (\Exception $e) {
            return false;
        }
    }
};
