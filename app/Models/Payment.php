<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'enrollment_id',
        'student_id',
        'program_id',
        'package_id',
        'payment_method',
        'amount',
        'payment_status',
        'payment_details',
        'verified_by',
        'verified_at',
        'receipt_number',
        'reference_number',
        'notes',
        'rejected_at',
        'rejection_reason',
        'rejected_by',
        'rejected_fields',
        'resubmitted_at',
        'resubmission_count',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
        'rejected_at' => 'datetime',
        'rejected_fields' => 'array',
        'resubmitted_at' => 'datetime',
    ];

    /**
     * Encrypt the reference number when setting
     */
    public function setReferenceNumberAttribute($value)
    {
        if ($value) {
            $this->attributes['reference_number'] = Crypt::encryptString($value);
        } else {
            $this->attributes['reference_number'] = null;
        }
    }

    /**
     * Decrypt the reference number when getting
     */
    public function getReferenceNumberAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                // If decryption fails, return the encrypted value or null
                return null;
            }
        }
        return null;
    }

    /**
     * Encrypt the notes when setting
     */
    public function setNotesAttribute($value)
    {
        if ($value) {
            $this->attributes['notes'] = Crypt::encryptString($value);
        } else {
            $this->attributes['notes'] = null;
        }
    }

    /**
     * Decrypt the notes when getting
     */
    public function getNotesAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Encrypt the rejection reason when setting
     */
    public function setRejectionReasonAttribute($value)
    {
        if ($value) {
            $this->attributes['rejection_reason'] = Crypt::encryptString($value);
        } else {
            $this->attributes['rejection_reason'] = null;
        }
    }

    /**
     * Decrypt the rejection reason when getting
     */
    public function getRejectionReasonAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Encrypt sensitive data in payment_details when setting
     */
    public function setPaymentDetailsAttribute($value)
    {
        if (is_array($value)) {
            // Encrypt QR code related data if present
            if (isset($value['qr_code_data'])) {
                $value['qr_code_data'] = Crypt::encryptString($value['qr_code_data']);
            }
            if (isset($value['qr_code_path'])) {
                $value['qr_code_path'] = Crypt::encryptString($value['qr_code_path']);
            }
            // Encrypt payment proof path for uploaded screenshots
            if (isset($value['payment_proof_path'])) {
                $value['payment_proof_path'] = Crypt::encryptString($value['payment_proof_path']);
            }
            // Keep reference_number encrypted in payment_details as well
            if (isset($value['reference_number'])) {
                $value['reference_number'] = Crypt::encryptString($value['reference_number']);
            }
            // Encrypt payment method name for additional security
            if (isset($value['payment_method_name'])) {
                $value['payment_method_name'] = Crypt::encryptString($value['payment_method_name']);
            }
            // Encrypt any transaction ID or external reference
            if (isset($value['transaction_id'])) {
                $value['transaction_id'] = Crypt::encryptString($value['transaction_id']);
            }
        }
        $this->attributes['payment_details'] = json_encode($value);
    }

    /**
     * Decrypt sensitive data in payment_details when getting
     */
    public function getPaymentDetailsAttribute($value)
    {
        if ($value) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                // Decrypt QR code related data if present
                if (isset($decoded['qr_code_data'])) {
                    try {
                        $decoded['qr_code_data'] = Crypt::decryptString($decoded['qr_code_data']);
                    } catch (\Exception $e) {
                        $decoded['qr_code_data'] = null;
                    }
                }
                if (isset($decoded['qr_code_path'])) {
                    try {
                        $decoded['qr_code_path'] = Crypt::decryptString($decoded['qr_code_path']);
                    } catch (\Exception $e) {
                        $decoded['qr_code_path'] = null;
                    }
                }
                // Decrypt payment proof path
                if (isset($decoded['payment_proof_path'])) {
                    try {
                        $decoded['payment_proof_path'] = Crypt::decryptString($decoded['payment_proof_path']);
                    } catch (\Exception $e) {
                        $decoded['payment_proof_path'] = null;
                    }
                }
                // Decrypt reference_number in payment_details as well
                if (isset($decoded['reference_number'])) {
                    try {
                        $decoded['reference_number'] = Crypt::decryptString($decoded['reference_number']);
                    } catch (\Exception $e) {
                        $decoded['reference_number'] = null;
                    }
                }
                // Decrypt payment method name
                if (isset($decoded['payment_method_name'])) {
                    try {
                        $decoded['payment_method_name'] = Crypt::decryptString($decoded['payment_method_name']);
                    } catch (\Exception $e) {
                        $decoded['payment_method_name'] = null;
                    }
                }
                // Decrypt transaction ID
                if (isset($decoded['transaction_id'])) {
                    try {
                        $decoded['transaction_id'] = Crypt::decryptString($decoded['transaction_id']);
                    } catch (\Exception $e) {
                        $decoded['transaction_id'] = null;
                    }
                }
            }
            return $decoded;
        }
        return null;
    }

    // Relationships
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }

    public function registration()
    {
        return $this->hasOneThrough(
            Registration::class,
            Enrollment::class,
            'enrollment_id', // Foreign key on enrollments table
            'id', // Foreign key on registrations table  
            'enrollment_id', // Local key on payments table
            'registration_id' // Local key on enrollments table
        );
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }
}