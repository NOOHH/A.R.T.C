<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentCompletion extends Model
{
    protected $table = 'content_completions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'student_id', 'content_id', 'course_id', 'module_id', 'completed_at',
    ];

    // Relationships
    public function student() {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
    public function content() {
        return $this->belongsTo(ContentItem::class, 'content_id', 'id');
    }
    public function course() {
        return $this->belongsTo(Course::class, 'course_id', 'subject_id');
    }
    public function module() {
        return $this->belongsTo(Module::class, 'module_id', 'modules_id');
    }
} 