<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchProfessor extends Model
{
    protected $table = 'batch_professors';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'batch_id',
        'professor_id',
        'assigned_at',
        'assigned_by'
    ];

    protected $dates = [
        'assigned_at'
    ];

    public function batch()
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id', 'batch_id');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id', 'professor_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Admin::class, 'assigned_by', 'admin_id');
    }
}
