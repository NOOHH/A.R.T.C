<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\Activity;
use App\Models\Quiz;
use App\Models\StudentGrade;
use App\Models\Deadline;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GradingController extends Controller
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
        
        if ($selectedProgramId) {
            $students = Student::whereHas('enrollments', function ($query) use ($selectedProgramId) {
                $query->where('program_id', $selectedProgramId);
            })->with(['grades' => function ($query) use ($selectedProgramId) {
                $query->where('program_id', $selectedProgramId);
            }])->get();
        }
        
        return view('professor.grading.index', compact('assignedPrograms', 'students', 'selectedProgramId'));
    }

    public function studentDetails($studentId, Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        $student = Student::findOrFail($studentId);
        
        // Check if professor is assigned to any of the student's programs
        $studentPrograms = $student->enrollments()->pluck('program_id');
        $professorPrograms = $professor->programs()->pluck('program_id');
        
        if ($studentPrograms->intersect($professorPrograms)->isEmpty()) {
            return redirect()->back()->with('error', 'You are not authorized to grade this student.');
        }
        
        $programId = $request->get('program_id', $studentPrograms->first());
        
        // Get all assignments, activities, and quizzes for this program
        $assignments = Assignment::where('professor_id', $professor->professor_id)
                                ->where('program_id', $programId)
                                ->where('is_active', true)
                                ->get();
        
        $activities = Activity::where('professor_id', $professor->professor_id)
                             ->where('program_id', $programId)
                             ->where('is_active', true)
                             ->get();
        
        $quizzes = Quiz::where('professor_id', $professor->professor_id)
                      ->where('program_id', $programId)
                      ->where('is_active', true)
                      ->get();
        
        // Get student's grades
        $grades = StudentGrade::where('student_id', $studentId)
                             ->where('program_id', $programId)
                             ->get()
                             ->keyBy(function ($grade) {
                                 return $grade->grade_type . '_' . $grade->reference_id;
                             });
        
        return view('professor.grading.student-details', compact(
            'student', 'assignments', 'activities', 'quizzes', 'grades', 'programId'
        ));
    }

    public function gradeAssignment(Request $request, $studentId, $assignmentId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
            'program_id' => 'required|exists:programs,program_id'
        ]);

        $professor = Professor::find(session('professor_id'));
        $assignment = Assignment::where('assignment_id', $assignmentId)
                                ->where('professor_id', $professor->professor_id)
                                ->firstOrFail();

        // Create or update grade
        StudentGrade::updateOrCreate([
            'student_id' => $studentId,
            'program_id' => $request->program_id,
            'grade_type' => 'assignment',
            'reference_id' => $assignmentId,
        ], [
            'grade' => $request->grade,
            'max_points' => $assignment->max_points,
            'feedback' => $request->feedback,
            'graded_by' => $professor->professor_id,
            'graded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Assignment graded successfully!');
    }

    public function gradeActivity(Request $request, $studentId, $activityId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
            'program_id' => 'required|exists:programs,program_id'
        ]);

        $professor = Professor::find(session('professor_id'));
        $activity = Activity::where('activity_id', $activityId)
                           ->where('professor_id', $professor->professor_id)
                           ->firstOrFail();

        // Create or update grade
        StudentGrade::updateOrCreate([
            'student_id' => $studentId,
            'program_id' => $request->program_id,
            'grade_type' => 'activity',
            'reference_id' => $activityId,
        ], [
            'grade' => $request->grade,
            'max_points' => $activity->max_points,
            'feedback' => $request->feedback,
            'graded_by' => $professor->professor_id,
            'graded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Activity graded successfully!');
    }

    public function gradeQuiz(Request $request, $studentId, $quizId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
            'program_id' => 'required|exists:programs,program_id'
        ]);

        $professor = Professor::find(session('professor_id'));
        $quiz = Quiz::where('quiz_id', $quizId)
                   ->where('professor_id', $professor->professor_id)
                   ->firstOrFail();

        $maxPoints = $quiz->questions()->sum('points') ?: 100;

        // Create or update grade
        StudentGrade::updateOrCreate([
            'student_id' => $studentId,
            'program_id' => $request->program_id,
            'grade_type' => 'quiz',
            'reference_id' => $quizId,
        ], [
            'grade' => $request->grade,
            'max_points' => $maxPoints,
            'feedback' => $request->feedback,
            'graded_by' => $professor->professor_id,
            'graded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Quiz graded successfully!');
    }

    public function createAssignment(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'max_points' => 'required|integer|min:1|max:1000',
            'due_date' => 'required|date|after:now',
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Check if professor is assigned to this program
        if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
            return redirect()->back()->with('error', 'You are not assigned to this program.');
        }

        $assignment = Assignment::create([
            'professor_id' => $professor->professor_id,
            'program_id' => $request->program_id,
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'max_points' => $request->max_points,
            'due_date' => $request->due_date,
            'is_active' => true,
        ]);

        // Sync with students - add to deadlines
        $this->syncAssignmentWithStudents($assignment);

        return redirect()->back()->with('success', 'Assignment created successfully and added to student deadlines!');
    }

    public function createActivity(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'max_points' => 'required|integer|min:1|max:1000',
            'due_date' => 'required|date|after:now',
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Check if professor is assigned to this program
        if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
            return redirect()->back()->with('error', 'You are not assigned to this program.');
        }

        $activity = Activity::create([
            'professor_id' => $professor->professor_id,
            'program_id' => $request->program_id,
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'max_points' => $request->max_points,
            'due_date' => $request->due_date,
            'is_active' => true,
        ]);

        // Sync with students - add to deadlines
        $this->syncActivityWithStudents($activity);

        return redirect()->back()->with('success', 'Activity created successfully and added to student deadlines!');
    }

    private function syncAssignmentWithStudents($assignment)
    {
        // Get all students enrolled in this program
        $students = Student::whereHas('enrollments', function ($query) use ($assignment) {
            $query->where('program_id', $assignment->program_id);
        })->get();

        // Add assignment deadline for each student
        foreach ($students as $student) {
            Deadline::create([
                'student_id' => $student->student_id,
                'program_id' => $assignment->program_id,
                'title' => 'Assignment: ' . $assignment->title,
                'description' => $assignment->description ?? 'Complete the assigned assignment',
                'type' => 'assignment',
                'reference_id' => $assignment->assignment_id,
                'due_date' => $assignment->due_date,
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }
    }

    private function syncActivityWithStudents($activity)
    {
        // Get all students enrolled in this program
        $students = Student::whereHas('enrollments', function ($query) use ($activity) {
            $query->where('program_id', $activity->program_id);
        })->get();

        // Add activity deadline for each student
        foreach ($students as $student) {
            Deadline::create([
                'student_id' => $student->student_id,
                'program_id' => $activity->program_id,
                'title' => 'Activity: ' . $activity->title,
                'description' => $activity->description ?? 'Complete the assigned activity',
                'type' => 'activity',
                'reference_id' => $activity->activity_id,
                'due_date' => $activity->due_date,
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }
    }
}
