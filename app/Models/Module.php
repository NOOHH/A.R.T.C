<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';
    protected $primaryKey = 'modules_id';
    public $incrementing = true;             // or false if you’re using strings
    protected $keyType = 'int';              // adjust if string

    protected $fillable = [
        'module_name',
        'module_description',
        'program_id',
        'plan_id',
        'attachment',
        'created_by_admin_id',
        'content_type',
        'content_data',
        'is_archived'
    ];

    protected $casts = [
        'content_data' => 'array',
        'is_archived' => 'boolean'
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
            default => 'Module/Lesson'
        };
    }

    // Helper method to get content type icon
    public function getContentTypeIconAttribute()
    {
        return match($this->content_type) {
            'module' => '📚',
            'assignment' => '📝',
            'quiz' => '❓',
            'test' => '📋',
            'link' => '🔗',
            default => '📚'
        };
    }
}
