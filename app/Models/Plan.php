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
        'enable_synchronous',
        'enable_asynchronous',
        'learning_mode_config'
    ];

    protected $casts = [
        'enable_synchronous' => 'boolean',
        'enable_asynchronous' => 'boolean',
        'learning_mode_config' => 'array'
    ];

    /**
     * Get available learning modes for this plan
     */
    public function getAvailableLearningModes()
    {
        $modes = [];
        
        if ($this->enable_synchronous) {
            $modes[] = 'synchronous';
        }
        
        if ($this->enable_asynchronous) {
            $modes[] = 'asynchronous';
        }
        
        return $modes;
    }

    /**
     * Check if a learning mode is enabled for this plan
     */
    public function isLearningModeEnabled($mode)
    {
        switch ($mode) {
            case 'synchronous':
                return $this->enable_synchronous;
            case 'asynchronous':
                return $this->enable_asynchronous;
            default:
                return false;
        }
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'plan_id', 'plan_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'plan_id', 'plan_id');
    }
}
