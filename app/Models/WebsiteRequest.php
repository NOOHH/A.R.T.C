<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteRequest extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'description',
        'domain_preference',
        'contact_email',
        'contact_phone',
        'template_data',
        'status',
        'admin_notes',
        'client_id',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'template_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        // SmartPrep requests are made by SmartPrep users
        return $this->belongsTo(\App\Models\Smartprep\User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'completed' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'completed' => 'Website Created',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }
}

