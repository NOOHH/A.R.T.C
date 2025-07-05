<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Program;
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
            'email' => 'required|email|unique:professors,professor_email',
            'password' => 'required|string|min:8',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,program_id'
        ]);

        $professor = new Professor();
        $professor->first_name = $request->first_name;
        $professor->last_name = $request->last_name;
        $professor->email = $request->email;
        $professor->password = Hash::make($request->password);
        $professor->admin_id = session('admin_id') ?? session('admin.admin_id') ?? 1; // Get admin ID from session

        $professor->save();

        // Assign programs
        if ($request->programs) {
            $professor->programs()->attach($request->programs);
        }

        return redirect()->back()->with('success', 'Professor added successfully!');
    }

    public function edit($id)
    {
        $professor = Professor::with('programs')->findOrFail($id);
        $programs = Program::all();
        
        return view('admin.professors.edit', compact('professor', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $professor = Professor::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('professors', 'professor_email')->ignore($professor->professor_id, 'professor_id')],
            'password' => 'nullable|string|min:8',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,program_id'
        ]);

        $professor->first_name = $request->first_name;
        $professor->last_name = $request->last_name;
        $professor->email = $request->email;
        
        if ($request->password) {
            $professor->password = Hash::make($request->password);
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

    public function archive($id)
    {
        $professor = Professor::findOrFail($id);
        $professor->archive();

        return redirect()->back()->with('success', 'Professor archived successfully!');
    }

    public function restore($id)
    {
        $professor = Professor::findOrFail($id);
        $professor->restore();

        return redirect()->back()->with('success', 'Professor restored successfully!');
    }

    public function destroy($id)
    {
        $professor = Professor::findOrFail($id);
        
        // Detach programs
        $professor->programs()->detach();
        
        $professor->delete();

        return redirect()->back()->with('success', 'Professor deleted successfully!');
    }

    public function updateVideoLink(Request $request, $professorId, $programId)
    {
        $request->validate([
            'video_link' => 'required|url',
            'video_description' => 'nullable|string|max:500'
        ]);

        $professor = Professor::findOrFail($professorId);
        
        // Update the pivot table
        $professor->programs()->updateExistingPivot($programId, [
            'video_link' => $request->video_link,
            'video_description' => $request->video_description
        ]);

        return response()->json(['success' => true, 'message' => 'Video link updated successfully!']);
    }
}
