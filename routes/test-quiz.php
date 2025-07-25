<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Professor\QuizGeneratorController;

// Test route to check authentication and quiz generation
Route::get('/test-auth', function() {
    echo "<h2>Authentication Test</h2>";
    
    $professorId = session('professor_id');
    echo "<p>Professor ID from session: " . ($professorId ?? 'not set') . "</p>";
    
    if ($professorId) {
        $professor = \App\Models\Professor::find($professorId);
        if ($professor) {
            echo "<p>✓ Professor found: " . $professor->professor_first_name . " " . $professor->professor_last_name . "</p>";
            echo "<p>✓ Professor email: " . $professor->professor_email . "</p>";
            
            // Test program assignment
            $programId = 35;
            $programAssignment = $professor->programs()->where('programs.program_id', $programId)->exists();
            echo "<p>" . ($programAssignment ? "✓" : "✗") . " Professor assigned to program $programId</p>";
            
        } else {
            echo "<p>✗ Professor not found in database</p>";
        }
    } else {
        echo "<p>✗ No professor logged in</p>";
        echo "<p>Available session data: " . json_encode(session()->all()) . "</p>";
    }
    
    echo "<hr>";
    echo "<h3>Test Quiz Generation Form</h3>";
    echo '<form action="/professor/quiz-generator/generate" method="POST" enctype="multipart/form-data">';
    echo csrf_field();
    echo '<input type="hidden" name="program_id" value="35">';
    echo '<input type="hidden" name="module_id" value="56">';
    echo '<input type="hidden" name="course_id" value="30">';
    echo '<input type="hidden" name="content_id" value="27">';
    echo '<input type="hidden" name="num_questions" value="5">';
    echo '<input type="hidden" name="quiz_type" value="multiple_choice">';
    echo '<input type="hidden" name="quiz_title" value="Test Quiz">';
    echo '<input type="hidden" name="instructions" value="Test instructions">';
    echo '<p>Upload a text file:</p>';
    echo '<input type="file" name="document" accept=".txt" required>';
    echo '<br><br>';
    echo '<button type="submit">Test Generate Quiz</button>';
    echo '</form>';
});

// Test route to manually generate a quiz
Route::post('/test-quiz-manual', function() {
    try {
        $professor = \App\Models\Professor::find(8); // Using professor ID from logs
        
        if (!$professor) {
            return response()->json(['error' => 'Professor not found'], 404);
        }
        
        // Create test quiz
        $quiz = \App\Models\Quiz::create([
            'professor_id' => $professor->professor_id,
            'program_id' => 35,
            'module_id' => 56,
            'course_id' => 30,
            'content_id' => 27,
            'quiz_title' => 'Manual Test Quiz',
            'instructions' => 'Test instructions',
            'randomize_order' => false,
            'tags' => ['test'],
            'is_draft' => false,
            'total_questions' => 1,
            'time_limit' => 60,
            'document_path' => 'test-path',
            'is_active' => true,
            'created_at' => now(),
        ]);
        
        // Create test question
        \App\Models\QuizQuestion::create([
            'quiz_id' => $quiz->quiz_id,
            'quiz_title' => $quiz->quiz_title,
            'program_id' => $quiz->program_id,
            'question_text' => 'What is 2 + 2?',
            'question_type' => 'multiple_choice',
            'options' => [
                'A' => '3',
                'B' => '4',
                'C' => '5',
                'D' => '6'
            ],
            'correct_answer' => 'B',
            'points' => 1,
            'is_active' => true,
            'created_by_professor' => $professor->professor_id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Quiz created successfully',
            'quiz_id' => $quiz->quiz_id
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
