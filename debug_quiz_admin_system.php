<?php
/**
 * Debug Quiz Admin System
 * Test script to verify quiz creation by both admin and professor
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Program;
use App\Models\Module;
use App\Models\Course;
use App\Models\Admin;
use App\Models\Professor;
use Illuminate\Support\Facades\DB;

echo "=== QUIZ ADMIN SYSTEM DEBUG ===\n\n";

try {
    // 1. Check database structure
    echo "1. CHECKING DATABASE STRUCTURE:\n";
    echo "------------------------------\n";
    
    $quizColumns = DB::select("SHOW COLUMNS FROM quizzes");
    echo "Quizzes table columns:\n";
    foreach ($quizColumns as $column) {
        echo "  - {$column->Field} ({$column->Type}) - Null: {$column->Null}\n";
    }
    
    // 2. Check existing data
    echo "\n2. CHECKING EXISTING DATA:\n";
    echo "-------------------------\n";
    
    $totalQuizzes = Quiz::count();
    $adminCreatedQuizzes = Quiz::whereNotNull('admin_id')->count();
    $professorCreatedQuizzes = Quiz::whereNotNull('professor_id')->whereNull('admin_id')->count();
    $legacyQuizzes = Quiz::whereNull('admin_id')->whereNull('professor_id')->count();
    
    echo "Total quizzes: {$totalQuizzes}\n";
    echo "Admin created quizzes: {$adminCreatedQuizzes}\n";
    echo "Professor created quizzes: {$professorCreatedQuizzes}\n";
    echo "Legacy quizzes (no creator): {$legacyQuizzes}\n";
    
    // 3. Show quiz breakdown by status
    echo "\n3. QUIZ BREAKDOWN BY STATUS:\n";
    echo "---------------------------\n";
    
    $quizStatusBreakdown = Quiz::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    
    foreach ($quizStatusBreakdown as $status) {
        echo "  {$status->status}: {$status->count} quizzes\n";
    }
    
    // 4. Show recent quizzes with creator info
    echo "\n4. RECENT QUIZZES WITH CREATOR INFO:\n";
    echo "-----------------------------------\n";
    
    $recentQuizzes = Quiz::with(['program'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($recentQuizzes as $quiz) {
        $creator = 'Unknown';
        if ($quiz->admin_id) {
            $creator = "Admin (ID: {$quiz->admin_id})";
        } elseif ($quiz->professor_id) {
            $creator = "Professor (ID: {$quiz->professor_id})";
        }
        
        echo "  Quiz ID: {$quiz->quiz_id}\n";
        echo "    Title: {$quiz->quiz_title}\n";
        echo "    Status: {$quiz->status}\n";
        echo "    Creator: {$creator}\n";
        $programName = $quiz->program ? $quiz->program->program_name : 'N/A';
        echo "    Program: {$programName}\n";
        echo "    Created: {$quiz->created_at}\n\n";
    }
    
    // 5. Check admin and professor accounts
    echo "5. CHECKING ADMIN AND PROFESSOR ACCOUNTS:\n";
    echo "----------------------------------------\n";
    
    $adminCount = Admin::count();
    $professorCount = Professor::count();
    
    echo "Total admin accounts: {$adminCount}\n";
    echo "Total professor accounts: {$professorCount}\n";
    
    if ($adminCount > 0) {
        echo "\nAdmin accounts:\n";
        $admins = Admin::all();
        foreach ($admins as $admin) {
            echo "  - ID: {$admin->admin_id}, Name: {$admin->admin_name}, Email: {$admin->email}\n";
        }
    }
    
    if ($professorCount > 0) {
        echo "\nProfessor accounts (first 5):\n";
        $professors = Professor::limit(5)->get();
        foreach ($professors as $professor) {
            echo "  - ID: {$professor->professor_id}, Name: {$professor->professor_firstname} {$professor->professor_lastname}, Email: {$professor->professor_email}\n";
        }
    }
    
    // 6. Test quiz creation for admin
    echo "\n6. TESTING ADMIN QUIZ CREATION:\n";
    echo "------------------------------\n";
    
    // Get first admin
    $admin = Admin::first();
    if (!$admin) {
        echo "ERROR: No admin found for testing!\n";
    } else {
        echo "Testing with Admin ID: {$admin->admin_id}\n";
        
        // Get first program for testing
        $program = Program::where('is_archived', false)->first();
        if (!$program) {
            echo "ERROR: No active program found for testing!\n";
        } else {
            echo "Using Program: {$program->program_name} (ID: {$program->program_id})\n";
            
            // Check if we can create a test quiz
            try {
                $testQuiz = Quiz::create([
                    'admin_id' => $admin->admin_id,
                    'professor_id' => null,
                    'program_id' => $program->program_id,
                    'quiz_title' => 'DEBUG TEST QUIZ - Admin Created ' . date('Y-m-d H:i:s'),
                    'quiz_description' => 'This is a test quiz created by debug script',
                    'instructions' => 'Test instructions',
                    'status' => 'draft',
                    'is_draft' => true,
                    'is_active' => false,
                    'total_questions' => 0,
                    'time_limit' => 60,
                ]);
                
                echo "✓ Successfully created test quiz with ID: {$testQuiz->quiz_id}\n";
                
                // Clean up - delete the test quiz
                $testQuiz->delete();
                echo "✓ Test quiz cleaned up\n";
                
            } catch (Exception $e) {
                echo "ERROR creating test quiz: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 7. Check quiz questions
    echo "\n7. CHECKING QUIZ QUESTIONS:\n";
    echo "--------------------------\n";
    
    $totalQuestions = QuizQuestion::count();
    $questionsWithQuizzes = QuizQuestion::whereExists(function($query) {
        $query->select(DB::raw(1))
              ->from('quizzes')
              ->whereRaw('quizzes.quiz_id = quiz_questions.quiz_id');
    })->count();
    
    echo "Total quiz questions: {$totalQuestions}\n";
    echo "Questions with valid quizzes: {$questionsWithQuizzes}\n";
    echo "Orphaned questions: " . ($totalQuestions - $questionsWithQuizzes) . "\n";
    
    // 8. Simulate admin session data
    echo "\n8. SIMULATING ADMIN SESSION:\n";
    echo "---------------------------\n";
    
    if ($admin) {
        // Simulate what happens during admin login
        $sessionData = [
            'user_id' => $admin->admin_id,
            'user_name' => $admin->admin_name,
            'user_email' => $admin->email,
            'user_type' => 'admin',
            'logged_in' => true
        ];
        
        echo "Admin session data would be:\n";
        foreach ($sessionData as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
        
        // Test the admin quiz query that would be used in controller
        echo "\nTesting admin quiz query:\n";
        $adminQuizzes = Quiz::where(function($query) use ($admin) {
                            $query->where('admin_id', $admin->admin_id)
                                  ->orWhereNull('admin_id');
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        echo "Admin would see {$adminQuizzes->count()} quizzes\n";
        
        $draftCount = $adminQuizzes->where('status', 'draft')->count();
        $publishedCount = $adminQuizzes->where('status', 'published')->count();
        $archivedCount = $adminQuizzes->where('status', 'archived')->count();
        
        echo "  - Draft: {$draftCount}\n";
        echo "  - Published: {$publishedCount}\n";
        echo "  - Archived: {$archivedCount}\n";
    }
    
    echo "\n=== DEBUG COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
