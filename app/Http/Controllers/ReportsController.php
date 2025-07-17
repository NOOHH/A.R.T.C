<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\StudentBatch;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function attendance(Request $request)
    {
        $professor = Professor::find(session('professor_id'));

        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }

        // Get professor's assigned batches and their students
        $batches = $professor->batches()->with(['students', 'program'])->get();
        
        return view('professor.reports.attendance', compact('professor', 'batches'));
    }

    public function grades(Request $request)
    {
        $professor = Professor::find(session('professor_id'));

        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }

        // Get professor's assigned programs and their students
        $programs = $professor->programs()->with('enrollments.student')->get();
        
        return view('professor.reports.grades', compact('professor', 'programs'));
    }
}
