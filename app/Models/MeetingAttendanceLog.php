<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MeetingAttendanceLog extends Model
{
    protected $table = 'meeting_attendance_logs';
    protected $primaryKey = 'log_id';
    
    protected $fillable = [
        'meeting_id',
        'student_id',
        'link_clicked_at',
        'attendance_status',
        'marked_by',
        'marked_at',
        'notes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'link_clicked_at' => 'datetime',
        'marked_at' => 'datetime'
    ];

    /**
     * Get the meeting this log belongs to
     */
    public function meeting()
    {
        return $this->belongsTo(ClassMeeting::class, 'meeting_id', 'meeting_id');
    }

    /**
     * Get the student this log belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the professor who marked attendance
     */
    public function markedBy()
    {
        return $this->belongsTo(Professor::class, 'marked_by', 'professor_id');
    }

    /**
     * Log when student clicks meeting link
     */
    public static function logLinkClick($meetingId, $studentId, $request = null)
    {
        $log = static::firstOrCreate(
            [
                'meeting_id' => $meetingId,
                'student_id' => $studentId
            ],
            [
                'attendance_status' => 'absent' // Default status
            ]
        );

        // Update click info
        $log->update([
            'link_clicked_at' => Carbon::now(),
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null
        ]);

        return $log;
    }

    /**
     * Mark attendance status
     */
    public function markAttendance($status, $professorId, $notes = null)
    {
        $this->update([
            'attendance_status' => $status,
            'marked_by' => $professorId,
            'marked_at' => Carbon::now(),
            'notes' => $notes
        ]);
    }

    /**
     * Check if student clicked the link
     */
    public function hasClickedLink()
    {
        return !is_null($this->link_clicked_at);
    }

    /**
     * Get attendance status with styling info
     */
    public function getStatusInfo()
    {
        $statusMap = [
            'present' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Present'],
            'absent' => ['class' => 'danger', 'icon' => 'x-circle', 'text' => 'Absent'],
            'late' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Late'],
            'excused' => ['class' => 'info', 'icon' => 'shield-check', 'text' => 'Excused']
        ];

        return $statusMap[$this->attendance_status] ?? $statusMap['absent'];
    }

    /**
     * Scope for specific meeting
     */
    public function scopeForMeeting($query, $meetingId)
    {
        return $query->where('meeting_id', $meetingId);
    }

    /**
     * Scope for students who clicked the link
     */
    public function scopeClickedLink($query)
    {
        return $query->whereNotNull('link_clicked_at');
    }

    /**
     * Scope by attendance status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('attendance_status', $status);
    }
}
