<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClassMeeting extends Model
{
    protected $table = 'class_meetings';
    protected $primaryKey = 'meeting_id';
    
    protected $fillable = [
        'batch_id',
        'professor_id',
        'title',
        'description',
        'meeting_date',
        'duration_minutes',
        'meeting_url',
        'attached_files',
        'status',
        'created_by',
        'url_visible_before_meeting',
        'url_visibility_minutes_before',
        'actual_start_time',
        'actual_end_time'
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
        'attached_files' => 'array',
        'url_visible_before_meeting' => 'boolean',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime'
    ];

    /**
     * Get the batch this meeting belongs to
     */
    public function batch()
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id', 'batch_id');
    }

    /**
     * Get the professor conducting this meeting
     */
    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id', 'professor_id');
    }

    /**
     * Get the admin/director who created this meeting
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }

    /**
     * Get attendance logs for this meeting
     */
    public function attendanceLogs()
    {
        return $this->hasMany(MeetingAttendanceLog::class, 'meeting_id', 'meeting_id');
    }

    /**
     * Get students who clicked the meeting link
     */
    public function studentsWhoClicked()
    {
        return $this->belongsToMany(Student::class, 'meeting_attendance_logs', 'meeting_id', 'student_id')
                    ->whereNotNull('meeting_attendance_logs.link_clicked_at')
                    ->withPivot(['link_clicked_at', 'attendance_status', 'marked_at', 'notes']);
    }

    /**
     * Check if the meeting URL should be visible
     */
    public function isUrlVisible()
    {
        if (!$this->url_visible_before_meeting || !$this->meeting_url) {
            return false;
        }

        $now = Carbon::now();
        $meetingTime = Carbon::parse($this->meeting_date);
        $visibilityTime = $meetingTime->subMinutes($this->url_visibility_minutes_before);

        return $now->gte($visibilityTime) && $now->lte($meetingTime->addHours(24)); // URL visible until 24h after meeting
    }

    /**
     * Get time remaining until URL becomes visible (in minutes)
     */
    public function getTimeUntilUrlVisible()
    {
        if (!$this->url_visible_before_meeting) {
            return null;
        }

        $now = Carbon::now();
        $meetingTime = Carbon::parse($this->meeting_date);
        $visibilityTime = $meetingTime->subMinutes($this->url_visibility_minutes_before);

        if ($now->gte($visibilityTime)) {
            return 0; // URL is already visible
        }

        return $now->diffInMinutes($visibilityTime);
    }

    /**
     * Scope for upcoming meetings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('meeting_date', '>', Carbon::now())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope for today's meetings
     */
    public function scopeToday($query)
    {
        return $query->whereDate('meeting_date', Carbon::today())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope for professor's meetings
     */
    public function scopeForProfessor($query, $professorId)
    {
        return $query->where('professor_id', $professorId);
    }

    /**
     * Scope for batch meetings
     */
    public function scopeForBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }
}
