<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SignupController extends Controller
{
    public function showSignupForm()
    {
        return view('Login.signup');
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email')
            ]);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in session with expiration
        Session::put('signup_otp', $otp);
        Session::put('signup_email', $request->email);
        Session::put('otp_expires_at', now()->addMinutes(10));

        try {
            // Send OTP email
            Mail::raw("Your OTP verification code is: {$otp}\n\nThis code will expire in 10 minutes.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('A.R.T.C - Email Verification Code');
            });

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email address.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ]);
        }
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid 6-digit OTP.'
            ]);
        }

        $sessionOTP = Session::get('signup_otp');
        $otpExpiresAt = Session::get('otp_expires_at');
        $sessionEmail = Session::get('signup_email');

        if (!$sessionOTP || !$otpExpiresAt || !$sessionEmail) {
            return response()->json([
                'success' => false,
                'message' => 'OTP session expired. Please request a new OTP.'
            ]);
        }

        if (now()->greaterThan($otpExpiresAt)) {
            Session::forget(['signup_otp', 'signup_email', 'otp_expires_at']);
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.'
            ]);
        }

        if ($request->otp !== $sessionOTP) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please check and try again.'
            ]);
        }

        // OTP verified successfully
        Session::put('email_verified', true);
        
        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully! You can now complete your registration.'
        ]);
    }

    public function signup(Request $request)
    {
        // Check if email was verified via OTP
        if (!Session::get('email_verified')) {
            return redirect()->back()
                           ->withErrors(['email' => 'Please verify your email address first.'])
                           ->withInput();
        }

        $sessionEmail = Session::get('signup_email');
        if ($sessionEmail !== $request->email) {
            return redirect()->back()
                           ->withErrors(['email' => 'Email address does not match the verified email.'])
                           ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $user = User::create([
            'user_firstname' => $request->first_name,
            'user_lastname' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // Set as student by default
        ]);

        // Clear OTP session data
        Session::forget(['signup_otp', 'signup_email', 'otp_expires_at', 'email_verified']);

        return redirect()->route('login')->with('success', 'Account created successfully! Please log in to continue.');
    }
    
    public function checkEmailAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'available' => false,
                'message' => 'Invalid email format.'
            ]);
        }

        // Check if email exists in database
        $emailExists = User::where('email', $request->email)->exists();

        return response()->json([
            'success' => true,
            'available' => !$emailExists,
            'message' => $emailExists ? 'Email already exists' : 'Email is available'
        ]);
    }
}
