<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    // Tell Laravel the custom primary key name
    protected $primaryKey = 'user_id';
    // If your PK is NOT a UUID or string, this should remain true (default)
    public $incrementing = true;
    // Tell Laravel it's an integer (default, so optional)
    protected $keyType = 'int';

    protected $fillable = [
        'user_firstname',
        'user_lastname',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
     public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }
}
