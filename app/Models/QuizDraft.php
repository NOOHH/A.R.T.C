<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizDraft extends Model
{
    use HasFactory;

    protected $table = 'quiz_drafts';
    protected $primaryKey = 'draft_id';

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
        'allow_retakes',
        'instant_feedback',
        'show_correct_answers',
        'max_attempts',
        'randomize_order',
        'tags',
        'quiz_source',
        'quiz_settings',
    ];

    protected $casts = [
        'allow_retakes' => 'boolean',
        'instant_feedback' => 'boolean',
        'show_correct_answers' => 'boolean',
        'randomize_order' => 'boolean',
        'tags' => 'array',
        'quiz_settings' => 'array',
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

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
