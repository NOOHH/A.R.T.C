<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Professor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'professors';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'professor_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'professor_first_name',
        'professor_last_name', 
        'professor_email',
        'professor_password',
        'professor_name',
        'admin_id',
        'professor_archived',
        'dynamic_data',
        'referral_code',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'professor_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'professor_archived' => 'boolean',
        'dynamic_data' => 'array',
    ];

    /**
     * Get the password attribute (Laravel expects 'password').
     */
    public function getPasswordAttribute()
    {
        return $this->professor_password;
    }

    /**
     * Set the password attribute.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['professor_password'] = $value;
    }

    /**
     * Get the email attribute (Laravel expects 'email').
     */
    public function getEmailAttribute()
    {
        return $this->professor_email;
    }

    /**
     * Set the email attribute.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['professor_email'] = $value;
    }

    /**
     * Get the first name attribute.
     */
    public function getFirstNameAttribute()
    {
        return $this->professor_first_name;
    }

    /**
     * Set the first name attribute.
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['professor_first_name'] = $value;
        $this->updateFullName();
    }

    /**
     * Get the last name attribute.
     */
    public function getLastNameAttribute()
    {
        return $this->professor_last_name;
    }

    /**
     * Set the last name attribute.
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['professor_last_name'] = $value;
        $this->updateFullName();
    }

    /**
     * Update the full name when first or last name changes.
     */
    protected function updateFullName()
    {
        if (isset($this->attributes['professor_first_name']) && isset($this->attributes['professor_last_name'])) {
            $this->attributes['professor_name'] = $this->attributes['professor_first_name'] . ' ' . $this->attributes['professor_last_name'];
        }
    }

    /**
     * Get the full name of the professor.
     */
    public function getFullNameAttribute()
    {
        return $this->professor_name ?: ($this->professor_first_name . ' ' . $this->professor_last_name);
    }

    /**
     * Get the programs that the professor is assigned to.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'professor_program', 'professor_id', 'program_id')
                    ->withPivot('video_link', 'video_description')
                    ->withTimestamps();
    }

    /**
     * Scope to get non-archived professors.
     */
    public function scopeActive($query)
    {
        return $query->where('professor_archived', false);
    }

    /**
     * Scope to get archived professors.
     */
    public function scopeArchived($query)
    {
        return $query->where('professor_archived', true);
    }

    /**
     * Archive the professor.
     */
    public function archive()
    {
        $this->update(['professor_archived' => true]);
    }

    /**
     * Restore the professor.
     */
    public function restore()
    {
        $this->update(['professor_archived' => false]);
    }

    /**
     * Check if professor is archived.
     */
    public function getIsArchivedAttribute()
    {
        return $this->professor_archived;
    }

    /**
     * Get the admin who created this professor.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Get attendance records created by this professor.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class, 'professor_id', 'professor_id');
    }

    /**
     * Get grades assigned by this professor.
     */
    public function gradesAssigned()
    {
        return $this->hasMany(StudentGrade::class, 'professor_id', 'professor_id');
    }

    /**
     * Get quiz questions created by this professor.
     */
    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'created_by_professor', 'professor_id');
    }

    /**
     * Assign a program to this professor.
     */
    public function assignProgram($programId, $videoLink = null, $videoDescription = null)
    {
        return $this->programs()->attach($programId, [
            'video_link' => $videoLink,
            'video_description' => $videoDescription,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Unassign a program from this professor.
     */
    public function unassignProgram($programId)
    {
        return $this->programs()->detach($programId);
    }

    /**
     * Update video link for a specific program assignment.
     */
    public function updateProgramVideo($programId, $videoLink, $videoDescription = null)
    {
        return $this->programs()->updateExistingPivot($programId, [
            'video_link' => $videoLink,
            'video_description' => $videoDescription,
            'updated_at' => now(),
        ]);
    }

    /**
     * Referral relationship - get all referrals made by this professor
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id', 'professor_id')
                    ->where('referrer_type', 'professor');
    }

    /**
     * Get referral analytics for this professor
     */
    public function getReferralAnalytics()
    {
        return [
            'total_referrals' => $this->referrals()->count(),
            'monthly_referrals' => $this->referrals()
                                       ->whereMonth('used_at', now()->month)
                                       ->whereYear('used_at', now()->year)
                                       ->count(),
            'recent_referrals' => $this->referrals()
                                      ->with(['student', 'registration'])
                                      ->orderBy('used_at', 'desc')
                                      ->limit(10)
                                      ->get()
        ];
    }

    /**
     * Many-to-many relationship with batches
     */
    public function batches()
    {
        return $this->belongsToMany(
            StudentBatch::class,
            'professor_batch',
            'professor_id',
            'batch_id'
        )->withPivot(['assigned_at', 'assigned_by'])
         ->withTimestamps();
    }

    /**
     * Get class meetings for this professor
     */
    public function classMeetings()
    {
        return $this->hasMany(ClassMeeting::class, 'professor_id', 'professor_id');
    }

    /**
     * Get upcoming meetings for this professor
     */
    public function upcomingMeetings()
    {
        return $this->classMeetings()->upcoming()->orderBy('meeting_date', 'asc');
    }

    /**
     * Get today's meetings for this professor
     */
    public function todaysMeetings()
    {
        return $this->classMeetings()->today()->orderBy('meeting_date', 'asc');
    }

    public function assignedPrograms()
    {
        return $this->belongsToMany(\App\Models\Program::class, 'professor_program', 'professor_id', 'program_id');
    }
}
