<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentLoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email'
            // password not required
        ]);

        $student = Student::where('email', $credentials['email'])->first();

        if ($student) {
            // Store student ID in session (optional)
            session(['student_id' => $student->id]);
            // Redirect to admin dashboard
            return redirect()->route('admin.dashboard')->with('success', 'Logged in!');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }
}