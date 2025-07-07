<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Program;
use Illuminate\Http\Request;

class ProfessorGradingController extends Controller
{
    public function __construct()
    {
        $this->middleware('professor.auth');
    }

    public function index(Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        $selectedProgramId = $request->get('program_id');
        
        $students = collect();
        $grades = collect();
        
        if ($selectedProgramId) {
            $students = Student::whereHas('enrollments', function ($query) use ($selectedProgramId) {
                $query->where('program_id', $selectedProgramId);
            })->get();
            
            $grades = StudentGrade::where('program_id', $selectedProgramId)
                ->with('student')
                ->get()
                ->groupBy('student_id');
        }
        
        return view('professor.grading.index', compact(
            'assignedPrograms',
            'students',
            'grades',
            'selectedProgramId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'program_id' => 'required|exists:programs,program_id',
            'assignment_name' => 'required|string|max:255',
            'grade' => 'required|numeric|min:0|max:100',
            'max_points' => 'required|numeric|min:1',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Verify professor has access to this program
        if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
            return back()->withErrors(['error' => 'You do not have access to this program.']);
        }

        StudentGrade::create([
            'student_id' => $request->student_id,
            'program_id' => $request->program_id,
            'professor_id' => $professor->professor_id,
            'assignment_name' => $request->assignment_name,
            'grade' => $request->grade,
            'max_points' => $request->max_points,
            'feedback' => $request->feedback,
            'graded_at' => now()
        ]);

        return back()->with('success', 'Grade assigned successfully!');
    }

    public function update(Request $request, $gradeId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'max_points' => 'required|numeric|min:1',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $professor = Professor::find(session('professor_id'));
        $studentGrade = StudentGrade::where('grade_id', $gradeId)
            ->where('professor_id', $professor->professor_id)
            ->firstOrFail();

        $studentGrade->update([
            'grade' => $request->grade,
            'max_points' => $request->max_points,
            'feedback' => $request->feedback,
            'graded_at' => now()
        ]);

        return back()->with('success', 'Grade updated successfully!');
    }

    public function destroy($gradeId)
    {
        $professor = Professor::find(session('professor_id'));
        $studentGrade = StudentGrade::where('grade_id', $gradeId)
            ->where('professor_id', $professor->professor_id)
            ->firstOrFail();

        $studentGrade->delete();

        return back()->with('success', 'Grade deleted successfully!');
    }

    public function studentDetails($studentId, Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        $student = Student::findOrFail($studentId);
        
        $programId = $request->get('program_id');
        
        // Verify professor has access to this program
        if ($programId && !$professor->programs()->where('program_id', $programId)->exists()) {
            return back()->withErrors(['error' => 'You do not have access to this program.']);
        }
        
        $grades = StudentGrade::where('student_id', $studentId)
            ->when($programId, function ($query) use ($programId) {
                return $query->where('program_id', $programId);
            })
            ->whereIn('program_id', $professor->programs()->pluck('program_id'))
            ->with('program')
            ->orderBy('graded_at', 'desc')
            ->get();
        
        $averageGrade = $grades->count() > 0 ? $grades->avg('grade') : 0;
        
        return view('professor.grading.student-details', compact(
            'student',
            'grades',
            'averageGrade',
            'programId'
        ));
    }
}
