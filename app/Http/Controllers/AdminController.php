<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            $registrations = Registration::all();
            $dbError = null;
        } catch (\Exception $e) {
            $registrations = [];
            $dbError = 'Database connection failed: ' . $e->getMessage();
        }
        return view('admin.admin-dashboard', compact('registrations', 'dbError'));
    }

    public function showRegistration($id)
    {
        try {
            $registration = Registration::where('registration_id', $id)->firstOrFail();
            $user = null;
            if ($registration->user_id) {
                $user = User::where('user_id', $registration->user_id)->first();
            }
            $data = $registration->toArray();
            $data['email'] = $user ? $user->email : $registration->email;
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration not found or database error.'], 404);
        }
    }

    public function showRegistrationDetails($id)
    {
        $registration = Registration::findOrFail($id);
        // If you want to fetch user email from users table, you can join or relate here if needed
        // For now, using registration email field
        return view('admin.admin-student-registration-view', compact('registration'));
    }

    public function approve($id)
    {
        Log::info('Approve called for registration_id: ' . $id);
        try {
            $registration = Registration::where('registration_id', $id)->first();
            if (!$registration) {
                Log::error('Registration not found for id: ' . $id);
                return redirect()->back()->with('error', 'Registration not found.');
            }
            Log::info('Registration found: ' . json_encode($registration->toArray()));
            $email = trim($registration->email);
            if (empty($email) && $registration->user_id) {
                $userRecord = User::where('user_id', $registration->user_id)->first();
                if ($userRecord) {
                    $email = $userRecord->email;
                }
            }
            if (empty($email)) {
                Log::error('Registration email is empty and could not be retrieved from users table for id: ' . $id);
                return redirect()->back()->with('error', 'Registration email is missing and could not be retrieved. Cannot approve.');
            }
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->role = 'student';
                $user->save();
            } else {
                $user = User::create([
                    'user_firstname' => $registration->firstname ?? '',
                    'user_lastname' => $registration->lastname ?? '',
                    'email' => $email,
                    'password' => Hash::make('defaultpassword'),
                    'role' => 'student',
                ]);
                Log::info('User created: ' . $user->user_id);
            }
            $now = now();
            $yearMonth = $now->format('Y-m');
            $count = Student::whereRaw("DATE_FORMAT(date_approved, '%Y-%m') = ?", [$yearMonth])->count();
            $studentId = $yearMonth . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
            $student = Student::create([
                'student_id' => $studentId,
                'user_id' => $user->user_id,
                'firstname' => $registration->firstname,
                'middlename' => $registration->middlename,
                'lastname' => $registration->lastname,
                'email' => $email,
                'student_school' => $registration->student_school ?? '',
                'street_address' => $registration->street_address ?? '',
                'state_province' => $registration->state_province ?? '',
                'city' => $registration->city ?? '',
                'zipcode' => $registration->zipcode ?? '',
                'contact_number' => $registration->contact_number ?? '',
                'emergency_contact_number' => $registration->emergency_contact_number ?? '',
                'good_moral' => $registration->good_moral ?? '',
                'PSA' => $registration->PSA ?? '',
                'Course_Cert' => $registration->Course_Cert ?? '',
                'TOR' => $registration->TOR ?? '',
                'Cert_of_Grad' => $registration->Cert_of_Grad ?? '',
                'Undergraduate' => $registration->Undergraduate ?? '',
                'Graduate' => $registration->Graduate ?? '',
                'photo_2x2' => $registration->photo_2x2 ?? '',
                'Start_Date' => $registration->Start_Date ?? $registration->birthdate ?? null,
                'date_approved' => $now,
            ]);
            Log::info('Student created: ' . $student->student_id);
            $registration->delete();
            Log::info('Registration deleted: ' . $id);
            return redirect()->back()->with('success', 'Student approved and added to students.');
        } catch (\Exception $e) {
            Log::error('Approve error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            // Do not change user role, just delete registration
            $registration->delete();
            return redirect()->back()->with('success', 'Registration rejected and removed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    public function studentRegistration()
    {
        $registrations = \App\Models\Registration::all()->filter(function($reg) {
            $email = $reg->email;
            $user = \App\Models\User::where('email', $email)->first();
            return !$user || $user->role === 'unverified';
        });
        return view('admin.admin-student-registration', compact('registrations'));
    }

    public function studentRegistrationHistory()
    {
        // Show all users who have been verified or rejected
        $students = \App\Models\Student::all();
        $verifiedUsers = \App\Models\User::whereIn('role', ['verified', 'rejected'])->get()->keyBy('email');
        $registrations = $students->filter(function($student) use ($verifiedUsers) {
            $user = $verifiedUsers->get($student->email);
            return $user && in_array($user->role, ['verified', 'rejected']);
        });
        return view('admin.admin-student-registration', [
            'registrations' => $registrations,
            'history' => true,
            'verifiedUsers' => $verifiedUsers
        ]);
    }
}
