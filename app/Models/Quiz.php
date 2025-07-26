<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';
    protected $primaryKey = 'quiz_id';

    protected $fillable = [
        'professor_id',
        'program_id',
        'module_id',
        'course_id', 
        'content_id',
        'quiz_title',
        'quiz_description',
        'instructions',
        'total_questions',
        'time_limit',
        'document_path',
        'is_active',
        'is_draft',
        'status',
        'allow_retakes',
        'instant_feedback',
        'show_correct_answers',
        'max_attempts',
        'randomize_order',
        'randomize_mc_options',
        'tags',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_draft' => 'boolean',
        'allow_retakes' => 'boolean',
        'instant_feedback' => 'boolean',
        'show_correct_answers' => 'boolean',
        'randomize_order' => 'boolean',
        'randomize_mc_options' => 'boolean',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id', 'professor_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id', 'quiz_id');
    }
}
