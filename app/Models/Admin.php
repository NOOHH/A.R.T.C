<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use Notifiable;
    
    // Tell Laravel the custom primary key name
    protected $primaryKey = 'admin_id';
    
    // If your PK is NOT a UUID or string, this should remain true (default)
    public $incrementing = true;
    
    // Tell Laravel it's an integer (default, so optional)
    protected $keyType = 'int';

    protected $fillable = [
        'admin_name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Hash the password when setting it
     */
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            // Check if password is already hashed by looking for bcrypt pattern
            if (strlen($password) === 60 && preg_match('/^\$2[aby]\$\d{1,2}\$[.\/A-Za-z0-9]{53}$/', $password)) {
                $this->attributes['password'] = $password;
            } else {
                $this->attributes['password'] = Hash::make($password);
            }
        }
    }
}
