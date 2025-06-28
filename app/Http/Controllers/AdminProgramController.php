<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProgramController extends Controller
{
    public function index()
    {
        $programs = Program::withCount('enrollments')->get();
        return view('admin.admin-programs.admin-programs', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string|max:100',
        ]);
        Program::create([
            'program_name' => $request->program_name,
            'created_by_admin_id' => Auth::user()->admin_id ?? 1, // fallback for demo
        ]);
        return redirect()->route('admin.programs.index')->with('success', 'Program added successfully!');
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        try {
            $program->delete();
            return redirect()->route('admin.programs.index')->with('success', 'Program deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('admin.programs.index')->with('error', 'Cannot delete program: it is in use by one or more enrollments.');
            }
            throw $e;
        }
    }

    public function enrollments($id)
    {
        $program = Program::with(['enrollments' => function($q) {
            $q->with('student');
        }])->findOrFail($id);
        $enrollments = $program->enrollments->map(function($enrollment) {
            return [
                'student_name' => $enrollment->student ? ($enrollment->student->firstname . ' ' . $enrollment->student->lastname) : 'Unknown',
                'student_id' => $enrollment->student ? $enrollment->student->student_id : null,
            ];
        });
        return response()->json($enrollments);
    }
}