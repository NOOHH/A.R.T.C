<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    // Tell Laravel the custom primary key name
    protected $primaryKey = 'user_id';
    // If your PK is NOT a UUID or string, this should remain true (default)
    public $incrementing = true;
    // Tell Laravel it's an integer (default, so optional)
    protected $keyType = 'int';

    protected $fillable = [
        'user_firstname',
        'user_lastname',
        'email',
        'password',
        'role',
        'admin_id',
        'directors_id',
        'is_online',
        'last_seen',
        'enrollment_id',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }
    
    public function registration()
    {
        return $this->hasOne(Registration::class, 'user_id', 'user_id');
    }

    /**
     * Set user as online
     */
    public function setOnline()
    {
        $this->is_online = true;
        $this->last_seen = now();
        $this->save();
    }

    /**
     * Set user as offline
     */
    public function setOffline()
    {
        $this->is_online = false;
        $this->last_seen = now();
        $this->save();
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return trim($this->user_firstname . ' ' . $this->user_lastname);
    }

    /**
     * Student relationship
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'user_id');
    }

    /**
     * Professor relationship
     */
    public function professor()
    {
        return $this->hasOne(Professor::class, 'user_id', 'user_id');
    }

    /**
     * Admin relationship
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'user_id');
    }

    /**
     * Director relationship
     */
    public function director()
    {
        return $this->hasOne(Director::class, 'user_id', 'user_id');
    }

    /**
     * Sent messages relationship
     */
    public function sentMessages()
    {
        return $this->hasMany(Chat::class, 'sender_id', 'user_id');
    }

    /**
     * Received messages relationship
     */
    public function receivedMessages()
    {
        return $this->hasMany(Chat::class, 'receiver_id', 'user_id');
    }
}
