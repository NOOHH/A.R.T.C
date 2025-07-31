<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'module_id',
        'program_id',
        'content_id',
        'file_path',
        'original_filename',
        'comments',
        'files',
        'submitted_at',
        'status',
        'grade',
        'feedback',
        'graded_at',
        'graded_by'
    ];

    protected $casts = [
        'files' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime'
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

    public function contentItem()
    {
        return $this->belongsTo(\App\Models\ContentItem::class, 'content_id', 'id');
    }

    public function gradedByProfessor()
    {
        return $this->belongsTo(Professor::class, 'graded_by', 'professor_id');
    }
}
