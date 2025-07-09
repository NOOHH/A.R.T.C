<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';
    protected $primaryKey = 'enrollment_id';
    public $timestamps = true;

    protected $fillable = [
        'student_id',
        'user_id',
        'program_id',
        'package_id',
        'enrollment_type',
        'learning_mode',
        'registration_id',
        'enrollment_status',
        'payment_status',
        'batch_id',
        'batch_access_granted',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    public function batch()
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id', 'batch_id');
    }
}
