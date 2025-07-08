<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    protected $primaryKey = 'activity_id';

    protected $fillable = [
        'professor_id',
        'program_id',
        'title',
        'description',
        'instructions',
        'max_points',
        'due_date',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id', 'professor_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function grades()
    {
        return $this->hasMany(StudentGrade::class, 'reference_id', 'activity_id')
                    ->where('grade_type', 'activity');
    }
}
