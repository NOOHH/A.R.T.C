<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    // If your PK is not 'id':
    protected $primaryKey = 'package_id';

    protected $fillable = [
        'package_name',
        'description',
        'amount',
        'program_id',
        'created_by_admin_id',
    ];

    /**
     * All the enrollments that have chosen this package.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(
            \App\Models\Enrollment::class,
            'package_id',    // FK on enrollments table
            'package_id'     // PK on packages table
        );
    }

    /**
     * The program this package belongs to.
     */
    public function program()
    {
        return $this->belongsTo(
            \App\Models\Program::class,
            'program_id',    // FK on packages table
            'program_id'     // PK on programs table
        );
    }
}
