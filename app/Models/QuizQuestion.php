<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';
    protected $primaryKey = 'id'; // Using id as primary key, not quiz_id
    public $incrementing = true;

    protected $fillable = [
        'quiz_id',
        'quiz_title',
        'program_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'explanation',
        'instructions',
        'points',
        'source_file',
        'is_active',
        'created_by_admin',
        'created_by_professor',
        'tags',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'points' => 'integer',
        'tags' => 'array',
    ];

    /**
     * Get the quiz this question belongs to.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    /**
     * Get the program this question belongs to.
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

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
