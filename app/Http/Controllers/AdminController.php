<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            return response()->json($registration);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration not found or database error.'], 404);
        }
    }

    public function approve($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            // Create user (customize as needed)
            $user = User::create([
                'name' => $registration->lastname . ', ' . $registration->firstname . ' ' . $registration->middlename,
                'email' => $registration->email ?? '',
                'password' => Hash::make('defaultpassword'), // Set a default or generate
            ]);
            $registration->delete();
            return redirect()->back()->with('success', 'Student approved and added to students.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            $registration->delete();
            return redirect()->back()->with('success', 'Registration rejected and removed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        }
    }
}
