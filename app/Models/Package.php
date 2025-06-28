<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
<<<<<<< HEAD
    
protected $primaryKey = 'package_id';
=======

    protected $primaryKey = 'package_id';

    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class, 'package_id', 'package_id');
    }
>>>>>>> main
}
