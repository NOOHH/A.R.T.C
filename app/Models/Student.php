<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'student_id';
    public $incrementing = false; // student_id is a string
    protected $keyType = 'string';
    public $timestamps = true;

    /**
     * Allow mass assignment for dynamic fields
     * Using guarded instead of fillable to allow dynamic form requirement fields
     * Only protect critical fields from mass assignment
     */
    protected $guarded = ['created_at', 'updated_at'];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    
    // Single program relationship (for backward compatibility)
    // This gets the first program the student is enrolled in
    public function program()
    {
        return $this->hasOneThrough(
            \App\Models\Program::class,
            \App\Models\Enrollment::class,
            'student_id', // Foreign key on enrollments table
            'program_id', // Foreign key on programs table
            'student_id', // Local key on students table
            'program_id'  // Local key on enrollments table
        );
    }
    
    // Enrollments relationship - a student can have multiple enrollments
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class, 'student_id', 'student_id');
    }
    
    // Get programs through enrollments
    public function programs()
    {
        return $this->hasManyThrough(
            \App\Models\Program::class,
            \App\Models\Enrollment::class,
            'student_id', // Foreign key on enrollments table
            'program_id', // Foreign key on programs table
            'student_id', // Local key on students table
            'program_id'  // Local key on enrollments table
        );
    }
    
    // Get packages through enrollments
    public function packages()
    {
        return $this->hasManyThrough(
            \App\Models\Package::class,
            \App\Models\Enrollment::class,
            'student_id', // Foreign key on enrollments table
            'package_id', // Foreign key on packages table
            'student_id', // Local key on students table
            'package_id'  // Local key on enrollments table
        );
    }
    
    // Module completions relationship
    public function moduleCompletions()
    {
        return $this->hasMany(\App\Models\ModuleCompletion::class, 'student_id', 'student_id');
    }
    
    // Get attendance records for this student
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }
    
    // Get grades for this student
    public function grades()
    {
        return $this->hasMany(StudentGrade::class, 'student_id', 'student_id');
    }
    
    // Get all completed module IDs for this student
    public function getCompletedModuleIdsAttribute()
    {
        return $this->moduleCompletions->pluck('module_id')->toArray();
    }
    
    // Check if student has completed a specific module
    public function hasCompleted($moduleId)
    {
        return $this->moduleCompletions()->where('module_id', $moduleId)->exists();
    }
    
    // Get program progress
    public function getProgramProgress($programId)
    {
        $totalModules = \App\Models\Module::where('program_id', $programId)
                                        ->where('is_archived', false)
                                        ->count();
                                        
        if ($totalModules === 0) {
            return 0;
        }
        
        $completedModules = $this->moduleCompletions()
                                ->where('program_id', $programId)
                                ->count();
                                
        return round(($completedModules / $totalModules) * 100);
    }

    // Latest enrollment relationship (for admin views)
    public function enrollment()
    {
        return $this->hasOne(\App\Models\Enrollment::class, 'student_id', 'student_id')->latest();
    }
}
