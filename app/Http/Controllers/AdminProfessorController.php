<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Program;
use App\Http\Controllers\UnifiedLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminProfessorController extends Controller
{
    public function index()
    {
        $professors = Professor::with('programs')->active()->paginate(10);
        $programs = Program::all();
        
        return view('admin.professors.index', compact('professors', 'programs'));
    }

    public function archived()
    {
        $professors = Professor::with('programs')->archived()->paginate(10);
        
        return view('admin.professors.archived', compact('professors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,program_id'
        ]);

        // Check email uniqueness across all user tables
        if (!UnifiedLoginController::isEmailUnique($request->email)) {
            return back()->withErrors([
                'email' => 'This email address is already registered in the system. Each user must have a unique email across all account types (Admin, Professor, Director, Student).'
            ])->withInput();
        }

        $professor = new Professor();
        $professor->professor_first_name = $request->first_name; // Use correct field name
        $professor->professor_last_name = $request->last_name;   // Use correct field name
        $professor->professor_email = $request->email;
        $professor->professor_password = $request->password; // Store plain text - will be hashed on first login
        $professor->professor_name = $request->first_name . ' ' . $request->last_name; // Set full name
        
        // Get valid admin ID - ensure it exists in admins table
        $adminId = session('user_id');
        if (!$adminId || !\App\Models\Admin::where('admin_id', $adminId)->exists()) {
            // Fallback to first available admin
            $firstAdmin = \App\Models\Admin::first();
            $adminId = $firstAdmin ? $firstAdmin->admin_id : 1;
        }
        $professor->admin_id = $adminId;

        $professor->save();

        // Sync to users table for email uniqueness tracking
        UnifiedLoginController::syncToUsersTable(
            $request->email, 
            $request->first_name . ' ' . $request->last_name, 
            'professor',
            $request->password
        );

        // Assign programs
        if ($request->programs) {
            $professor->programs()->attach($request->programs);
        }

        return redirect()->back()->with('success', 'Professor added successfully! They can now log in using the main login page.');
    }

    public function edit($professor_id)
    {
        $professor = Professor::with('programs')->findOrFail($professor_id);
        $programs = Program::all();
        
        return view('admin.professors.edit', compact('professor', 'programs'));
    }

    public function update(Request $request, $professor_id)
    {
        $professor = Professor::findOrFail($professor_id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('professors', 'professor_email')->ignore($professor->professor_id, 'professor_id')],
            'password' => 'nullable|string|min:8',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,program_id'
        ]);

        // Check email uniqueness across all user tables (exclude current professor)
        if ($request->email !== $professor->professor_email) {
            if (!UnifiedLoginController::isEmailUnique($request->email)) {
                return back()->withErrors([
                    'email' => 'This email address is already registered in the system. Each user must have a unique email across all account types (Admin, Professor, Director, Student).'
                ])->withInput();
            }
        }

        $professor->professor_first_name = $request->first_name;
        $professor->professor_last_name = $request->last_name;
        $professor->professor_email = $request->email;
        $professor->professor_name = $request->first_name . ' ' . $request->last_name; // Update full name
        
        if ($request->password) {
            $professor->professor_password = $request->password; // Store plain text - will be hashed on next login
        }

        $professor->save();

        // Update program assignments
        if ($request->programs) {
            $professor->programs()->sync($request->programs);
        } else {
            $professor->programs()->detach();
        }

        return redirect()->route('admin.professors.index')->with('success', 'Professor updated successfully!');
    }

    public function archive($professor_id)
    {
        $professor = Professor::findOrFail($professor_id);
        $professor->archive();

        return redirect()->back()->with('success', 'Professor archived successfully!');
    }

    public function restore($professor_id)
    {
        $professor = Professor::findOrFail($professor_id);
        $professor->restore();

        return redirect()->back()->with('success', 'Professor restored successfully!');
    }

    public function destroy($professor_id)
    {
        $professor = Professor::findOrFail($professor_id);
        
        // Detach programs
        $professor->programs()->detach();
        
        $professor->delete();

        return redirect()->back()->with('success', 'Professor deleted successfully!');
    }

    public function updateVideoLink(Request $request, $professor_id, $programId)
    {
        $request->validate([
            'video_link' => 'required|url',
            'video_description' => 'nullable|string|max:500'
        ]);

        $professor = Professor::findOrFail($professor_id);
        
        // Update the pivot table
        $professor->programs()->updateExistingPivot($programId, [
            'video_link' => $request->video_link,
            'video_description' => $request->video_description
        ]);

        return response()->json(['success' => true, 'message' => 'Video link updated successfully!']);
    }
}
