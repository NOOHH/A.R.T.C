<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function showForm(Request $request)
    {
        // Pass the enrollment data to the registration form view
        $data = $request->only(['course', 'package_id', 'enrollment_type']);
        return view('registration.form', $data);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'course' => 'required|string',
            'package_id' => 'required|exists:packages,package_id',
            'enrollment_type' => 'required|in:full,modular',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            // Add more validation as needed
        ]);

        // Save user (account creation)
        $user = new \App\Models\User();
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->user_firstname = $validated['firstname'];
        $user->user_lastname = $validated['lastname'];
        $user->role = 'student';
        $user->save();

        // Save enrollment
        $enrollment = new \App\Models\Enrollment();
        if ($validated['enrollment_type'] === 'modular') {
            $enrollment->Modular_enrollment = $validated['course'];
            $enrollment->Complete_Program = '';
        } else {
            $enrollment->Modular_enrollment = '';
            $enrollment->Complete_Program = $validated['course'];
        }
        $enrollment->package_id = $validated['package_id'];
        $enrollment->save();

        // Optionally link user and enrollment (if your schema supports it)
        $user->enrollment_id = $enrollment->enrollment_id;
        $user->save();

        // Redirect to a success page or dashboard
        return redirect()->route('home')->with('success', 'Registration and enrollment successful!');
    }

    public function showAccountForm(Request $request)
    {
        // Store enrollment selection in session
        session([
            'enrollment.course' => $request->course,
            'enrollment.package_id' => $request->package_id,
            'enrollment.enrollment_type' => $request->enrollment_type,
        ]);
        return view('registration.account');
    }

    public function showDetailsForm(Request $request)
    {
        // Save account info in session
        session([
            'enrollment.firstname' => $request->firstname,
            'enrollment.lastname' => $request->lastname,
            'enrollment.email' => $request->email,
            'enrollment.password' => bcrypt($request->password),
        ]);
        return view('registration.details');
    }

    public function submit(Request $request)
    {
        // Save all registration info in session
        session(['enrollment' => array_merge((array)session('enrollment', []), $request->all())]);
        $data = session('enrollment');

        // Save user
        $user = new \App\Models\User();
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->user_firstname = $data['firstname'];
        $user->user_lastname = $data['lastname'];
        $user->role = 'student';
        $user->save();

        // Save enrollment
        $enrollment = new \App\Models\Enrollment();
        if ($data['enrollment_type'] === 'modular') {
            $enrollment->Modular_enrollment = $data['course'];
            $enrollment->Complete_Program = '';
        } else {
            $enrollment->Modular_enrollment = '';
            $enrollment->Complete_Program = $data['course'];
        }
        $enrollment->package_id = $data['package_id'];
        $enrollment->save();

        // Link user and enrollment
        $user->enrollment_id = $enrollment->enrollment_id;
        $user->save();

        // Clear session
        session()->forget('enrollment');

        return redirect()->route('home')->with('success', 'Registration and enrollment successful!');
    }
}
