<?php

namespace App\Models\Smartprep;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    // Always use main SmartPrep database
    protected $connection = 'mysql';
    protected $table = 'users';
    // Current live database still uses `id` as primary key (migration file shows user_id but DB not migrated) -> use id
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_firstname',
        'user_lastname',
        'email',
        'username',
        'password',
        'role',
        'enrollment_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // If later you migrate to 'user_id', update $primaryKey above and optionally keep an accessor for id

    /**
     * Hash the password when setting it (idempotent if already bcrypt)
     */
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            if (strlen($password) === 60 && preg_match('/^\$2[aby]\$\d{1,2}\$[\.\/A-Za-z0-9]{53}$/', $password)) {
                $this->attributes['password'] = $password; // already hashed
            } else {
                $this->attributes['password'] = Hash::make($password);
            }
        }
    }
}
