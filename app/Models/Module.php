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
        'batch_id',
        'learning_mode',
        'attachment',
        'created_by_admin_id',
        'content_type',
        'content_data',
        'is_archived',
        'module_order',
        'video_path',
        'additional_content',
        'order',
        'admin_override',
    ];

    protected $casts = [
        'content_data' => 'array',
        'is_archived' => 'boolean',
        'module_order' => 'integer',
        'order' => 'integer',
        'admin_override' => 'array',
    ];

    protected $appends = [
        'id',
        'name',
        'description',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    // Accessors to provide compatibility with expected field names
    public function getIdAttribute()
    {
        return $this->modules_id;
    }

    public function getNameAttribute()
    {
        return $this->module_name;
    }

    public function getDescriptionAttribute()
    {
        return $this->module_description;
    }

    public function batch()
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id', 'batch_id');
    }

    // Helper method to get content type display name
    public function getContentTypeDisplayAttribute()
    {
        return match($this->content_type) {
            'module' => 'Module/Lesson',
            'assignment' => 'Assignment',
            'quiz' => 'Quiz',
            'ai_quiz' => 'AI-Powered Quiz',
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
            'ai_quiz' => '🤖',
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

    // Scope for ordering modules
    public function scopeOrdered($query)
    {
        return $query->orderBy('module_order');
    }
}
