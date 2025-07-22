<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'notes',
        // Rejection system fields
        'rejection_reason',
        'rejected_fields',
        'rejected_at',
        'can_resubmit',
        'resubmitted_at',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
        'rejected_fields' => 'array',
        'rejected_at' => 'datetime',
        'resubmitted_at' => 'datetime',
        'can_resubmit' => 'boolean',
    ];

    // Relationships
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
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