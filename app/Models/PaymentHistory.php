<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_history';
    protected $primaryKey = 'payment_history_id';
    public $timestamps = true;

    protected $fillable = [
        'enrollment_id',
        'user_id',
        'student_id',
        'program_id',
        'package_id',
        'amount',
        'payment_status',
        'payment_method',
        'payment_notes',
        'payment_date',
        'processed_by_admin_id',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    public function processedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'processed_by_admin_id', 'admin_id');
    }
}
