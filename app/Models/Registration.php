<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations'; // updated table name

    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'email',
        'contact',
        'address',
        'birthdate',
        'gender',
        // Add more fields as needed
    ];
}
