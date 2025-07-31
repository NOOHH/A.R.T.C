<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';
    protected $primaryKey = 'certificate_id';
    
    protected $fillable = [
        'student_id',
        'enrollment_id',
        'program_id',
        'certificate_number',
        'student_name',
        'program_name',
        'start_date',
        'completion_date',
        'final_score',
        'certificate_type',
        'status',
        'certificate_data',
        'file_path',
        'qr_code',
        'issued_at',
        'issued_by',
        'rejection_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
        'completion_date' => 'date',
        'issued_at' => 'datetime',
        'final_score' => 'decimal:2',
        'certificate_data' => 'array'
    ];

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber($student_id, $program_id)
    {
        $year = date('Y');
        $prefix = 'ARTC';
        $sequence = str_pad(Certificate::where('certificate_number', 'like', "{$prefix}-{$year}-%")->count() + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(Admin::class, 'issued_by', 'admin_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Accessors & Mutators
     */
    public function getFormattedIssuedAtAttribute()
    {
        return $this->issued_at ? $this->issued_at->format('F j, Y') : null;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('F j, Y') : null;
    }

    public function getFormattedCompletionDateAttribute()
    {
        return $this->completion_date ? $this->completion_date->format('F j, Y') : null;
    }

    public function getDurationAttribute()
    {
        if (!$this->start_date || !$this->completion_date) {
            return null;
        }
        
        $start = Carbon::parse($this->start_date);
        $completion = Carbon::parse($this->completion_date);
        
        return $start->diffInDays($completion);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-info',
            'issued' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Methods
     */
    public function approve($admin_id)
    {
        $this->update([
            'status' => 'approved',
            'issued_by' => $admin_id,
            'issued_at' => now()
        ]);
    }

    public function reject($admin_id, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'issued_by' => $admin_id,
            'rejection_reason' => $reason
        ]);
    }

    public function issue($file_path, $qr_code = null)
    {
        $this->update([
            'status' => 'issued',
            'file_path' => $file_path,
            'qr_code' => $qr_code,
            'issued_at' => now()
        ]);
    }
}
