<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDeadline extends Model
{
    use HasFactory;

    protected $table = 'student_deadlines';
    protected $primaryKey = 'deadline_id';

    protected $fillable = [
        'student_id',
        'assignment_type',
        'assignment_title',
        'description',
        'due_date',
        'is_completed',
        'quiz_id',
        'module_id',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }
}
