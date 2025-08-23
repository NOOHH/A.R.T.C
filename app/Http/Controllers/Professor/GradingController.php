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
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GradingController extends Controller
{
    public function __construct()
    {
        $this->middleware('professor.auth')->except(['previewIndex']);
    }

    public function index(Request $request)
    {
        // Check if this is a preview request - handle before any other logic
        if (request()->has('preview') && request('preview') === 'true') {
            return $this->previewIndex();
        }
        
        // Check if this is a tenant preview context
        if (request()->route() && str_contains(request()->route()->getName() ?? '', 'tenant.')) {
            return $this->previewIndex();
        }
        
        // Check if this is preview mode
        if (session('professor_id') === 'preview-professor') {
            return $this->previewIndex();
        }
        
        // Check for additional preview contexts
        if (request()->has('website') || session('preview_mode')) {
            return $this->previewIndex();
        }

        $professor = Professor::find(session('professor_id'));
        if (!$professor) {
            return redirect()->route('professor.login')->withErrors('Session expired. Please log in again.');
        }
        
        $assignedPrograms = $professor->programs()->get();
        
        $selectedProgramId = $request->get('program_id');
        $students = collect();
        $programAnalytics = null;
        
        if ($selectedProgramId) {
            // Get students with their grades and quiz submissions
            $students = Student::whereHas('enrollments', function ($query) use ($selectedProgramId) {
                $query->where('program_id', $selectedProgramId);
            })->with([
                'grades' => function ($query) use ($selectedProgramId) {
                    $query->where('program_id', $selectedProgramId);
                },
                'quizSubmissions' => function ($query) use ($selectedProgramId) {
                    $query->whereHas('quiz', function ($q) use ($selectedProgramId) {
                        $q->where('program_id', $selectedProgramId);
                    });
                }
            ])->get();
            
            // Calculate program analytics
            $programAnalytics = $this->calculateProgramAnalytics($selectedProgramId, $professor);
        }
        
        return view('professor.grading.index', compact(
            'assignedPrograms', 
            'students', 
            'selectedProgramId', 
            'programAnalytics'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'program_id' => 'required|exists:programs,program_id',
            'assignment_name' => 'required|string|max:255',
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Verify professor has access to this program
        if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
            return back()->with('error', 'You are not authorized to grade this program.');
        }

        try {
            // Create or update grade
            StudentGrade::updateOrCreate([
                'student_id' => $request->student_id,
                'program_id' => $request->program_id,
                'grade_type' => 'assignment',
                'reference_name' => $request->assignment_name
            ], [
                'grade' => $request->grade,
                'feedback' => $request->feedback,
                'professor_id' => $professor->professor_id,
                'graded_at' => now()
            ]);

            return back()->with('success', 'Grade added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add grade: ' . $e->getMessage());
        }
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

    public function createAssignmentForm()
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        return view('professor.assignments.create', compact('assignedPrograms'));
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

    /**
     * Calculate comprehensive program analytics
     */
    private function calculateProgramAnalytics($programId, $professor)
    {
        try {
            // Get all students in the program
            $students = Student::whereHas('enrollments', function ($query) use ($programId) {
                $query->where('program_id', $programId);
            })->get();

            // Get all quiz submissions for this program
            $quizSubmissions = \App\Models\QuizSubmission::whereHas('quiz', function ($query) use ($programId, $professor) {
                $query->where('program_id', $programId)
                      ->where('professor_id', $professor->professor_id);
            })->get();

            // Get all grades for this program
            $grades = StudentGrade::where('program_id', $programId)
                                ->where('graded_by', $professor->professor_id)
                                ->get();

            $analytics = [
                'total_students' => $students->count(),
                'total_quizzes_taken' => $quizSubmissions->count(),
                'average_quiz_score' => $quizSubmissions->avg('score') ?? 0,
                'average_grade' => $grades->avg('grade') ?? 0,
                'completion_rate' => $students->count() > 0 ? 
                    ($quizSubmissions->groupBy('student_id')->count() / $students->count()) * 100 : 0,
                'grade_distribution' => $this->calculateGradeDistribution($grades),
                'quiz_performance' => $this->calculateQuizPerformance($quizSubmissions),
                'low_performers' => $this->identifyLowPerformers($students, $programId),
                'top_performers' => $this->identifyTopPerformers($students, $programId)
            ];

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Analytics calculation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate grade distribution for analytics
     */
    private function calculateGradeDistribution($grades)
    {
        $distribution = [
            'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0
        ];

        foreach ($grades as $grade) {
            $percentage = $grade->grade;
            if ($percentage >= 90) $distribution['A']++;
            elseif ($percentage >= 80) $distribution['B']++;
            elseif ($percentage >= 70) $distribution['C']++;
            elseif ($percentage >= 60) $distribution['D']++;
            else $distribution['F']++;
        }

        return $distribution;
    }

    /**
     * Calculate quiz performance analytics
     */
    private function calculateQuizPerformance($quizSubmissions)
    {
        $quizAnalytics = [];
        $groupedByQuiz = $quizSubmissions->groupBy('quiz_id');

        foreach ($groupedByQuiz as $quizId => $submissions) {
            $quiz = \App\Models\Quiz::find($quizId);
            if ($quiz) {
                $quizAnalytics[] = [
                    'quiz_title' => $quiz->quiz_title,
                    'total_submissions' => $submissions->count(),
                    'average_score' => $submissions->avg('score'),
                    'highest_score' => $submissions->max('score'),
                    'lowest_score' => $submissions->min('score'),
                    'completion_rate' => ($submissions->count() / $quiz->program->students()->count()) * 100
                ];
            }
        }

        return $quizAnalytics;
    }

    /**
     * Identify students who need attention (low performers)
     */
    private function identifyLowPerformers($students, $programId)
    {
        $lowPerformers = [];
        
        foreach ($students as $student) {
            $averageGrade = $student->grades()
                ->where('program_id', $programId)
                ->avg('grade');
            
            $averageQuizScore = $student->quizSubmissions()
                ->whereHas('quiz', function ($query) use ($programId) {
                    $query->where('program_id', $programId);
                })
                ->avg('score');

            $overallAverage = ($averageGrade + $averageQuizScore) / 2;

            if ($overallAverage < 70 && $overallAverage > 0) {
                $lowPerformers[] = [
                    'student' => $student,
                    'average' => $overallAverage,
                    'grade_count' => $student->grades()->where('program_id', $programId)->count(),
                    'quiz_count' => $student->quizSubmissions()->whereHas('quiz', function ($query) use ($programId) {
                        $query->where('program_id', $programId);
                    })->count()
                ];
            }
        }

        return collect($lowPerformers)->sortBy('average')->take(5);
    }

    /**
     * Identify top performing students
     */
    private function identifyTopPerformers($students, $programId)
    {
        $topPerformers = [];
        
        foreach ($students as $student) {
            $averageGrade = $student->grades()
                ->where('program_id', $programId)
                ->avg('grade');
            
            $averageQuizScore = $student->quizSubmissions()
                ->whereHas('quiz', function ($query) use ($programId) {
                    $query->where('program_id', $programId);
                })
                ->avg('score');

            $overallAverage = ($averageGrade + $averageQuizScore) / 2;

            if ($overallAverage >= 85) {
                $topPerformers[] = [
                    'student' => $student,
                    'average' => $overallAverage,
                    'grade_count' => $student->grades()->where('program_id', $programId)->count(),
                    'quiz_count' => $student->quizSubmissions()->whereHas('quiz', function ($query) use ($programId) {
                        $query->where('program_id', $programId);
                    })->count()
                ];
            }
        }

        return collect($topPerformers)->sortByDesc('average')->take(5);
    }

    /**
     * Get comprehensive student grade details with quiz results
     */
    public function getStudentGradeDetails($studentId, $programId)
    {
        $student = Student::findOrFail($studentId);
        
        // Get all grades
        $grades = StudentGrade::where('student_id', $studentId)
                             ->where('program_id', $programId)
                             ->orderBy('graded_at', 'desc')
                             ->get();

        // Get all quiz submissions
        $quizSubmissions = \App\Models\QuizSubmission::where('student_id', $studentId)
            ->whereHas('quiz', function ($query) use ($programId) {
                $query->where('program_id', $programId);
            })
            ->with('quiz')
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Calculate comprehensive statistics
        $statistics = [
            'total_grades' => $grades->count(),
            'average_grade' => $grades->avg('grade') ?? 0,
            'total_quizzes' => $quizSubmissions->count(),
            'average_quiz_score' => $quizSubmissions->avg('score') ?? 0,
            'overall_average' => ($grades->avg('grade') + $quizSubmissions->avg('score')) / 2,
            'trend_analysis' => $this->calculateGradeTrend($grades, $quizSubmissions)
        ];

        return [
            'student' => $student,
            'grades' => $grades,
            'quiz_submissions' => $quizSubmissions,
            'statistics' => $statistics
        ];
    }

    /**
     * Calculate grade trend analysis
     */
    private function calculateGradeTrend($grades, $quizSubmissions)
    {
        $allScores = collect();
        
        // Combine grades and quiz scores with dates
        foreach ($grades as $grade) {
            $allScores->push([
                'score' => $grade->grade,
                'date' => $grade->graded_at,
                'type' => 'grade'
            ]);
        }
        
        foreach ($quizSubmissions as $submission) {
            $allScores->push([
                'score' => $submission->score,
                'date' => $submission->submitted_at,
                'type' => 'quiz'
            ]);
        }

        $sortedScores = $allScores->sortBy('date')->values();
        
        if ($sortedScores->count() < 2) {
            return 'insufficient_data';
        }

        $firstHalf = $sortedScores->take(ceil($sortedScores->count() / 2));
        $secondHalf = $sortedScores->skip(ceil($sortedScores->count() / 2));

        $firstAverage = $firstHalf->avg('score');
        $secondAverage = $secondHalf->avg('score');

        $improvement = $secondAverage - $firstAverage;

        if ($improvement > 5) return 'improving';
        elseif ($improvement < -5) return 'declining';
        else return 'stable';
    }

    /**
     * Export grades for a program
     */
    public function exportGrades(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'format' => 'required|in:csv,pdf'
        ]);

        $professor = Professor::find(session('professor_id'));
        $program = \App\Models\Program::findOrFail($request->program_id);
        
        // Verify professor has access
        if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $students = Student::whereHas('enrollments', function ($query) use ($request) {
            $query->where('program_id', $request->program_id);
        })->with([
            'grades' => function ($query) use ($request) {
                $query->where('program_id', $request->program_id);
            },
            'quizSubmissions' => function ($query) use ($request) {
                $query->whereHas('quiz', function ($q) use ($request) {
                    $q->where('program_id', $request->program_id);
                });
            }
        ])->get();

        if ($request->input('format') === 'csv') {
            return $this->exportToCSV($students, $program);
        } else {
            return $this->exportToPDF($students, $program);
        }
    }

    /**
     * Export grades to CSV
     */
    private function exportToCSV($students, $program)
    {
        $filename = "grades_" . str_replace(' ', '_', $program->program_name) . "_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Student ID', 'Student Name', 'Email', 'Total Grades', 'Average Grade',
                'Total Quizzes', 'Average Quiz Score', 'Overall Average', 'Status'
            ]);

            foreach ($students as $student) {
                $averageGrade = $student->grades->avg('grade') ?? 0;
                $averageQuiz = $student->quizSubmissions->avg('score') ?? 0;
                $overallAverage = ($averageGrade + $averageQuiz) / 2;
                
                $status = $overallAverage >= 75 ? 'Passing' : ($overallAverage >= 60 ? 'At Risk' : 'Failing');

                fputcsv($file, [
                    $student->student_id,
                    $student->user->user_firstname . ' ' . $student->user->user_lastname,
                    $student->user->user_email,
                    $student->grades->count(),
                    number_format($averageGrade, 2),
                    $student->quizSubmissions->count(),
                    number_format($averageQuiz, 2),
                    number_format($overallAverage, 2),
                    $status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Auto-grade quiz submissions
     */
    public function autoGradeQuizzes(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id'
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Get ungraded quiz submissions
        $submissions = \App\Models\QuizSubmission::whereHas('quiz', function ($query) use ($request, $professor) {
            $query->where('program_id', $request->program_id)
                  ->where('professor_id', $professor->professor_id);
        })->whereDoesntHave('grade')->get();

        $gradedCount = 0;

        foreach ($submissions as $submission) {
            try {
                // Create grade record from quiz submission
                StudentGrade::create([
                    'student_id' => $submission->student_id,
                    'program_id' => $request->program_id,
                    'grade_type' => 'quiz',
                    'reference_id' => $submission->quiz_id,
                    'reference_name' => $submission->quiz->quiz_title,
                    'grade' => $submission->score,
                    'max_points' => $submission->total_questions,
                    'feedback' => 'Auto-graded quiz submission',
                    'graded_by' => $professor->professor_id,
                    'graded_at' => now(),
                ]);

                $gradedCount++;
            } catch (\Exception $e) {
                Log::error('Auto-grading failed for submission: ' . $submission->id);
            }
        }

        return redirect()->back()->with('success', "Successfully auto-graded {$gradedCount} quiz submissions.");
    }

    /**
     * Export grades to PDF (placeholder - can be enhanced with actual PDF generation)
     */
    private function exportToPDF($students, $program)
    {
        // For now, return a simple HTML view that can be printed as PDF
        return view('professor.grading.pdf-export', compact('students', 'program'));
    }

    /**
     * Get detailed analytics for a specific quiz
     */
    public function getQuizAnalytics($quizId)
    {
        $professor = Professor::find(session('professor_id'));
        $quiz = Quiz::where('quiz_id', $quizId)
                   ->where('professor_id', $professor->professor_id)
                   ->firstOrFail();

        $submissions = \App\Models\QuizSubmission::where('quiz_id', $quizId)->get();
        
        $analytics = [
            'quiz' => $quiz,
            'total_submissions' => $submissions->count(),
            'average_score' => $submissions->avg('score'),
            'highest_score' => $submissions->max('score'),
            'lowest_score' => $submissions->min('score'),
            'score_distribution' => $this->calculateScoreDistribution($submissions),
            'question_analysis' => $this->analyzeQuestionPerformance($quiz, $submissions)
        ];

        return response()->json($analytics);
    }

    /**
     * Calculate score distribution for quiz analytics
     */
    private function calculateScoreDistribution($submissions)
    {
        $distribution = [
            '90-100' => 0,
            '80-89' => 0,
            '70-79' => 0,
            '60-69' => 0,
            'Below 60' => 0
        ];

        foreach ($submissions as $submission) {
            $score = $submission->score;
            if ($score >= 90) $distribution['90-100']++;
            elseif ($score >= 80) $distribution['80-89']++;
            elseif ($score >= 70) $distribution['70-79']++;
            elseif ($score >= 60) $distribution['60-69']++;
            else $distribution['Below 60']++;
        }

        return $distribution;
    }

    /**
     * Analyze question performance in quizzes
     */
    private function analyzeQuestionPerformance($quiz, $submissions)
    {
        $questionAnalysis = [];
        
        foreach ($quiz->questions as $question) {
            $correctAnswers = 0;
            $totalAnswers = 0;
            
            foreach ($submissions as $submission) {
                $answers = json_decode($submission->answers, true);
                if (isset($answers[$question->id])) {
                    $totalAnswers++;
                    if ($answers[$question->id] === $question->correct_answer) {
                        $correctAnswers++;
                    }
                }
            }
            
            $questionAnalysis[] = [
                'question_text' => substr($question->question_text, 0, 100) . '...',
                'correct_percentage' => $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0,
                'total_responses' => $totalAnswers,
                'difficulty_level' => $this->determineDifficultyLevel($correctAnswers, $totalAnswers)
            ];
        }

        return $questionAnalysis;
    }

    /**
     * Determine question difficulty based on success rate
     */
    private function determineDifficultyLevel($correct, $total)
    {
        if ($total === 0) return 'unknown';
        
        $percentage = ($correct / $total) * 100;
        
        if ($percentage >= 80) return 'easy';
        elseif ($percentage >= 60) return 'medium';
        else return 'hard';
    }
    
    /**
     * Preview grading page for tenant customization
     */
    public function previewIndex($tenantSlug = null)
    {
        $this->setupTenantPreviewContext($tenantSlug);
        
        // Create mock programs data
        $assignedPrograms = collect([
            (object) [
                'program_id' => 1,
                'program_name' => 'Nursing Board Review',
                'program_description' => 'Comprehensive nursing board examination review program.'
            ],
            (object) [
                'program_id' => 2,
                'program_name' => 'Medical Technology Review',
                'program_description' => 'Advanced medical technology certification review.'
            ]
        ]);
        
        // Create mock students with grades
        $students = collect([
            (object) [
                'student_id' => 1,
                'firstname' => 'John',
                'lastname' => 'Doe',
                'full_name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'overall_grade' => 85.5,
                'assignment_average' => 88.0,
                'quiz_average' => 83.0,
                'activity_average' => 86.0,
                'grades' => collect([
                    (object) ['grade_type' => 'assignment', 'score' => 90, 'max_score' => 100],
                    (object) ['grade_type' => 'assignment', 'score' => 86, 'max_score' => 100],
                    (object) ['grade_type' => 'quiz', 'score' => 85, 'max_score' => 100],
                    (object) ['grade_type' => 'quiz', 'score' => 81, 'max_score' => 100]
                ]),
                'pending_grades' => 2
            ],
            (object) [
                'student_id' => 2,
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'full_name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'overall_grade' => 92.3,
                'assignment_average' => 94.0,
                'quiz_average' => 90.5,
                'activity_average' => 92.0,
                'grades' => collect([
                    (object) ['grade_type' => 'assignment', 'score' => 95, 'max_score' => 100],
                    (object) ['grade_type' => 'assignment', 'score' => 93, 'max_score' => 100],
                    (object) ['grade_type' => 'quiz', 'score' => 91, 'max_score' => 100],
                    (object) ['grade_type' => 'quiz', 'score' => 90, 'max_score' => 100]
                ]),
                'pending_grades' => 1
            ],
            (object) [
                'student_id' => 3,
                'firstname' => 'Mike',
                'lastname' => 'Johnson',
                'full_name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'overall_grade' => 78.2,
                'assignment_average' => 80.0,
                'quiz_average' => 76.0,
                'activity_average' => 79.0,
                'grades' => collect([
                    (object) ['grade_type' => 'assignment', 'score' => 82, 'max_score' => 100],
                    (object) ['grade_type' => 'assignment', 'score' => 78, 'max_score' => 100],
                    (object) ['grade_type' => 'quiz', 'score' => 76, 'max_score' => 100],
                    (object) ['grade_type' => 'quiz', 'score' => 76, 'max_score' => 100]
                ]),
                'pending_grades' => 3
            ]
        ]);
        
        // Mock program analytics
        $programAnalytics = [
            'total_students' => 28,
            'average_grade' => 85.3,
            'average_quiz_score' => 82.7,
            'completion_rate' => 89.5,
            'class_average' => 85.3,
            'total_assignments' => 12,
            'total_quizzes' => 8,
            'total_activities' => 15,
            'graded_items' => 28,
            'pending_items' => 7,
            'quiz_performance' => [
                [
                    'quiz_title' => 'Nursing Fundamentals Assessment',
                    'total_submissions' => 15,
                    'average_score' => 85.2,
                    'completion_rate' => 93.8
                ],
                [
                    'quiz_title' => 'Pharmacology Basics Quiz', 
                    'total_submissions' => 12,
                    'average_score' => 78.8,
                    'completion_rate' => 87.5
                ],
                [
                    'quiz_title' => 'Patient Care Evaluation',
                    'total_submissions' => 18,
                    'average_score' => 91.3,
                    'completion_rate' => 100.0
                ]
            ],
            'low_performers' => collect([
                [
                    'student' => (object) [
                        'student_id' => 'STU001',
                        'user' => (object) [
                            'user_firstname' => 'Mike',
                            'user_lastname' => 'Johnson'
                        ]
                    ],
                    'average' => 65.2
                ],
                [
                    'student' => (object) [
                        'student_id' => 'STU002', 
                        'user' => (object) [
                            'user_firstname' => 'Sarah',
                            'user_lastname' => 'Wilson'
                        ]
                    ],
                    'average' => 68.9
                ]
            ]),
            'top_performers' => collect([
                [
                    'student' => (object) [
                        'student_id' => 'STU003',
                        'user' => (object) [
                            'user_firstname' => 'Jane',
                            'user_lastname' => 'Smith'
                        ]
                    ],
                    'average' => 92.3
                ],
                [
                    'student' => (object) [
                        'student_id' => 'STU004',
                        'user' => (object) [
                            'user_firstname' => 'David',
                            'user_lastname' => 'Brown'
                        ]
                    ],
                    'average' => 89.7
                ]
            ]),
            'grade_distribution' => [
                'A' => 8,
                'B' => 12,
                'C' => 5,
                'D' => 2,
                'F' => 1
            ]
        ];
        
        $selectedProgramId = 1; // Default to first program for preview
        
        // Create mock grades collection keyed by student_id
        $grades = collect([
            1 => collect([
                (object) [
                    'grade' => 90, 
                    'max_points' => 100,
                    'grade_type' => 'assignment', 
                    'assignment_name' => 'Nursing Fundamentals Quiz',
                    'created_at' => now()->subDays(5)
                ],
                (object) [
                    'grade' => 86, 
                    'max_points' => 100,
                    'grade_type' => 'assignment', 
                    'assignment_name' => 'Patient Care Assignment',
                    'created_at' => now()->subDays(3)
                ],
                (object) [
                    'grade' => 85, 
                    'max_points' => 100,
                    'grade_type' => 'quiz', 
                    'assignment_name' => 'Pharmacology Quiz',
                    'created_at' => now()->subDays(2)
                ],
                (object) [
                    'grade' => 81, 
                    'max_points' => 100,
                    'grade_type' => 'quiz', 
                    'assignment_name' => 'Ethics Quiz',
                    'created_at' => now()->subDays(1)
                ]
            ]),
            2 => collect([
                (object) [
                    'grade' => 95, 
                    'max_points' => 100,
                    'grade_type' => 'assignment', 
                    'assignment_name' => 'Nursing Fundamentals Quiz',
                    'created_at' => now()->subDays(5)
                ],
                (object) [
                    'grade' => 93, 
                    'max_points' => 100,
                    'grade_type' => 'assignment', 
                    'assignment_name' => 'Patient Care Assignment',
                    'created_at' => now()->subDays(3)
                ],
                (object) [
                    'grade' => 91, 
                    'max_points' => 100,
                    'grade_type' => 'quiz', 
                    'assignment_name' => 'Pharmacology Quiz',
                    'created_at' => now()->subDays(2)
                ],
                (object) [
                    'grade' => 90, 
                    'max_points' => 100,
                    'grade_type' => 'quiz', 
                    'assignment_name' => 'Ethics Quiz',
                    'created_at' => now()->subDays(1)
                ]
            ]),
            3 => collect([
                (object) [
                    'grade' => 82, 
                    'max_points' => 100,
                    'grade_type' => 'assignment', 
                    'assignment_name' => 'Nursing Fundamentals Quiz',
                    'created_at' => now()->subDays(5)
                ],
                (object) [
                    'grade' => 78, 
                    'max_points' => 100,
                    'grade_type' => 'assignment', 
                    'assignment_name' => 'Patient Care Assignment',
                    'created_at' => now()->subDays(3)
                ],
                (object) [
                    'grade' => 76, 
                    'max_points' => 100,
                    'grade_type' => 'quiz', 
                    'assignment_name' => 'Pharmacology Quiz',
                    'created_at' => now()->subDays(2)
                ],
                (object) [
                    'grade' => 76, 
                    'max_points' => 100,
                    'grade_type' => 'quiz', 
                    'assignment_name' => 'Ethics Quiz',
                    'created_at' => now()->subDays(1)
                ]
            ])
        ]);
        
        return view('professor.grading.index', compact(
            'assignedPrograms', 'students', 'programAnalytics', 'selectedProgramId', 'grades'
        ));
    }
    
    /**
     * Setup tenant preview context (common method for all preview methods)
     */
    private function setupTenantPreviewContext($tenantSlug = null)
    {
        // Load tenant settings if provided
        if ($tenantSlug) {
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToMain();
            
            $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();
            
            if ($tenant) {
                try {
                    $tenantService->switchToTenant($tenant);
                    
                    // Load settings from tenant database
                    $settings = [
                        'navbar' => [
                            'brand_name' => \App\Models\Setting::get('navbar', 'brand_name', 'Ascendo Review & Training Center'),
                            'brand_logo' => \App\Models\Setting::get('navbar', 'brand_logo', null),
                        ],
                        'professor_panel' => [
                            'brand_name' => \App\Models\Setting::get('professor_panel', 'brand_name', 'Ascendo Review & Training Center'),
                            'brand_logo' => \App\Models\Setting::get('professor_panel', 'brand_logo', null),
                        ],
                    ];
                    
                    $tenantService->switchToMain();
                    
                    // Share settings with the view
                    view()->share('settings', $settings);
                    view()->share('navbar', $settings['navbar'] ?? []);
                    
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to load tenant settings for professor grading preview', [
                        'tenant' => $tenant->slug,
                        'error' => $e->getMessage()
                    ]);
                    $tenantService->switchToMain();
                }
            }
        }
        
        // Set up session data for preview mode
        session([
            'user_id' => 'preview-professor',
            'user_name' => 'Dr. Jane Professor',
            'user_role' => 'professor',
            'user_type' => 'professor',
            'professor_id' => 'preview-professor',
            'logged_in' => true
        ]);
    }
}
