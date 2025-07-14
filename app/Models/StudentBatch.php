<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBatch extends Model
{
    protected $table = 'student_batches';
    protected $primaryKey = 'batch_id';
    
    protected $fillable = [
        'batch_name',
        'program_id',
        'max_capacity',
        'current_capacity',
        'batch_status',
        'registration_deadline',
        'start_date',
        'end_date',
        'description',
        'created_by',
        'professor_id',
        'professor_assigned_at',
        'professor_assigned_by'
    ];

    protected $dates = [
        'registration_deadline',
        'start_date',
        'end_date',
        'professor_assigned_at'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'batch_id', 'batch_id');
    }

    public function assignedProfessor()
    {
        return $this->belongsTo(Professor::class, 'professor_id', 'professor_id');
    }

    // New relationship for multiple professors
    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'batch_professors', 'batch_id', 'professor_id')
                    ->withPivot('assigned_at', 'assigned_by')
                    ->withTimestamps();
    }

    public function professorAssignedBy()
    {
        return $this->belongsTo(Admin::class, 'professor_assigned_by', 'admin_id');
    }

    public function getStatusBadgeClassAttribute()
    {
        switch ($this->batch_status) {
            case 'available':
                return 'badge-success';
            case 'ongoing':
                return 'badge-warning';
            case 'closed':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    public function getCapacityPercentageAttribute()
    {
        if ($this->max_capacity == 0) return 0;
        return round(($this->current_capacity / $this->max_capacity) * 100, 2);
    }

    public function isAvailable()
    {
        return $this->batch_status === 'available' && 
               $this->current_capacity < $this->max_capacity &&
               now()->lte($this->registration_deadline);
    }

    /**
     * Get current students count (only approved registration and paid payment)
     */
    public function getCurrentCapacityAttribute()
    {
        return $this->enrollments()
            ->where('enrollment_status', 'approved')
            ->where('payment_status', 'paid')
            ->count();
    }

    /**
     * Get pending students count
     */
    public function getPendingStudentsCountAttribute()
    {
        return $this->enrollments()
            ->where(function($query) {
                $query->where('enrollment_status', 'pending')
                      ->orWhere('payment_status', 'pending');
            })
            ->where(function($query) {
                // Exclude students who are both approved and paid (they are current)
                $query->where('enrollment_status', '!=', 'approved')
                      ->orWhere('payment_status', '!=', 'paid');
            })
            ->count();
    }

    /**
     * Check if batch has available slots
     */
    public function hasAvailableSlots()
    {
        return $this->current_capacity < $this->max_capacity;
    }

    /**
     * Get available slots count
     */
    public function getAvailableSlotsAttribute()
    {
        return max(0, $this->max_capacity - $this->current_capacity);
    }

    /**
     * Update batch status based on current date
     */
    public function updateStatusBasedOnDates()
    {
        $today = now()->toDateString();
        
        if ($this->end_date && $today > $this->end_date) {
            // Batch has ended
            $this->update(['batch_status' => 'completed']);
        } elseif ($this->start_date && $today >= $this->start_date) {
            // Batch has started (ongoing)
            if ($this->batch_status !== 'ongoing' && $this->batch_status !== 'completed') {
                $this->update(['batch_status' => 'ongoing']);
            }
        }
        
        return $this->batch_status;
    }

    /**
     * Check if batch is ongoing
     */
    public function isOngoing()
    {
        $today = now()->toDateString();
        return $this->start_date && $today >= $this->start_date && 
               (!$this->end_date || $today <= $this->end_date);
    }

    /**
     * Check if batch is completed
     */
    public function isCompleted()
    {
        $today = now()->toDateString();
        return $this->end_date && $today > $this->end_date;
    }

    /**
     * Check if batch registration is still open
     */
    public function isRegistrationOpen()
    {
        $today = now()->toDateString();
        return $this->registration_deadline && $today <= $this->registration_deadline;
    }
}
