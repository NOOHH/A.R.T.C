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
        'description',
        'created_by',
        'professor_id',
        'professor_assigned_at',
        'professor_assigned_by'
    ];

    protected $dates = [
        'registration_deadline',
        'start_date',
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
}
