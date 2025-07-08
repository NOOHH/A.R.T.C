<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Program;
use App\Models\Student;
use App\Models\AdminSetting;
use App\Models\FormRequirement;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfessorDashboardController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated professors can access these methods
        $this->middleware('professor.auth');
    }

    public function index()
    {
        // Get professor from session
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->with(['modules'])->get();
        
        // Calculate students for each program
        foreach ($assignedPrograms as $program) {
            $program->students = Student::whereHas('enrollments', function ($query) use ($program) {
                $query->where('program_id', $program->program_id);
            })->get();
        }
        
        // Get statistics
        $totalPrograms = $assignedPrograms->count();
        $totalStudents = $assignedPrograms->sum(function($program) {
            return $program->students->count();
        });
        $totalModules = $assignedPrograms->sum(function($program) {
            return $program->modules->count();
        });
        
        // Check if AI Quiz feature is enabled
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        
        return view('professor.dashboard', compact('professor', 'assignedPrograms', 'totalPrograms', 'totalStudents', 'totalModules', 'aiQuizEnabled'));
    }

    public function programs()
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->with(['modules'])->get();
        
        // Calculate students for each program
        foreach ($assignedPrograms as $program) {
            $program->students = Student::whereHas('enrollments', function ($query) use ($program) {
                $query->where('program_id', $program->program_id);
            })->get();
        }
        
        return view('professor.programs', compact('professor', 'assignedPrograms'));
    }

    public function programDetails($programId)
    {
        $professor = Professor::find(session('professor_id'));
        $program = $professor->programs()->where('professor_program.program_id', $programId)->with(['modules'])->firstOrFail();
        
        // Calculate students for this program
        $program->students = Student::whereHas('enrollments', function ($query) use ($program) {
            $query->where('program_id', $program->program_id);
        })->get();
        
        // Get the video for this professor-program combination
        $videoData = $professor->programs()->where('professor_program.program_id', $programId)->first()->pivot;
        
        return view('professor.program-details', compact('professor', 'program', 'videoData'));
    }

    public function updateVideo(Request $request, $programId)
    {
        $request->validate([
            'video_link' => 'required|url',
            'video_description' => 'nullable|string|max:500'
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Update the pivot table
        $professor->programs()->updateExistingPivot($programId, [
            'video_link' => $request->video_link,
            'video_description' => $request->video_description,
            'updated_at' => now()
        ]);

        // Create announcement for students about the new video
        $program = Program::find($programId);
        
        Announcement::create([
            'professor_id' => $professor->professor_id,
            'program_id' => $programId,
            'title' => 'New Video Available: ' . $program->program_name,
            'content' => $request->video_description ?? 'A new video has been uploaded for this program.',
            'type' => 'video',
            'video_link' => $request->video_link,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Video link updated successfully and announcement sent to students!');
    }

    public function profile()
    {
        $professor = Professor::find(session('professor_id'));
        
        // Get dynamic form fields for professors (if any exist)
        $dynamicFields = FormRequirement::where('entity_type', 'professor')
            ->where('is_active', true)
            ->orderBy('field_order')
            ->get();
        
        return view('professor.profile', compact('professor', 'dynamicFields'));
    }

    public function updateProfile(Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        
        // Get dynamic fields for validation
        $dynamicFields = FormRequirement::where('entity_type', 'professor')
            ->where('is_active', true)
            ->get();
        
        $validationRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:professors,professor_email,' . $professor->professor_id . ',professor_id',
            'password' => 'nullable|string|min:8|confirmed',
        ];
        
        // Add dynamic field validation rules
        foreach ($dynamicFields as $field) {
            $rule = [];
            if ($field->is_required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }
            
            switch ($field->field_type) {
                case 'email':
                    $rule[] = 'email';
                    break;
                case 'phone':
                    $rule[] = 'string|max:20';
                    break;
                case 'date':
                    $rule[] = 'date';
                    break;
                case 'select':
                    if ($field->field_options) {
                        $options = json_decode($field->field_options, true);
                        $rule[] = 'in:' . implode(',', $options);
                    }
                    break;
                default:
                    $rule[] = 'string|max:500';
                    break;
            }
            
            $validationRules['dynamic.' . $field->field_name] = implode('|', $rule);
        }
        
        $request->validate($validationRules);

        $professor->first_name = $request->first_name;
        $professor->last_name = $request->last_name;
        $professor->email = $request->email;
        
        if ($request->filled('password')) {
            $professor->password = Hash::make($request->password);
        }
        
        // Handle dynamic fields
        if ($request->has('dynamic')) {
            $dynamicData = $professor->dynamic_data ?? [];
            foreach ($request->dynamic as $fieldName => $value) {
                $dynamicData[$fieldName] = $value;
            }
            $professor->dynamic_data = $dynamicData;
        }
        
        $professor->save();

        // Update session data
        session([
            'professor_name' => $professor->full_name,
            'professor_email' => $professor->professor_email,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function studentList()
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        // Get all students enrolled in professor's programs
        $students = Student::whereHas('enrollments', function ($query) use ($assignedPrograms) {
            $query->whereIn('program_id', $assignedPrograms->pluck('program_id'));
        })->with(['enrollments.program', 'enrollments.package'])->get();
        
        return view('professor.students', compact('professor', 'students', 'assignedPrograms'));
    }

    public function gradeStudent(Request $request, $studentId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $professor = Professor::find(session('professor_id'));
        $student = Student::findOrFail($studentId);
        
        // Check if professor is assigned to any of the student's programs
        $studentPrograms = $student->enrollments()->pluck('program_id');
        $professorPrograms = $professor->programs()->pluck('program_id');
        
        if ($studentPrograms->intersect($professorPrograms)->isEmpty()) {
            return redirect()->back()->with('error', 'You are not authorized to grade this student.');
        }
        
        // Here you can implement grading logic
        // For now, we'll just return success
        return redirect()->back()->with('success', 'Grade assigned successfully!');
    }
}
