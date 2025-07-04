<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Registration;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Module;
use App\Models\Enrollment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            // Get pending registrations
            $registrations = Registration::where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            // Calculate analytics data
            $analytics = [
                'total_students' => Student::count(),
                'total_programs' => Program::where('is_archived', false)->count(),
                'total_modules' => Module::where('is_archived', false)->count(),
                'total_enrollments' => Enrollment::count(),
                'pending_registrations' => Registration::where('status', 'pending')->count(),
                'new_students_this_month' => Student::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
                'modules_this_week' => Module::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
                'archived_programs' => Program::where('is_archived', true)->count(),
            ];

            $dbError = null;
        } catch (\Exception $e) {
            $registrations = collect();
            $analytics = [
                'total_students' => 0,
                'total_programs' => 0,
                'total_modules' => 0,
                'total_enrollments' => 0,
                'pending_registrations' => 0,
                'new_students_this_month' => 0,
                'modules_this_week' => 0,
                'archived_programs' => 0,
            ];
            $dbError = 'Database connection failed: ' . $e->getMessage();
        }

        return view('admin.admin-dashboard', compact('registrations', 'analytics', 'dbError'));
    }

    public function showRegistration($id)
    {
        try {
            $registration = Registration::where('registration_id', $id)->firstOrFail();
            $user = User::find($registration->user_id);

            $data = $registration->toArray();
            $data['email'] = $user ? $user->email : 'N/A';

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration not found or database error.'], 404);
        }
    }

    public function showRegistrationDetails($id)
    {
        $registration = Registration::findOrFail($id);
        return view('admin.admin-student-registration-view', compact('registration'));
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $registration = Registration::findOrFail($id);

            // Upgrade user role
            $user = User::find($registration->user_id);
            if ($user) {
                $user->role = 'student';
                $user->save();
            }

            // Generate unique, non-duplicating student_id
            $now       = now();
            $yearMonth = $now->format('Y-m');

            $lastStudent = Student::where('student_id', 'like', "{$yearMonth}-%")
                                  ->orderBy('student_id', 'desc')
                                  ->first();

            $nextSeq = $lastStudent
                ? ((int) substr($lastStudent->student_id, strlen($yearMonth) + 1)) + 1
                : 1;

            $studentId = $yearMonth . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

            // Create the Student record
            Student::create([
                'student_id'               => $studentId,
                'user_id'                  => $user?->user_id,
                'firstname'                => $registration->firstname,
                'middlename'               => $registration->middlename,
                'lastname'                 => $registration->lastname,
                'student_school'           => $registration->student_school,
                'street_address'           => $registration->street_address,
                'state_province'           => $registration->state_province,
                'city'                     => $registration->city,
                'zipcode'                  => $registration->zipcode,
                'contact_number'           => $registration->contact_number,
                'emergency_contact_number' => $registration->emergency_contact_number,
                'good_moral'               => $registration->good_moral,
                'PSA'                      => $registration->PSA,
                'Course_Cert'              => $registration->Course_Cert,
                'TOR'                      => $registration->TOR,
                'Cert_of_Grad'             => $registration->Cert_of_Grad,
                'Undergraduate'            => $registration->Undergraduate,
                'Graduate'                 => $registration->Graduate,
                'photo_2x2'                => $registration->photo_2x2,
                'Start_Date'               => $registration->Start_Date,
                'date_approved'            => $now,
                'program_id'               => $registration->program_id,
                'package_id'               => $registration->package_id,
                'plan_id'                  => $registration->plan_id,
                'package_name'             => $registration->package_name,
                'plan_name'                => $registration->plan_name,
                'program_name'             => $registration->program_name,
                'email'                    => $user?->email,
            ]);

            // Remove from pending
            $registration->delete();

            DB::commit();

            return redirect()
                ->route('admin.student.registration.history')
                ->with('success', "Student “{$studentId}” approved and moved to history.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            $registration->delete();

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected and removed.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function studentRegistration()
    {
        $registrations = Registration::with('user')->get();
        return view('admin.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => false,
        ]);
    }

    public function studentRegistrationHistory()
    {
        $registrations = Student::with(['user', 'program', 'package'])->get();
        return view('admin.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => true,
        ]);
    }
}
