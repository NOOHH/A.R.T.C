<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorDashboardController extends Controller
{
    public function index()
    {
        $professor = auth()->user(); // Assuming professor is logged in
        $assignedPrograms = $professor->programs()->with(['students', 'modules'])->get();
        
        // Get statistics
        $totalPrograms = $assignedPrograms->count();
        $totalStudents = $assignedPrograms->sum(function($program) {
            return $program->students->count();
        });
        $totalModules = $assignedPrograms->sum(function($program) {
            return $program->modules->count();
        });
        
        return view('professor.dashboard', compact('professor', 'assignedPrograms', 'totalPrograms', 'totalStudents', 'totalModules'));
    }

    public function programs()
    {
        $professor = auth()->user();
        $assignedPrograms = $professor->programs()->with(['students', 'modules'])->get();
        
        return view('professor.programs', compact('professor', 'assignedPrograms'));
    }

    public function programDetails($programId)
    {
        $professor = auth()->user();
        $program = $professor->programs()->where('program_id', $programId)->with(['students', 'modules'])->firstOrFail();
        
        // Get the video for this professor-program combination
        $videoData = $professor->programs()->where('program_id', $programId)->first()->pivot;
        
        return view('professor.program-details', compact('professor', 'program', 'videoData'));
    }

    public function updateVideo(Request $request, $programId)
    {
        $request->validate([
            'video_link' => 'required|url',
            'video_description' => 'nullable|string|max:500'
        ]);

        $professor = auth()->user();
        
        // Update the pivot table
        $professor->programs()->updateExistingPivot($programId, [
            'video_link' => $request->video_link,
            'video_description' => $request->video_description
        ]);

        return redirect()->back()->with('success', 'Video link updated successfully!');
    }
}
