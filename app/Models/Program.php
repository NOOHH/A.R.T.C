<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $primaryKey = 'program_id';
    protected $fillable = [
        'program_name',
        'program_description',
        'created_by_admin_id',
    ];
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'program_id', 'program_id');
    }
}
