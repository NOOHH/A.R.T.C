<?php

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;

Route::get('/test-quiz-flow', function() {
    echo "<h1>Complete Quiz Flow Test</h1>";
    
    try {
        // 1. Setup test student session
        echo "<h2>1. Setting up test session</h2>";
        session([
            'user_id' => 1,
            'user_role' => 'student',
            'logged_in' => true
        ]);
        
        $student = Student::where('user_id', 1)->first();
        if (!$student) {
            $student = Student::first();
            if ($student) {
                session(['user_id' => $student->user_id]);
                echo "<p>✅ Using existing student: ID {$student->student_id}</p>";
            } else {
                echo "<p style='color: red;'>❌ No students found in database</p>";
                return;
            }
        } else {
            echo "<p>✅ Student found: ID {$student->student_id}</p>";
        }
        
        // 2. Check for existing quiz
        echo "<h2>2. Finding test quiz</h2>";
        $quiz = Quiz::with('questions')->where('is_active', true)->first();
        if (!$quiz) {
            $quiz = Quiz::with('questions')->first();
        }
        
        if (!$quiz) {
            echo "<p style='color: red;'>❌ No quizzes found. Creating test quiz...</p>";
            
            // Create a test quiz
            $quiz = Quiz::create([
                'quiz_title' => 'Test Quiz for Flow',
                'program_id' => 1,
                'module_id' => 1,
                'course_id' => 1,
                'instructions' => 'This is a test quiz',
                'time_limit' => 60,
                'max_attempts' => 3,
                'is_active' => true,
                'status' => 'published',
                'total_questions' => 2
            ]);
            
            // Create test questions
            QuizQuestion::create([
                'quiz_id' => $quiz->quiz_id,
                'question_text' => 'What is 2 + 2?',
                'question_type' => 'multiple_choice',
                'options' => ['A' => '3', 'B' => '4', 'C' => '5', 'D' => '6'],
                'correct_answer' => '1',
                'points' => 1,
                'is_active' => true
            ]);
            
            QuizQuestion::create([
                'quiz_id' => $quiz->quiz_id,
                'question_text' => 'Is Laravel a PHP framework?',
                'question_type' => 'true_false',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1,
                'is_active' => true
            ]);
            
            $quiz->load('questions');
            echo "<p>✅ Created test quiz: ID {$quiz->quiz_id}</p>";
        } else {
            echo "<p>✅ Found existing quiz: ID {$quiz->quiz_id} - {$quiz->quiz_title}</p>";
        }
        
        echo "<p>Quiz has {$quiz->questions->count()} questions</p>";
        
        // 3. Test quiz start
        echo "<h2>3. Testing Quiz Start</h2>";
        
        // Check for existing active attempt
        $activeAttempt = QuizAttempt::where('quiz_id', $quiz->quiz_id)
            ->where('student_id', $student->student_id)
            ->where('status', 'in_progress')
            ->first();
            
        if ($activeAttempt) {
            echo "<p>✅ Found existing active attempt: {$activeAttempt->attempt_id}</p>";
        } else {
            // Create new attempt
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->quiz_id,
                'student_id' => $student->student_id,
                'started_at' => now(),
                'status' => 'in_progress',
                'total_questions' => $quiz->questions->count(),
                'answers' => []
            ]);
            echo "<p>✅ Created new quiz attempt: {$attempt->attempt_id}</p>";
            $activeAttempt = $attempt;
        }
        
        // 4. Test route generation
        echo "<h2>4. Testing Route Generation</h2>";
        
        try {
            $startUrl = route('student.quiz.start', ['quizId' => $quiz->quiz_id]);
            echo "<p>✅ Start route: <a href='{$startUrl}'>{$startUrl}</a></p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Start route error: {$e->getMessage()}</p>";
        }
        
        try {
            $takeUrl = route('student.quiz.take', ['attemptId' => $activeAttempt->attempt_id]);
            echo "<p>✅ Take route: <a href='{$takeUrl}'>{$takeUrl}</a></p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Take route error: {$e->getMessage()}</p>";
        }
        
        try {
            $submitUrl = route('student.quiz.submit.attempt', ['attemptId' => $activeAttempt->attempt_id]);
            echo "<p>✅ Submit route: {$submitUrl}</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Submit route error: {$e->getMessage()}</p>";
        }
        
        try {
            $resultsUrl = route('student.quiz.results', ['attemptId' => $activeAttempt->attempt_id]);
            echo "<p>✅ Results route: {$resultsUrl}</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Results route error: {$e->getMessage()}</p>";
        }
        
        // 5. Test controller methods
        echo "<h2>5. Testing Controller Methods</h2>";
        
        $controller = new \App\Http\Controllers\StudentDashboardController();
        
        // Test startQuiz
        echo "<h3>Testing startQuiz method</h3>";
        try {
            $response = $controller->startQuiz($quiz->quiz_id);
            $responseData = $response->getData(true);
            echo "<p>✅ startQuiz response: " . json_encode($responseData) . "</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ startQuiz error: {$e->getMessage()}</p>";
        }
        
        // Test takeQuiz
        echo "<h3>Testing takeQuiz method</h3>";
        try {
            $response = $controller->takeQuiz($activeAttempt->attempt_id);
            echo "<p>✅ takeQuiz method executed successfully</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ takeQuiz error: {$e->getMessage()}</p>";
        }
        
        // 6. Test answer submission
        echo "<h2>6. Testing Answer Submission</h2>";
        
        $testAnswers = [];
        foreach ($quiz->questions as $index => $question) {
            if ($question->question_type === 'multiple_choice') {
                $testAnswers[$question->id] = '1'; // B option (correct for "What is 2+2?")
            } else {
                $testAnswers[$question->id] = 'True'; // Correct for Laravel question
            }
        }
        
        $request = new Request();
        $request->merge(['answers' => $testAnswers]);
        
        try {
            $response = $controller->submitQuizAttempt($request, $activeAttempt->attempt_id);
            $responseData = $response->getData(true);
            echo "<p>✅ submitQuizAttempt response: " . json_encode($responseData) . "</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ submitQuizAttempt error: {$e->getMessage()}</p>";
        }
        
        // 7. Test links
        echo "<h2>7. Test Links</h2>";
        
        echo "<div style='margin: 20px 0;'>";
        echo "<h3>Direct Test Links:</h3>";
        echo "<p><a href='{$takeUrl}' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🎯 Take Quiz</a></p>";
        echo "<p><a href='" . route('student.dashboard') . "' target='_blank' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🏠 Student Dashboard</a></p>";
        echo "</div>";
        
        // 8. Debugging info
        echo "<h2>8. Debugging Information</h2>";
        echo "<h3>Session Data:</h3>";
        echo "<pre>" . json_encode(session()->all(), JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Active Attempt Data:</h3>";
        echo "<pre>" . json_encode($activeAttempt->toArray(), JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Quiz Questions:</h3>";
        foreach ($quiz->questions as $question) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 5px 0;'>";
            echo "<strong>Q{$question->id}:</strong> {$question->question_text}<br>";
            echo "<strong>Type:</strong> {$question->question_type}<br>";
            echo "<strong>Options:</strong> " . json_encode($question->options) . "<br>";
            echo "<strong>Correct Answer:</strong> {$question->correct_answer}";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Critical Error: {$e->getMessage()}</p>";
        echo "<pre>{$e->getTraceAsString()}</pre>";
    }
    
    echo "<h2>Next Steps</h2>";
    echo "<ol>";
    echo "<li>Click 'Take Quiz' to test the quiz interface</li>";
    echo "<li>Submit answers and check if they're processed correctly</li>";
    echo "<li>Verify redirects work properly</li>";
    echo "<li>Check if results page displays correctly</li>";
    echo "</ol>";
});

// Additional test route for direct quiz submission
Route::post('/test-quiz-submit/{attemptId}', function(Request $request, $attemptId) {
    $controller = new \App\Http\Controllers\StudentDashboardController();
    return $controller->submitQuizAttempt($request, $attemptId);
})->name('test.quiz.submit');
