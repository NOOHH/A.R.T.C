<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'db_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'status',
        'user_id',
        'archived'
    ];
    
    protected $casts = [
        'archived' => 'boolean',
    ];

    // Website status constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_OFFLINE = 'offline';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_ARCHIVE = 'archive';

    /**
     * Human readable status label (e.g. 'Active', 'Draft')
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status ?? self::STATUS_ACTIVE);
    }

    /**
     * Badge CSS classes for the current status
     */
    public function getStatusBadgeClassAttribute()
    {
        $status = strtolower($this->status ?? self::STATUS_ACTIVE);
        switch ($status) {
            case self::STATUS_DRAFT:
                return 'badge bg-secondary';
            case self::STATUS_OFFLINE:
                return 'badge bg-warning text-dark';
            case self::STATUS_MAINTENANCE:
                return 'badge bg-info text-dark';
            case self::STATUS_ARCHIVE:
                return 'badge bg-dark';
            default:
                return 'badge bg-success';
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function websiteRequest()
    {
        return $this->hasOne(WebsiteRequest::class);
    }
}


