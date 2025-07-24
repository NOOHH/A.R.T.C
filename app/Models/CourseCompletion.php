<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCompletion extends Model
{
    protected $table = 'course_completions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'student_id', 'course_id', 'module_id', 'completed_at',
    ];

    // Relationships
    public function student() {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
    public function course() {
        return $this->belongsTo(Course::class, 'course_id', 'subject_id');
    }
    public function module() {
        return $this->belongsTo(Module::class, 'module_id', 'modules_id');
    }
} 