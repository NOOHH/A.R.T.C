<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'module_id',
        'program_id',
        'quiz_id',
        'answers',
        'score',
        'total_questions',
        'time_taken',
        'time_spent',
        'is_practice',
        'submitted_at',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'time_spent' => 'integer',
        'is_practice' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'modules_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }
}
