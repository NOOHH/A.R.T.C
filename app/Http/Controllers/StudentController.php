<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use App\Models\Student; // Uncomment if you have a Student model

class StudentController extends Controller
{
    public function store(Request $request)
    {
        // Validate form fields and files
        $validated = $request->validate([
            'first_name'         => 'required|string|max:255',
            'middle_name'        => 'nullable|string|max:255',
            'last_name'          => 'required|string|max:255',
            'school'             => 'required|string|max:255',
            'street_address'     => 'required|string|max:255',
            'state'              => 'required|string|max:255',
            'city'               => 'required|string|max:255',
            'zip'                => 'required|string|max:10',
            'email'              => 'required|email|max:255',
            'contact_number'     => 'required|string|max:20',
            'emergency_contact'  => 'required|string|max:20',
            'education'          => 'required|string',
            'course_1'           => 'required|string',
            'course_2'           => 'nullable|string',
            'start_date'         => 'nullable|date',
            'terms'              => 'accepted',

            // Optional: File validation rules
            'good_moral'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'birth_cert'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'course_cert'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tor'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'grad_cert'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo'              => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ]);

        // Handle file uploads
        $uploadedFiles = [];
        foreach (['good_moral', 'birth_cert', 'course_cert', 'tor', 'grad_cert', 'photo'] as $field) {
            if ($request->hasFile($field)) {
                $uploadedFiles[$field] = $request->file($field)->store("documents/{$field}", 'public');
            }
        }

        // Example: Save to database (uncomment after creating a Student model and migration)
        /*
        Student::create([
            ...$validated,
            ...$uploadedFiles,
        ]);
        */

        return back()->with('success', 'Registration submitted successfully!');
    }
}
