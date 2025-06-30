<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $primaryKey = 'program_id';
    protected $fillable = [
        'program_name',
        'program_description',
        'created_by_admin_id',
    ];
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'program_id', 'program_id');
    }

    /**
     * Get the students enrolled in the program.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'program_id', 'program_id');
    }

    /**
     * Alias for students relationship (for backward compatibility)
     */
    public function registrations()
    {
        return $this->students();
    }

    /**
     * Get the modules for the program.
     */
    public function modules()
    {
        return $this->hasMany(Module::class, 'program_id', 'program_id');
    }
}
