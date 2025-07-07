<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    use HasFactory;

    protected $table = 'student_grades';

    protected $fillable = [
        'student_id',
        'program_id',
        'professor_id',
        'assignment_type',
        'assignment_title',
        'score',
        'max_score',
        'percentage',
        'feedback',
        'graded_date'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'graded_date' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($grade) {
            // Auto-calculate percentage
            if ($grade->score && $grade->max_score) {
                $grade->percentage = ($grade->score / $grade->max_score) * 100;
            }
        });
    }

    /**
     * Get the student that owns the grade.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the program associated with the grade.
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    /**
     * Get the professor that assigned the grade.
     */
    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id', 'professor_id');
    }

    /**
     * Scope to get grades for a specific assignment type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('assignment_type', $type);
    }

    /**
     * Get the letter grade based on percentage.
     */
    public function getLetterGradeAttribute()
    {
        if (!$this->percentage) return 'N/A';

        if ($this->percentage >= 90) return 'A';
        if ($this->percentage >= 80) return 'B';
        if ($this->percentage >= 70) return 'C';
        if ($this->percentage >= 60) return 'D';
        return 'F';
    }

    /**
     * Check if the grade is passing.
     */
    public function getIsPassingAttribute()
    {
        return $this->percentage >= 60;
    }
}
