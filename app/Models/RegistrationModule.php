<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationModule extends Model
{
    use HasFactory;

    protected $table = 'registration_modules';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'registration_id',
        'module_id'
    ];

    // Relationships
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id', 'registration_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'modules_id');
    }
}
