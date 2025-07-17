<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $table = 'course_enrollments';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'student_id',
        'course_id',
        'enrollment_id',
        'price_paid',
        'enrollment_status',
        'progress',
        'completed_at'
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'progress' => 'integer',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'subject_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }
}
