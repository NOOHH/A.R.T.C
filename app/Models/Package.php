<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $primaryKey = 'package_id';
    public $timestamps = true;

    protected $fillable = [
        'package_name',
        'description',
        'amount',
        'program_id',
        'created_by_admin_id',
        'package_type',
        'module_count',
        'price',
        'status',
    ];

    /**
     * Get all enrollments that have chosen this package.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(
            Enrollment::class,
            'package_id',
            'package_id'
        );
    }

    /**
     * The program this package belongs to.
     */
    public function program()
    {
        return $this->belongsTo(
            Program::class,
            'program_id',
            'program_id'
        );
    }

    /**
     * Get all modules associated with this package.
     */
    public function modules()
    {
        return $this->belongsToMany(
            Module::class,
            'package_modules',
            'package_id',
            'module_id'
        );
    }

    /**
     * Get all registrations with this package.
     */
    public function registrations()
    {
        return $this->hasMany(
            Registration::class,
            'package_id',
            'package_id'
        );
    }
}
