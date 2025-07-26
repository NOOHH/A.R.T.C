<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';
    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'admin_id',
        'professor_id',
        'program_id',
        'title',
        'content',
        'description',
        'type',
        'video_link',
        'target_users',
        'target_programs',
        'target_batches',
        'target_plans',
        'target_scope',
        'publish_date',
        'expire_date',
        'is_published',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'target_users' => 'array',
        'target_programs' => 'array',
        'target_batches' => 'array',
        'target_plans' => 'array',
        'publish_date' => 'datetime',
        'expire_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id', 'professor_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}
