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
        'quiz_title',
        'instructions',
        'total_questions',
        'time_limit',
        'document_path',
        'is_active',
        'is_draft',
        'randomize_order',
        'tags',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_draft' => 'boolean',
        'randomize_order' => 'boolean',
        'tags' => 'array',
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
