<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plan';
    protected $primaryKey = 'plan_id';
    public $timestamps = false; // Based on the schema, the plan table doesn't have timestamps

    protected $fillable = [
        'plan_id',
        'plan_name',
        'description',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'plan_id', 'plan_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'plan_id', 'plan_id');
    }
}
