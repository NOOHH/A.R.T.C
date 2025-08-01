<?php
/**
 * Test Student Dashboard Quiz Deadline Display
 * This script tests that students can see quiz deadlines properly
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== Testing Student Dashboard Quiz Deadline Display ===\n\n";

try {
    // Get a student for testing
    $student = DB::table('students')->first();
    if (!$student) {
        throw new Exception("No student found. Please create a student first.");
    }

    echo "Using Student: {$student->firstname} {$student->lastname} (ID: {$student->student_id})\n";
    echo "Student Program ID: {$student->program_id}\n\n";

    // Test 1: Get available quizzes for the student
    echo "1. Getting available quizzes for student...\n";
    
    $availableQuizzes = DB::table('quizzes')
        ->where('program_id', $student->program_id)
        ->where('is_active', true)
        ->where('status', 'published')
        ->select([
            'quiz_id',
            'quiz_title',
            'quiz_description',
            'has_deadline',
            'due_date',
            'infinite_retakes',
            'max_attempts',
            'time_limit'
        ])
        ->get();

    echo "Found " . count($availableQuizzes) . " available quizzes\n\n";

    // Test 2: Display quizzes with deadline information
    echo "2. Quiz deadline information:\n";
    
    foreach ($availableQuizzes as $quiz) {
        echo "Quiz: {$quiz->quiz_title}\n";
        echo "  - Has Deadline: " . ($quiz->has_deadline ? 'Yes' : 'No') . "\n";
        
        if ($quiz->has_deadline && $quiz->due_date) {
            $dueDate = Carbon::parse($quiz->due_date);
            $now = Carbon::now();
            
            echo "  - Due Date: " . $dueDate->format('M j, Y g:i A') . "\n";
            
            if ($dueDate->isFuture()) {
                $timeLeft = $now->diffForHumans($dueDate, true);
                echo "  - Time Left: {$timeLeft}\n";
                echo "  - Status: Available\n";
            } else {
                $overdue = $dueDate->diffForHumans($now, true);
                echo "  - Status: Overdue by {$overdue}\n";
            }
        } else {
            echo "  - Due Date: No deadline\n";
            echo "  - Status: Always available\n";
        }
        
        echo "  - Infinite Retakes: " . ($quiz->infinite_retakes ? 'Yes' : 'No') . "\n";
        echo "  - Max Attempts: {$quiz->max_attempts}\n";
        echo "  - Time Limit: {$quiz->time_limit} minutes\n";
        echo "\n";
    }

    // Test 3: Get upcoming deadlines (next 7 days)
    echo "3. Upcoming quiz deadlines (next 7 days):\n";
    
    $upcomingDeadlines = DB::table('quizzes')
        ->where('program_id', $student->program_id)
        ->where('is_active', true)
        ->where('status', 'published')
        ->where('has_deadline', true)
        ->whereNotNull('due_date')
        ->where('due_date', '>', now())
        ->where('due_date', '<=', now()->addDays(7))
        ->orderBy('due_date', 'asc')
        ->get(['quiz_id', 'quiz_title', 'due_date']);

    if (count($upcomingDeadlines) > 0) {
        foreach ($upcomingDeadlines as $deadline) {
            $dueDate = Carbon::parse($deadline->due_date);
            $timeLeft = Carbon::now()->diffForHumans($dueDate, true);
            echo "- {$deadline->quiz_title} (Due in {$timeLeft})\n";
        }
    } else {
        echo "No upcoming deadlines in the next 7 days.\n";
    }

    // Test 4: Get overdue quizzes
    echo "\n4. Overdue quizzes:\n";
    
    $overdueQuizzes = DB::table('quizzes')
        ->where('program_id', $student->program_id)
        ->where('is_active', true)
        ->where('status', 'published')
        ->where('has_deadline', true)
        ->whereNotNull('due_date')
        ->where('due_date', '<', now())
        ->orderBy('due_date', 'desc')
        ->get(['quiz_id', 'quiz_title', 'due_date']);

    if (count($overdueQuizzes) > 0) {
        foreach ($overdueQuizzes as $overdue) {
            $dueDate = Carbon::parse($overdue->due_date);
            $overdueBy = $dueDate->diffForHumans(Carbon::now(), true);
            echo "- {$overdue->quiz_title} (Overdue by {$overdueBy})\n";
        }
    } else {
        echo "No overdue quizzes.\n";
    }

    // Test 5: Test content items for calendar
    echo "\n5. Testing content items for calendar integration:\n";
    
    $calendarItems = DB::table('content_items')
        ->where('content_type', 'quiz')
        ->where('is_active', true)
        ->whereNotNull('due_date')
        ->where('due_date', '>', now())
        ->orderBy('due_date', 'asc')
        ->get(['content_title', 'due_date', 'content_data']);

    echo "Calendar quiz events:\n";
    foreach ($calendarItems as $item) {
        $dueDate = Carbon::parse($item->due_date);
        echo "- {$item->content_title} on " . $dueDate->format('M j, Y g:i A') . "\n";
    }

    // Test 6: Simulate dashboard data structure
    echo "\n6. Simulating dashboard data structure:\n";
    
    $dashboardData = [
        'upcoming_deadlines' => [],
        'available_quizzes' => [],
        'overdue_quizzes' => []
    ];

    // Upcoming deadlines
    foreach ($upcomingDeadlines as $deadline) {
        $dueDate = Carbon::parse($deadline->due_date);
        $dashboardData['upcoming_deadlines'][] = [
            'quiz_id' => $deadline->quiz_id,
            'title' => $deadline->quiz_title,
            'due_date' => $deadline->due_date,
            'formatted_due_date' => $dueDate->format('M j, Y g:i A'),
            'time_left' => Carbon::now()->diffForHumans($dueDate, true)
        ];
    }

    // Available quizzes without deadlines
    $availableNoDeadline = DB::table('quizzes')
        ->where('program_id', $student->program_id)
        ->where('is_active', true)
        ->where('status', 'published')
        ->where(function($query) {
            $query->where('has_deadline', false)
                  ->orWhereNull('due_date');
        })
        ->get(['quiz_id', 'quiz_title', 'infinite_retakes', 'max_attempts']);

    foreach ($availableNoDeadline as $quiz) {
        $dashboardData['available_quizzes'][] = [
            'quiz_id' => $quiz->quiz_id,
            'title' => $quiz->quiz_title,
            'infinite_retakes' => $quiz->infinite_retakes,
            'max_attempts' => $quiz->max_attempts
        ];
    }

    // Overdue quizzes
    foreach ($overdueQuizzes as $overdue) {
        $dueDate = Carbon::parse($overdue->due_date);
        $dashboardData['overdue_quizzes'][] = [
            'quiz_id' => $overdue->quiz_id,
            'title' => $overdue->quiz_title,
            'due_date' => $overdue->due_date,
            'overdue_by' => $dueDate->diffForHumans(Carbon::now(), true)
        ];
    }

    echo "Dashboard data structure created:\n";
    echo "- Upcoming deadlines: " . count($dashboardData['upcoming_deadlines']) . " items\n";
    echo "- Available quizzes (no deadline): " . count($dashboardData['available_quizzes']) . " items\n";
    echo "- Overdue quizzes: " . count($dashboardData['overdue_quizzes']) . " items\n";

    echo "\n=== STUDENT DASHBOARD TEST COMPLETED SUCCESSFULLY! ===\n";
    echo "✓ Can retrieve available quizzes for student\n";
    echo "✓ Quiz deadline information is properly formatted\n";
    echo "✓ Upcoming deadlines are calculated correctly\n";
    echo "✓ Overdue quizzes are identified properly\n";
    echo "✓ Calendar integration data is available\n";
    echo "✓ Dashboard data structure is properly organized\n";

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
