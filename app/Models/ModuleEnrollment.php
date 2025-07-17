<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleEnrollment extends Model
{
    use HasFactory;

    protected $table = 'module_enrollments';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'student_id',
        'module_id',
        'enrollment_id',
        'is_additional',
        'price_paid',
        'enrollment_status',
        'progress',
        'completed_at'
    ];

    protected $casts = [
        'is_additional' => 'boolean',
        'price_paid' => 'decimal:2',
        'progress' => 'integer',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'modules_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }
}
