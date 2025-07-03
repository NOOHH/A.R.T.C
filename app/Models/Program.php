<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';
    protected $primaryKey = 'program_id';
    public $timestamps = true;

    protected $fillable = [
        'program_name',
        'program_description',
        'created_by_admin_id',
        'director_id',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'program_id', 'program_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'program_id', 'program_id');
    }

    public function registrations()
    {
        return $this->students();
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'program_id', 'program_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id', 'admin_id');
    }

    public function director()
    {
        return $this->belongsTo(Director::class, 'director_id', 'directors_id');
    }
    
    public function assignedDirectors()
    {
        return $this->belongsToMany(Director::class, 'director_program', 'program_id', 'director_id', 'program_id', 'directors_id');
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'professor_program')
                    ->withPivot('video_link', 'video_description')
                    ->withTimestamps();
    }
}
