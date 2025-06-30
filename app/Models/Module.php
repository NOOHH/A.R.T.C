<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';
    protected $primaryKey = 'modules_id';
    public $incrementing = true;             // or false if you’re using strings
    protected $keyType = 'int';              // adjust if string

    protected $fillable = [
        'module_name',
        'module_description',
        'program_id',
        'plan_id',
        'attachment',
        'created_by_admin_id',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}
