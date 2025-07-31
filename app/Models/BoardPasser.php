<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardPasser extends Model
{
    use HasFactory;

    protected $table = 'board_passers';

    protected $primaryKey = 'passer_id';
    
    protected $fillable = [
        'student_id',
        'student_name',
        'program',
        'board_exam',
        'exam_year',
        'exam_date',
        'result',
        'rating',
        'notes'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'rating' => 'decimal:2',
        'exam_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship with Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Scope for passed students
    public function scopePassed($query)
    {
        return $query->where('result', 'PASS');
    }

    // Scope for failed students
    public function scopeFailed($query)
    {
        return $query->where('result', 'FAIL');
    }

    public function scopeByExam($query, $exam)
    {
        return $query->where('board_exam', $exam);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('exam_year', $year);
    }

    // Get formatted exam date
    public function getFormattedExamDateAttribute()
    {
        return $this->exam_date ? $this->exam_date->format('M d, Y') : null;
    }

    // Get result badge class
    public function getResultBadgeClassAttribute()
    {
        return $this->result === 'PASS' ? 'badge-success' : 'badge-danger';
    }

    // Get rating percentage
    public function getRatingPercentageAttribute()
    {
        return $this->rating ? $this->rating . '%' : 'N/A';
    }
}
