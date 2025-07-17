<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Program;
use App\Http\Controllers\UnifiedLoginController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDirectorController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (!session('user_type') || session('user_type') !== 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Access denied. Director management is only available for admins.');
        }

        $directors = Director::with(['programs', 'assignedPrograms', 'admin'])
            ->where('directors_archived', false)
            ->orderBy('directors_last_name')
            ->get();

        $programs = Program::where('is_archived', false)->orderBy('program_name')->get();

        return view('admin.directors.director', compact('directors', 'programs'));
    }

    public function create()
    {
        return view('admin.directors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'directors_first_name' => 'required|string|max:100',
            'directors_last_name' => 'required|string|max:100',
            'directors_email' => 'required|email',
            'directors_password' => 'required|string|min:6',
            'referral_code' => 'nullable|string|max:20|unique:directors,referral_code|unique:professors,referral_code',
            'program_access' => 'required',
        ]);

        // Check email uniqueness across all user tables
        if (!UnifiedLoginController::isEmailUnique($validated['directors_email'])) {
            return back()->withErrors(['directors_email' => 'This email address is already registered in the system.'])->withInput();
        }

        // Store plain text password - will be hashed on first login
        $plainPassword = $validated['directors_password'];
        $validated['directors_password'] = $plainPassword; // Store as plain text initially
        
        // Get admin_id from session or set a default value for testing
        $admin_id = session('user_id') ?? session('admin_id') ?? auth()->id() ?? 1;
        $validated['admin_id'] = $admin_id;
        
        $validated['directors_name'] = $validated['directors_first_name'] . ' ' . $validated['directors_last_name'];
        
        // Set program access flag
        $programAccess = $request->program_access;
        $validated['has_all_program_access'] = is_array($programAccess) ? in_array('all', $programAccess) : $programAccess === 'all';

        $director = Director::create($validated);

        // Generate and save referral code after director is created (to get the ID)
        if ($request->referral_code) {
            $director->referral_code = strtoupper($request->referral_code);
        } else {
            $director->referral_code = \App\Helpers\ReferralCodeGenerator::generateCode(
                $validated['directors_first_name'], 
                $validated['directors_last_name'], 
                'director', 
                $director->directors_id
            );
        }
        $director->save();

        // Sync to users table for email uniqueness tracking
        UnifiedLoginController::syncToUsersTable(
            $validated['directors_email'], 
            $validated['directors_name'], 
            'director',
            $plainPassword,
            $director->directors_id
        );

        // Handle program assignment
        if (!$validated['has_all_program_access']) {
            // Handle multiple program assignments using pivot table
            $programIds = is_array($programAccess) ? $programAccess : [$programAccess];
            
            $validProgramIds = [];
            foreach ($programIds as $programId) {
                if ($programId !== 'all' && is_numeric($programId)) {
                    $validProgramIds[] = $programId;
                }
            }
            
            if (!empty($validProgramIds)) {
                $director->assignedPrograms()->attach($validProgramIds);
            }
        }
        // If 'all' is selected, no specific program assignment is needed
        // The director will have access to all programs

        // Check if request is from modal (AJAX/modal submission)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Director added successfully! They can now log in using the main login page.']);
        }

        return redirect()->route('admin.directors.index')
            ->with('success', 'Director added successfully! They can now log in using the main login page.');
    }

    public function show(Director $director)
    {
        $director->load(['programs', 'admin']);
        return view('admin.directors.show', compact('director'));
    }

    public function edit(Director $director)
    {
        $director->load(['programs', 'assignedPrograms']);
        $programs = Program::where('is_archived', false)->orderBy('program_name')->get();
        return view('admin.directors.edit', compact('director', 'programs'));
    }

    public function update(Request $request, Director $director)
    {
        $validated = $request->validate([
            'directors_first_name' => 'required|string|max:100',
            'directors_last_name' => 'required|string|max:100',
            'directors_email' => ['required', 'email', Rule::unique('directors', 'directors_email')->ignore($director->directors_id, 'directors_id')],
            'directors_password' => 'nullable|string|min:6',
            'referral_code' => 'nullable|string|max:20|unique:directors,referral_code,' . $director->directors_id . ',directors_id|unique:professors,referral_code',
            'program_access' => 'sometimes|required',
        ]);

        if (!empty($validated['directors_password'])) {
            $validated['directors_password'] = bcrypt($validated['directors_password']);
        } else {
            unset($validated['directors_password']);
        }

        $validated['directors_name'] = $validated['directors_first_name'] . ' ' . $validated['directors_last_name'];
        
        // Handle referral code update
        if ($request->referral_code) {
            $validated['referral_code'] = strtoupper($request->referral_code);
        } elseif (!$director->referral_code) {
            // Generate if not exists
            $validated['referral_code'] = \App\Helpers\ReferralCodeGenerator::generateCode(
                $validated['directors_first_name'], 
                $validated['directors_last_name'], 
                'director', 
                $director->directors_id
            );
        }
        
        // Handle program access if provided
        if ($request->has('program_access')) {
            $programAccess = $request->program_access;
            $validated['has_all_program_access'] = is_array($programAccess) ? in_array('all', $programAccess) : $programAccess === 'all';
            
            // Clear existing program assignments
            $director->assignedPrograms()->detach();
            
            // Handle program assignment
            if (!$validated['has_all_program_access']) {
                $programIds = is_array($programAccess) ? $programAccess : [$programAccess];
                
                $validProgramIds = [];
                foreach ($programIds as $programId) {
                    if ($programId !== 'all' && is_numeric($programId)) {
                        $validProgramIds[] = $programId;
                    }
                }
                
                if (!empty($validProgramIds)) {
                    $director->assignedPrograms()->attach($validProgramIds);
                }
            }
        }

        $director->update($validated);

        return redirect()->route('admin.directors.index')
            ->with('success', 'Director updated successfully!');
    }

    public function archive(Director $director)
    {
        $director->update(['directors_archived' => true]);

        return redirect()->route('admin.directors.index')
            ->with('success', 'Director archived successfully!');
    }

    public function archived()
    {
        $directors = Director::with(['programs', 'assignedPrograms', 'admin'])
            ->where('directors_archived', true)
            ->orderBy('directors_last_name')
            ->get();

        return view('admin.directors.archived', compact('directors'));
    }

    public function restore(Director $director)
    {
        $director->update(['directors_archived' => false]);

        return redirect()->route('admin.directors.archived')
            ->with('success', 'Director restored successfully!');
    }

    public function destroy(Director $director)
    {
        if ($director->programs()->count() > 0) {
            return redirect()->route('admin.directors.archived')
                ->with('error', 'Cannot delete director with assigned programs. Please reassign programs first.');
        }

        $director->delete();

        return redirect()->route('admin.directors.archived')
            ->with('success', 'Director deleted permanently!');
    }

    public function assignProgram(Request $request, Director $director)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $program->update(['director_id' => $director->directors_id]);

        return redirect()->route('admin.directors.show', $director)
            ->with('success', 'Program assigned successfully!');
    }

    public function unassignProgram(Request $request, Director $director)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $program->update(['director_id' => null]);

        return redirect()->route('admin.directors.show', $director)
            ->with('success', 'Program unassigned successfully!');
    }
}
