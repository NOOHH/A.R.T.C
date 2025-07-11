<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardPasser extends Model
{
    use HasFactory;

    protected $table = 'board_passers';

    protected $fillable = [
        'student_id',
        'board_exam',
        'exam_year',
        'exam_date',
        'result',
        'notes'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'exam_year' => 'integer'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopePassers($query)
    {
        return $query->where('result', 'PASS');
    }

    public function scopeFailers($query)
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
}
