<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'content_id',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
        'submission_notes',
        'status',
        'grade',
        'feedback',
        'submitted_at',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'grade' => 'decimal:2',
        'file_size' => 'integer',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function contentItem()
    {
        return $this->belongsTo(ContentItem::class, 'content_id');
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by', 'user_id');
    }

    // Accessors
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByContent($query, $contentId)
    {
        return $query->where('content_id', $contentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
