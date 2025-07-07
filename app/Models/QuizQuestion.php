<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';

    protected $fillable = [
        'quiz_title',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'source_file',
        'is_active',
        'created_by_admin',
        'created_by_professor'
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    /**
     * Get the admin who created the question.
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin', 'admin_id');
    }

    /**
     * Get the professor who created the question.
     */
    public function createdByProfessor()
    {
        return $this->belongsTo(Professor::class, 'created_by_professor', 'professor_id');
    }

    /**
     * Scope to get active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get questions by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    /**
     * Scope to get questions by quiz title.
     */
    public function scopeForQuiz($query, $quizTitle)
    {
        return $query->where('quiz_title', $quizTitle);
    }

    /**
     * Get questions created by a specific professor.
     */
    public function scopeByProfessor($query, $professorId)
    {
        return $query->where('created_by_professor', $professorId);
    }

    /**
     * Get questions created by admin.
     */
    public function scopeByAdmin($query, $adminId = null)
    {
        if ($adminId) {
            return $query->where('created_by_admin', $adminId);
        }
        return $query->whereNotNull('created_by_admin');
    }
}
