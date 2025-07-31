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

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    /**
     * Get the creator of the announcement (either admin or professor)
     */
    public function getCreator()
    {
        if ($this->admin_id) {
            return $this->admin;
        } elseif ($this->professor_id) {
            return $this->professor;
        }
        return null;
    }

    /**
     * Get the creator's name
     */
    public function getCreatorName()
    {
        $creator = $this->getCreator();
        if (!$creator) {
            return 'Unknown';
        }

        if ($this->admin_id) {
            return $creator->admin_name ?? ($creator->first_name && $creator->last_name ? $creator->first_name . ' ' . $creator->last_name : $creator->email);
        } elseif ($this->professor_id) {
            return $creator->professor_name ?? ($creator->professor_first_name && $creator->professor_last_name ? $creator->professor_first_name . ' ' . $creator->professor_last_name : ($creator->first_name && $creator->last_name ? $creator->first_name . ' ' . $creator->last_name : $creator->email));
        }

        return 'Unknown';
    }

    /**
     * Get the creator's avatar/profile picture
     */
    public function getCreatorAvatar()
    {
        $creator = $this->getCreator();
        if (!$creator) {
            return null;
        }

        // Check if avatar field exists and has value
        if (isset($creator->avatar) && $creator->avatar) {
            return $creator->avatar;
        }

        return null;
    }
}
