<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ReferralCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReferralController extends Controller
{
    /**
     * Validate a referral code
     */
    public function validateReferralCode(Request $request): JsonResponse
    {
        $request->validate([
            'referral_code' => 'required|string|max:20'
        ]);
        
        $referralCode = strtoupper(trim($request->input('referral_code')));
        
        // Get student ID from session if available
        $studentId = session('student_id') ?? $request->input('student_id');
        
        // Validate the referral code
        $validation = ReferralCodeGenerator::validateReferralCode($referralCode, $studentId);
        
        if ($validation['valid']) {
            $referralInfo = $validation['referral_info'];
            
            return response()->json([
                'valid' => true,
                'referrer_name' => $referralInfo['name'],
                'referrer_type' => ucfirst($referralInfo['type']),
                'referrer_email' => $referralInfo['email'],
                'message' => 'Valid referral code'
            ]);
        } else {
            return response()->json([
                'valid' => false,
                'message' => $validation['message']
            ], 400);
        }
    }
    
    /**
     * Get referral analytics (admin and director access)
     */
    public function getReferralAnalytics(Request $request): JsonResponse
    {
        // Check admin or director authentication
        $userType = session('user_type');
        if (!$userType || !in_array($userType, ['admin', 'director'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $year = $request->input('year', date('Y'));
        $limit = $request->input('limit', 10);
        
        try {
            // Get monthly stats
            $monthlyStats = \App\Models\Referral::getMonthlyStats($year);
            
            // Get top referrers
            $topReferrers = \App\Models\Referral::getTopReferrers($limit);
            
            // Get total stats
            $totalReferrals = \App\Models\Referral::count();
            $totalThisMonth = \App\Models\Referral::whereMonth('used_at', now()->month)
                                                ->whereYear('used_at', now()->year)
                                                ->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'monthly_stats' => $monthlyStats,
                    'top_referrers' => $topReferrers,
                    'total_referrals' => $totalReferrals,
                    'total_this_month' => $totalThisMonth,
                    'year' => $year
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get individual referrer stats
     */
    public function getReferrerStats(Request $request, $type, $id): JsonResponse
    {
        // Check admin or director authentication
        $userType = session('user_type');
        if (!$userType || !in_array($userType, ['admin', 'director'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if (!in_array($type, ['director', 'professor'])) {
            return response()->json(['error' => 'Invalid referrer type'], 400);
        }
        
        try {
            $stats = \App\Models\Referral::getStatsForReferrer($type, $id);
            
            // Get referrer info
            if ($type === 'director') {
                $referrer = \App\Models\Director::find($id);
                $referrerName = $referrer ? $referrer->directors_name : 'Unknown Director';
                $referralCode = $referrer ? $referrer->referral_code : '';
            } else {
                $referrer = \App\Models\Professor::find($id);
                $referrerName = $referrer ? $referrer->professor_name : 'Unknown Professor';
                $referralCode = $referrer ? $referrer->referral_code : '';
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'referrer_name' => $referrerName,
                    'referrer_type' => $type,
                    'referral_code' => $referralCode,
                    'stats' => $stats
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch referrer stats: ' . $e->getMessage()
            ], 500);
        }
    }
}
