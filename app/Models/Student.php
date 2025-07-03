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

    protected $fillable = [
        'student_id',
        'user_id',
        'firstname',
        'middlename',
        'lastname',
        'student_school',
        'street_address',
        'state_province',
        'city',
        'zipcode',
        'contact_number',
        'emergency_contact_number',
        'good_moral',
        'PSA',
        'Course_Cert',
        'TOR',
        'Cert_of_Grad',
        'Undergraduate',
        'Graduate',
        'photo_2x2',
        'Start_Date',
        'date_approved',
        'program_id',
        'package_id',
        'plan_id',
        'package_name',
        'plan_name',
        'program_name',
        'email',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

}
