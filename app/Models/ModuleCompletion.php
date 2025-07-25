<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleCompletion extends Model
{
    use HasFactory;
    
    protected $table = 'module_completions';
    
    protected $fillable = [
        'student_id',
        'modules_id',
        'program_id',
        'completed_at',
        'score',
        'time_spent',
        'submission_data'
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
        'submission_data' => 'array',
        'score' => 'float'
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
    
    public function module()
    {
        return $this->belongsTo(Module::class, 'modules_id', 'modules_id');
    }
    
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}