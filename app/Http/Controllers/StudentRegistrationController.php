<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Package;

class StudentRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'firstname' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'lastname' => 'required|string|max:50',
            'student_school' => 'required|string|max:50',
            'street_address' => 'required|string|max:50',
            'state_province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'zipcode' => 'required|string|max:20',
            'contact_number' => 'required|string|max:15',
            'emergency_contact_number' => 'required|string|max:15',
            'Start_Date' => 'required|date',
            'program_id' => 'required|integer|exists:programs,program_id',
            'package_id' => 'required|integer|exists:packages,package_id',
            'enrollment_type' => 'required|in:modular,full',
            'plan_id' => 'nullable|integer',
        ]);

        $enrollmentType = $validated['enrollment_type'] === 'full' ? 'Complete' : 'Modular';

        $enrollment = Enrollment::create([
            'program_id' => $validated['program_id'],
            'package_id' => $validated['package_id'],
            'enrollment_type' => $enrollmentType,
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_firstname' => $validated['user_firstname'],
            'user_lastname' => $validated['user_lastname'],
            'role' => 'unverified',
            'enrollment_id' => $enrollment->enrollment_id,
        ]);

        $package = Package::find($validated['package_id']);
        $program = Program::find($validated['program_id']);
        $planName = $enrollmentType;

        $registration = new Registration();
        $registration->user_id = $user->user_id;
        $registration->firstname = $validated['firstname'];
        $registration->middlename = $validated['middle_name'] ?? null;
        $registration->lastname = $validated['lastname'];
        $registration->student_school = $validated['student_school'];
        $registration->street_address = $validated['street_address'];
        $registration->state_province = $validated['state_province'];
        $registration->city = $validated['city'];
        $registration->zipcode = $validated['zipcode'];
        $registration->contact_number = $validated['contact_number'];
        $registration->emergency_contact_number = $validated['emergency_contact_number'];
        $registration->Start_Date = $validated['Start_Date'];
        $registration->status = 'pending';
        $registration->package_id = $validated['package_id'];
        $registration->program_id = $validated['program_id'];
        $registration->plan_id = $request->input('plan_id');
        $registration->package_name = $package ? $package->package_name : null;
        $registration->program_name = $program ? $program->program_name : null;
        $registration->plan_name = $planName;

        $fileFields = [
            'good_moral' => 'good_moral',
            'birth_cert' => 'PSA',
            'course_cert' => 'Course_Cert',
            'tor' => 'TOR',
            'grad_cert' => 'Cert_of_Grad',
            'photo' => 'photo_2x2',
        ];

        foreach ($fileFields as $inputName => $columnName) {
            if ($request->hasFile($inputName)) {
                $registration->$columnName = $request->file($inputName)->store('documents', 'public');
            }
        }

        $education = $request->input('education');
        $registration->Undergraduate = $education === 'Undergraduate' ? 'yes' : 'no';
        $registration->Graduate = $education === 'Undergraduate' ? 'no' : 'yes';

        $registration->save();

        return redirect()->back()->with('success', 'Registration successful!');
    }

    public function showRegistrationForm(Request $request)
    {
        $enrollmentType = 'full'; // Set to full since this is the full enrollment route
        $programs = Program::all();
        $packages = Package::all();

        return view('registration.Full_enrollment', compact('enrollmentType', 'programs', 'packages'));
    }

    public function showEnrollmentSelection()
    {
        return view('enrollment');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        
        // Check if email exists in users table
        $exists = User::where('email', $email)->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email already exists' : 'Email is available'
        ]);
    }
}
