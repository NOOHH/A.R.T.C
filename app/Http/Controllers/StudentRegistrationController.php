<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:50',
            'middlename' => 'nullable|string|max:50',
            'lastname' => 'required|string|max:50',
            'student_school' => 'required|string|max:50',
            'street_address' => 'required|string|max:50',
            'state_province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'zipcode' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'contact_number' => 'required|string|max:15',
            'emergency_contact_number' => 'required|string|max:15',
            // Add other fields as needed
        ]);

        $student = new Student();
        $student->firstname = $validated['firstname'];
        $student->middlename = $validated['middlename'] ?? null;
        $student->lastname = $validated['lastname'];
        $student->student_school = $validated['student_school'];
        $student->street_address = $validated['street_address'];
        $student->state_province = $validated['state_province'];
        $student->city = $validated['city'];
        $student->zipcode = $validated['zipcode'];
        $student->email = $validated['email'];
        $student->contact_number = $validated['contact_number'];
        $student->emergency_contact_number = $validated['emergency_contact_number'];
        $student->Start_Date = $request->input('start_date');
        // Add other fields as needed
        $student->save();

        return redirect()->back()->with('success', 'Registration successful!');
    }
}
