<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'is_active',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * A program has many modules.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'program_id', 'program_id');
    }

    /**
     * A program has many packages.
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'program_id', 'program_id');
    }

    /**
     * A program’s enrolled students via the enrollments pivot.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,      // Related model
            'enrollments',       // Pivot table
            'program_id',        // This table’s FK on pivot
            'student_id',        // Other table’s FK on pivot
            'program_id',        // Local PK
            'student_id'         // Related model’s PK
        );
    }

    /**
     * Alias if you prefer a hasMany-style name.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'program_id', 'program_id');
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
        return $this->belongsToMany(
            Director::class,
            'director_program',
            'program_id',
            'director_id',
            'program_id',
            'directors_id'
        );
    }

    public function professors()
    {
        return $this->belongsToMany(
            Professor::class,
            'professor_program',
            'program_id',
            'professor_id'
        )
        ->withPivot('video_link', 'video_description')
        ->withTimestamps();
    }

    public function batches()
    {
        return $this->hasMany(StudentBatch::class, 'program_id', 'program_id');
    }
}
