<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchEnrollment extends Model
{
    protected $table = 'batch_enrollments';
    
    protected $fillable = [
        'batch_name',
        'program_id',
        'start_date',
        'end_date',
        'max_capacity',
        'current_capacity',
        'schedule',
        'status'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'batch_id');
    }
}
