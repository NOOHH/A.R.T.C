<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentCourse extends Model
{
    use HasFactory;

    protected $table = 'enrollment_courses';

    protected $fillable = [
        'enrollment_id',
        'course_id',
        'module_id',
        'enrollment_type',
        'course_price',
        'is_active',
    ];

    protected $casts = [
        'course_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the enrollment that owns this course enrollment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }

    /**
     * Get the course for this enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'subject_id');
    }

    /**
     * Get the module for this enrollment.
     */
    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'modules_id');
    }
}
