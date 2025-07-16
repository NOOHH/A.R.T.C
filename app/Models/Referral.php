<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $table = 'referrals';
    protected $primaryKey = 'referral_id';

    protected $fillable = [
        'referral_code',
        'referrer_type',
        'referrer_id',
        'student_id',
        'registration_id',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Get the referrer (director or professor)
     */
    public function referrer()
    {
        if ($this->referrer_type === 'director') {
            return $this->belongsTo(Director::class, 'referrer_id', 'directors_id');
        } else {
            return $this->belongsTo(Professor::class, 'referrer_id', 'professor_id');
        }
    }

    /**
     * Get the student who used the referral
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the registration associated with this referral
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id', 'registration_id');
    }

    /**
     * Scope to get referrals by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('referrer_type', $type);
    }

    /**
     * Scope to get referrals by referrer
     */
    public function scopeByReferrer($query, $type, $id)
    {
        return $query->where('referrer_type', $type)->where('referrer_id', $id);
    }

    /**
     * Get referral analytics for a specific referrer
     */
    public static function getAnalyticsForReferrer($type, $id)
    {
        return static::byReferrer($type, $id)
            ->with(['student', 'registration'])
            ->orderBy('used_at', 'desc')
            ->get();
    }

    /**
     * Get overall referral analytics
     */
    public static function getOverallAnalytics()
    {
        return [
            'total_referrals' => static::count(),
            'director_referrals' => static::byType('director')->count(),
            'professor_referrals' => static::byType('professor')->count(),
            'monthly_referrals' => static::whereMonth('used_at', now()->month)
                                         ->whereYear('used_at', now()->year)
                                         ->count(),
            'top_referrers' => static::selectRaw('referrer_type, referrer_id, COUNT(*) as referral_count')
                                   ->groupBy('referrer_type', 'referrer_id')
                                   ->orderBy('referral_count', 'desc')
                                   ->limit(10)
                                   ->get()
        ];
    }

    /**
     * Get monthly statistics for a given year
     */
    public static function getMonthlyStats($year = null)
    {
        $year = $year ?: date('Y');
        
        return static::selectRaw('MONTH(used_at) as month, COUNT(*) as count')
                     ->whereYear('used_at', $year)
                     ->groupBy('month')
                     ->orderBy('month')
                     ->get()
                     ->map(function ($item) {
                         return [
                             'month' => $item->month,
                             'count' => $item->count,
                             'month_name' => date('F', mktime(0, 0, 0, $item->month, 1))
                         ];
                     });
    }

    /**
     * Get top referrers with their details
     */
    public static function getTopReferrers($limit = 10)
    {
        $topReferrers = static::selectRaw('referrer_type, referrer_id, COUNT(*) as referral_count')
                              ->groupBy('referrer_type', 'referrer_id')
                              ->orderBy('referral_count', 'desc')
                              ->limit($limit)
                              ->get();

        return $topReferrers->map(function ($referrer) {
            $details = null;
            if ($referrer->referrer_type === 'director') {
                $details = \App\Models\Director::find($referrer->referrer_id);
                $name = $details ? $details->directors_first_name . ' ' . $details->directors_last_name : 'Unknown Director';
                $email = $details ? $details->directors_email : 'N/A';
                $code = $details ? $details->referral_code : 'N/A';
            } else {
                $details = \App\Models\Professor::find($referrer->referrer_id);
                $name = $details ? $details->professor_first_name . ' ' . $details->professor_last_name : 'Unknown Professor';
                $email = $details ? $details->professor_email : 'N/A';
                $code = $details ? $details->referral_code : 'N/A';
            }

            return [
                'type' => $referrer->referrer_type,
                'id' => $referrer->referrer_id,
                'name' => $name,
                'email' => $email,
                'referral_code' => $code,
                'referral_count' => $referrer->referral_count
            ];
        });
    }
}
