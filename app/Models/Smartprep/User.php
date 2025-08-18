<?php

namespace App\Models\Smartprep;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    // Force this model to always use the main database connection
    protected $connection = 'mysql';

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'username',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
