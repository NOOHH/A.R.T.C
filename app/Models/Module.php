<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';
    protected $primaryKey = 'modules_id';
    public $incrementing = true;             // Keep true if using auto-increment int
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'module_name',
        'module_description',
        'program_id',
        'attachment',
        'created_by_admin_id',
        'content_type',
        'content_data',
        'is_archived',
        'module_order',
    ];

    protected $casts = [
        'content_data' => 'array',
        'is_archived' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    // Helper method to get content type display name
    public function getContentTypeDisplayAttribute()
    {
        return match($this->content_type) {
            'module' => 'Module/Lesson',
            'assignment' => 'Assignment',
            'quiz' => 'Quiz',
            'test' => 'Test',
            'link' => 'External Link',
            'file' => 'File Upload',
            default => 'Module/Lesson',
        };
    }

    // Helper method to get content type icon
    public function getContentTypeIconAttribute()
    {
        return $this->attributes['content_type_icon'] ?? match($this->content_type) {
            'module' => '📚',
            'assignment' => '📝',
            'quiz' => '❓',
            'test' => '📋',
            'link' => '🔗',
            'file' => '📎',
            default => '📚',
        };
    }
    
    // Relationship for module completions
    public function completions()
    {
        return $this->hasMany(ModuleCompletion::class, 'module_id', 'modules_id');
    }
    
    // Check if a student has completed this module
    public function isCompletedBy($studentId)
    {
        return $this->completions()->where('student_id', $studentId)->exists();
    }
}
