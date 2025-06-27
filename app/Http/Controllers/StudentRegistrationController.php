<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Registration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // Validate step 1 + step 2
        $validated = $request->validate([
            // Step 1 (Account)
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',

            // Step 2 (Details)
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
        ]);

        // Step 1: Create user account
        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_firstname' => $validated['user_firstname'],
            'user_lastname' => $validated['user_lastname'],
            'role' => 'unverified',
        ]);

        // Step 2: Create registration record
        $registration = new Registration();
        $registration->user_id = $user->id;
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

        // Handle file uploads
        foreach (['good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 'photo_2x2'] as $field) {
            if ($request->hasFile($field)) {
                $registration->$field = $request->file($field)->store('documents', 'public');
            }
        }

        // Handle education radio
        $education = $request->input('education');
        if ($education === 'Undergraduate') {
            $registration->Undergraduate = 'yes';
            $registration->Graduate = 'no';
        } else {
            $registration->Undergraduate = 'no';
            $registration->Graduate = 'yes';
        }

        $registration->save();

        return redirect()->back()->with('success', 'Registration successful!');
    }
}
