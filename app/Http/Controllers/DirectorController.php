<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Program;

class DirectorController extends Controller
{
    /**
     * Show the director dashboard.
     */
    public function dashboard()
    {
        // Get director data from session
        $directorId = session('directors_id');
        $directorName = session('user_name');
        
        if (!$directorId) {
            return redirect('/login')->with('error', 'Please log in to access the director dashboard.');
        }
        
        // Get statistics
        $analytics = [
            'total_students' => DB::table('students')->count(),
            'total_professors' => DB::table('professors')->count(),
            'total_programs' => DB::table('programs')->count(),
            'total_batches' => DB::table('student_batches')->count(),
            'pending_registrations' => DB::table('users')->where('role', 'pending')->count(),
            'active_programs' => DB::table('programs')->where('is_archived', false)->count(),
            'accessible_programs' => DB::table('programs')->where('is_archived', false)->count(),
            'total_modules' => 0, // Set to 0 if modules table doesn't exist
        ];
        
        // Try to get modules count if table exists
        try {
            $analytics['total_modules'] = DB::table('modules')->count();
        } catch (\Exception $e) {
            // If modules table doesn't exist, keep it as 0
            $analytics['total_modules'] = 0;
        }
        
        // Get recent registrations (simplified without complex joins that don't match database structure)
        $recentRegistrations = DB::table('students')
            ->select('students.*')
            ->orderBy('students.created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get accessible programs with counts
        $programs = Program::withCount(['modules', 'students'])
            ->where('is_archived', false)
            ->orderBy('program_name')
            ->get();
        
        // Get director information
        $director = DB::table('directors')
            ->where('directors_id', $directorId)
            ->first();
        
        return view('director.dashboard', compact('analytics', 'recentRegistrations', 'programs', 'director'));
    }
    
    /**
     * Show director profile
     */
    public function profile()
    {
        $directorId = session('directors_id');
        
        if (!$directorId) {
            return redirect('/login')->with('error', 'Please log in to access your profile.');
        }
        
        $director = DB::table('directors')
            ->where('directors_id', $directorId)
            ->first();
        
        return view('director.profile', compact('director'));
    }
    
    /**
     * Update director profile
     */
    public function updateProfile(Request $request)
    {
        $directorId = session('directors_id');
        
        if (!$directorId) {
            return redirect('/login')->with('error', 'Please log in to update your profile.');
        }
        
        $request->validate([
            'directors_name' => 'required|string|max:255',
            'directors_email' => 'required|email|max:255|unique:directors,directors_email,' . $directorId . ',directors_id',
            'directors_phone' => 'nullable|string|max:20',
        ]);
        
        DB::table('directors')
            ->where('directors_id', $directorId)
            ->update([
                'directors_name' => $request->directors_name,
                'directors_email' => $request->directors_email,
                'directors_phone' => $request->directors_phone,
                'updated_at' => now(),
            ]);
        
        // Update session data
        session(['user_name' => $request->directors_name]);
        session(['user_email' => $request->directors_email]);
        
        return back()->with('success', 'Profile updated successfully.');
    }
}
