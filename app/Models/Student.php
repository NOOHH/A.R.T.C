<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    public $timestamps = false; // Disable timestamps since your table does not have created_at/updated_at

    // Optionally, add fillable fields for mass assignment
    protected $fillable = [
        'user_id', 'firstname', 'middlename', 'lastname', 'student_school', 'street_address', 'state_province', 'city', 'zipcode', 'email', 'contact_number', 'emergency_contact_number', 'good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 'Undergraduate', 'Graduate', 'photo_2x2', 'Start_Date', 'date_approved'
    ];
}
