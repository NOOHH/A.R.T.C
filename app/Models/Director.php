<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Director extends Model
{
    use HasFactory;

    protected $table = 'directors';
    protected $primaryKey = 'directors_id';
    public $timestamps = true;

    protected $fillable = [
        'admin_id',
        'directors_name',
        'directors_first_name',
        'directors_last_name',
        'directors_email',
        'directors_password',
        'directors_archived',
        'has_all_program_access',
    ];

    protected $casts = [
        'directors_archived' => 'boolean',
        'has_all_program_access' => 'boolean',
    ];

    // Relationships
    public function programs()
    {
        return $this->hasMany(Program::class, 'director_id', 'directors_id');
    }
    
    public function assignedPrograms()
    {
        return $this->belongsToMany(Program::class, 'director_program', 'director_id', 'program_id', 'directors_id', 'program_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return $this->directors_first_name . ' ' . $this->directors_last_name;
    }
    
    // Get all programs this director has access to
    public function getAllAccessiblePrograms()
    {
        if ($this->has_all_program_access) {
            return Program::where('is_archived', false)->get();
        }
        
        // Merge programs from both relationships
        $allPrograms = collect();
        if ($this->programs->count() > 0) {
            $allPrograms = $allPrograms->merge($this->programs);
        }
        if ($this->assignedPrograms->count() > 0) {
            $allPrograms = $allPrograms->merge($this->assignedPrograms);
        }
        
        return $allPrograms->unique('program_id')->where('is_archived', false);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('directors_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('directors_archived', true);
    }
}
