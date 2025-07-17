<?php

namespace App\Helpers;

use App\Models\Director;
use App\Models\Professor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralCodeGenerator
{
    /**
     * Generate a unique referral code for a user
     */
    public static function generateCode($firstName, $lastName, $type = 'professor', $userId = null)
    {
        $attempts = 0;
        $maxAttempts = 10;
        
        do {
            $code = self::createCodeFromName($firstName, $lastName, $type, $userId, $attempts);
            $attempts++;
        } while (self::codeExists($code) && $attempts < $maxAttempts);
        
        // If we couldn't generate a unique code from name, generate random
        if (self::codeExists($code)) {
            $code = self::generateRandomCode($type, $userId);
        }
        
        return $code;
    }
    
    /**
     * Create code based on first and last name
     */
    private static function createCodeFromName($firstName, $lastName, $type = 'professor', $userId = null, $attempt = 0)
    {
        // Clean names - remove spaces and special characters
        $firstName = preg_replace('/[^A-Za-z]/', '', $firstName);
        $lastName = preg_replace('/[^A-Za-z]/', '', $lastName);
        
        $firstName = strtoupper($firstName);
        $lastName = strtoupper($lastName);
        
        // Get user type prefix
        $prefix = $type === 'director' ? 'DIR' : 'PROF';
        
        // Get user ID for identification (padded to 2 digits)
        $userIdStr = $userId ? str_pad($userId, 2, '0', STR_PAD_LEFT) : '01';
        
        if ($attempt === 0) {
            // First attempt: PREFIX + FIRST_NAME + USER_ID (e.g., PROFNEON01, DIRJOHN01)
            $nameCode = substr($firstName, 0, 4) . $userIdStr;
            $code = $prefix . $nameCode;
        } elseif ($attempt === 1) {
            // Second attempt: PREFIX + FIRST_INITIAL + LASTNAME + USER_ID (e.g., PROFNVEGA01)
            $nameCode = substr($firstName, 0, 1) . substr($lastName, 0, 4) . $userIdStr;
            $code = $prefix . $nameCode;
        } elseif ($attempt === 2) {
            // Third attempt: PREFIX + FIRST_2 + LAST_2 + USER_ID (e.g., PROFNEVE01)
            $nameCode = substr($firstName, 0, 2) . substr($lastName, 0, 2) . $userIdStr;
            $code = $prefix . $nameCode;
        } elseif ($attempt === 3) {
            // Fourth attempt: PREFIX + consonants + USER_ID
            $consonants = self::extractConsonants($firstName . $lastName);
            $nameCode = substr($consonants, 0, 3) . $userIdStr;
            $code = $prefix . $nameCode;
        } else {
            // Additional attempts: Add numbers
            $nameCode = substr($firstName, 0, 2) . substr($lastName, 0, 1) . $userIdStr . ($attempt - 3);
            $code = $prefix . $nameCode;
        }
        
        // Ensure code is reasonable length (max 12 characters)
        if (strlen($code) > 12) {
            $code = substr($code, 0, 12);
        }
        
        return $code;
    }
    
    /**
     * Extract consonants from a string
     */
    private static function extractConsonants($text)
    {
        $consonants = preg_replace('/[AEIOU]/', '', $text);
        return substr($consonants, 0, 6);
    }
    
    /**
     * Generate a random referral code
     */
    private static function generateRandomCode($type = 'professor', $userId = null)
    {
        $prefix = $type === 'director' ? 'DIR' : 'PROF';
        $userIdStr = $userId ? str_pad($userId, 2, '0', STR_PAD_LEFT) : '01';
        $randomNumber = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        return $prefix . $userIdStr . $randomNumber;
    }
    
    /**
     * Check if a referral code already exists
     */
    private static function codeExists($code)
    {
        $existsInDirectors = Director::where('referral_code', $code)->exists();
        $existsInProfessors = Professor::where('referral_code', $code)->exists();
        
        return $existsInDirectors || $existsInProfessors;
    }
    
    /**
     * Get referral information by code
     */
    public static function getReferralInfo($code)
    {
        $director = Director::where('referral_code', $code)->where('directors_archived', 0)->first();
        if ($director) {
            return [
                'type' => 'director',
                'id' => $director->directors_id,
                'name' => $director->directors_name,
                'email' => $director->directors_email
            ];
        }
        
        $professor = Professor::where('referral_code', $code)->where('professor_archived', 0)->first();
        if ($professor) {
            return [
                'type' => 'professor',
                'id' => $professor->professor_id,
                'name' => $professor->professor_name,
                'email' => $professor->professor_email
            ];
        }
        
        return null;
    }
    
    /**
     * Validate if referral code can be used by student
     */
    public static function validateReferralCode($code, $studentId)
    {
        // Check if code exists and is valid
        $referralInfo = self::getReferralInfo($code);
        if (!$referralInfo) {
            return ['valid' => false, 'message' => 'Invalid referral code'];
        }
        
        // Check if student has already used a referral code
        $existingReferral = DB::table('referrals')->where('student_id', $studentId)->first();
        if ($existingReferral) {
            return ['valid' => false, 'message' => 'You have already used a referral code'];
        }
        
        return ['valid' => true, 'referral_info' => $referralInfo, 'message' => 'Valid referral code'];
    }
    
    /**
     * Record referral usage - only when enrollment is approved and paid
     */
    public static function recordReferralUsage($code, $studentId, $registrationId, $enrollmentId = null)
    {
        $referralInfo = self::getReferralInfo($code);
        if (!$referralInfo) {
            return false;
        }
        
        // Check if enrollment is approved and paid before recording referral
        if ($enrollmentId) {
            $enrollment = DB::table('enrollments')->where('enrollment_id', $enrollmentId)->first();
            if (!$enrollment || $enrollment->enrollment_status !== 'approved' || $enrollment->payment_status !== 'paid') {
                Log::info("Referral not recorded yet - enrollment not approved/paid", [
                    'enrollment_id' => $enrollmentId,
                    'enrollment_status' => $enrollment->enrollment_status ?? 'not_found',
                    'payment_status' => $enrollment->payment_status ?? 'not_found'
                ]);
                return false;
            }
        }
        
        // Check if referral already exists to avoid duplicates
        $existingReferral = DB::table('referrals')
            ->where('student_id', $studentId)
            ->where('referral_code', $code)
            ->first();
            
        if ($existingReferral) {
            Log::info("Referral already recorded for student", [
                'student_id' => $studentId,
                'referral_code' => $code
            ]);
            return true; // Already recorded, return success
        }
        
        try {
            DB::table('referrals')->insert([
                'referral_code' => $code,
                'referrer_type' => $referralInfo['type'],
                'referrer_id' => $referralInfo['id'],
                'student_id' => $studentId,
                'registration_id' => $registrationId,
                'used_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info("Referral recorded successfully", [
                'referral_code' => $code,
                'student_id' => $studentId,
                'referrer_type' => $referralInfo['type'],
                'referrer_id' => $referralInfo['id']
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to record referral usage: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process pending referrals when enrollment status changes
     */
    public static function processPendingReferral($enrollmentId)
    {
        try {
            // Get enrollment details
            $enrollment = DB::table('enrollments')->where('enrollment_id', $enrollmentId)->first();
            if (!$enrollment) {
                return false;
            }
            
            // Check if enrollment is now approved and paid
            if ($enrollment->enrollment_status !== 'approved' || $enrollment->payment_status !== 'paid') {
                return false;
            }
            
            // Get registration details to find referral code
            $registration = DB::table('registrations')->where('registration_id', $enrollment->registration_id)->first();
            if (!$registration || empty($registration->referral_code)) {
                return false;
            }
            
            // Record the referral usage
            return self::recordReferralUsage(
                $registration->referral_code,
                $enrollment->student_id,
                $enrollment->registration_id,
                $enrollmentId
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to process pending referral: ' . $e->getMessage());
            return false;
        }
    }
}
