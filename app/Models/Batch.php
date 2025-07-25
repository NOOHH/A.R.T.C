<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Batch extends Model
{
    protected $table = 'student_batches';
    protected $primaryKey = 'batch_id';
    
    protected $fillable = [
        'program_id',
        'batch_name',
        'batch_description',
        'batch_capacity',
        'batch_status',
        'start_date',
        'end_date',
        'enrollment_deadline',
        'created_by_admin_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'enrollment_deadline' => 'datetime',
    ];

    /**
     * Relationship with Program
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    /**
     * Relationship with Enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'batch_id');
    }

    /**
     * Relationship with Admin who created the batch
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id', 'admin_id');
    }

    /**
     * Get current enrollment count
     */
    public function getCurrentEnrollmentCount()
    {
        return $this->enrollments()->count();
    }

    /**
     * Get available slots
     */
    public function getAvailableSlots()
    {
        return $this->batch_capacity - $this->getCurrentEnrollmentCount();
    }

    /**
     * Check if batch is full
     */
    public function isFull()
    {
        return $this->getCurrentEnrollmentCount() >= $this->batch_capacity;
    }

    /**
     * Check if enrollment deadline has passed
     */
    public function isEnrollmentDeadlinePassed()
    {
        return $this->enrollment_deadline && Carbon::now()->isAfter($this->enrollment_deadline);
    }

    /**
     * Check if batch is available for enrollment
     */
    public function isAvailableForEnrollment()
    {
        return $this->batch_status === 'available' && 
               !$this->isFull() && 
               !$this->isEnrollmentDeadlinePassed();
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayAttribute()
    {
        $currentCount = $this->getCurrentEnrollmentCount();
        $capacity = $this->batch_capacity;
        
        switch ($this->batch_status) {
            case 'available':
                if ($this->isFull()) {
                    return "Full ({$currentCount}/{$capacity} students)";
                } elseif ($this->isEnrollmentDeadlinePassed()) {
                    return "Enrollment Closed ({$currentCount}/{$capacity} students)";
                } else {
                    return "Available ({$currentCount}/{$capacity} students)";
                }
            case 'ongoing':
                return "Ongoing and Available to Join ({$currentCount}/{$capacity} students)";
            case 'closed':
                return "Closed ({$currentCount}/{$capacity} students)";
            case 'completed':
                return "Completed ({$currentCount}/{$capacity} students)";
            default:
                return ucfirst($this->batch_status) . " ({$currentCount}/{$capacity} students)";
        }
    }

    /**
     * Scope for available batches
     */
    public function scopeAvailable($query)
    {
        return $query->where('batch_status', 'available')
                    ->where('registration_deadline', '>', Carbon::now())
                    ->whereRaw('(SELECT COUNT(*) FROM enrollments WHERE batch_id = student_batches.batch_id) < max_capacity');
    }

    /**
     * Scope for ongoing batches
     */
    public function scopeOngoing($query)
    {
        return $query->where('batch_status', 'ongoing');
    }

    /**
     * Scope for a specific program
     */
    public function scopeForProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }
}
