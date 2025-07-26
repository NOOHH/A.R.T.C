<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempts';
    protected $primaryKey = 'attempt_id';

    protected $fillable = [
        'quiz_id',
        'student_id',
        'answers',
        'score',
        'total_questions',
        'correct_answers',
        'started_at',
        'completed_at',
        'time_taken',
        'status',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Scope for completed attempts
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope for in progress attempts
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Get the best score for a student on a quiz
    public static function getBestScore($quizId, $studentId)
    {
        return self::where('quiz_id', $quizId)
                   ->where('student_id', $studentId)
                   ->where('status', 'completed')
                   ->max('score');
    }

    // Get the last score for a student on a quiz
    public static function getLastScore($quizId, $studentId)
    {
        $attempt = self::where('quiz_id', $quizId)
                       ->where('student_id', $studentId)
                       ->where('status', 'completed')
                       ->orderBy('completed_at', 'desc')
                       ->first();
        
        return $attempt ? $attempt->score : null;
    }

    // Get attempt count for a student on a quiz
    public static function getAttemptCount($quizId, $studentId)
    {
        return self::where('quiz_id', $quizId)
                   ->where('student_id', $studentId)
                   ->count();
    }
}
