<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Registration;
use App\Models\Program;
use App\Models\Module;
use App\Models\Batch;
use App\Models\StudentActivity;
use App\Models\Quiz;
use App\Models\QuizResult;
use Carbon\Carbon;

class StudentAnalyticsController extends Controller
{
    public function index()
    {
        // Check if user is logged in via session
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is a student
        if (session('user_role') !== 'student') {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied.');
        }

        try {
            $userId = session('user_id');
            $user = User::find($userId);
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'User not found.');
            }

            // Get student's analytics data
            $analytics = $this->getStudentAnalytics($userId);

            return view('student.analytics.dashboard', compact('analytics', 'user'));
        } catch (\Exception $e) {
            Log::error('Student analytics error: ' . $e->getMessage());
            return redirect()->route('student.dashboard')
                ->with('error', 'Error loading analytics dashboard.');
        }
    }

    private function getStudentAnalytics($userId)
    {
        try {
            // Get student's enrollments and progress
            $enrollments = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('enrollments.user_id', $userId)
                ->select('enrollments.*', 'programs.program_name')
                ->get();

            // Get quiz results
            $quizResults = DB::table('quiz_results')
                ->join('quizzes', 'quiz_results.quiz_id', '=', 'quizzes.quiz_id')
                ->join('modules', 'quizzes.module_id', '=', 'modules.module_id')
                ->where('quiz_results.user_id', $userId)
                ->select('quiz_results.*', 'quizzes.quiz_title', 'modules.module_name')
                ->get();

            // Calculate metrics
            $totalQuizzes = $quizResults->count();
            $avgScore = $totalQuizzes > 0 ? $quizResults->avg('score') : 0;
            $passedQuizzes = $quizResults->where('score', '>=', 70)->count();
            $passRate = $totalQuizzes > 0 ? ($passedQuizzes / $totalQuizzes) * 100 : 0;

            // Get progress data
            $progressData = [];
            foreach ($enrollments as $enrollment) {
                $modules = DB::table('modules')
                    ->where('program_id', $enrollment->program_id)
                    ->get();

                $completedModules = DB::table('student_progress')
                    ->where('user_id', $userId)
                    ->whereIn('module_id', $modules->pluck('module_id'))
                    ->where('is_completed', true)
                    ->count();

                $progressData[] = [
                    'program_name' => $enrollment->program_name,
                    'total_modules' => $modules->count(),
                    'completed_modules' => $completedModules,
                    'progress_percentage' => $modules->count() > 0 ? 
                        round(($completedModules / $modules->count()) * 100, 2) : 0
                ];
            }

            return [
                'total_enrollments' => $enrollments->count(),
                'total_quizzes' => $totalQuizzes,
                'average_score' => round($avgScore, 2),
                'pass_rate' => round($passRate, 2),
                'quiz_results' => $quizResults,
                'progress_data' => $progressData,
                'enrollments' => $enrollments
            ];

        } catch (\Exception $e) {
            Log::error('Error getting student analytics: ' . $e->getMessage());
            return [
                'total_enrollments' => 0,
                'total_quizzes' => 0,
                'average_score' => 0,
                'pass_rate' => 0,
                'quiz_results' => collect(),
                'progress_data' => [],
                'enrollments' => collect()
            ];
        }
    }
}