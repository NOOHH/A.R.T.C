<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    // ✅ Specify the correct table name (if it’s not plural)
    protected $table = 'enrollments';

    // ✅ Tell Laravel that the primary key is `enrollment_id`
    protected $primaryKey = 'enrollment_id';

    // ✅ If your PK is not auto-incrementing integer, you'd need:
    // public $incrementing = true;

    // ✅ If you're using timestamps like created_at/updated_at
    public $timestamps = true;

    protected $fillable = [
        'program_id',
        'package_id',
        'enrollment_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
