<?php
// Direct quiz test - bypass all Laravel session issues
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>🧪 Direct Quiz Test</h1>";

try {
    // Step 1: Get the student and attempt
    $student = \App\Models\Student::where('user_id', 15)->first();
    $attempt = \App\Models\QuizAttempt::with(['quiz.questions'])->find(3);
    
    if (!$student) {
        echo "<p style='color: red;'>❌ Student with user_id 15 not found</p>";
        
        // Find any student and update the attempt
        $anyStudent = \App\Models\Student::first();
        if ($anyStudent) {
            $student = $anyStudent;
            echo "<p>✅ Using student: {$student->student_fname} {$student->student_lname} (ID: {$student->student_id})</p>";
            
            if ($attempt) {
                $attempt->update(['student_id' => $student->student_id]);
                echo "<p>✅ Updated attempt to belong to this student</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ No students found in database</p>";
            exit;
        }
    } else {
        echo "<p>✅ Student found: {$student->student_fname} {$student->student_lname} (ID: {$student->student_id})</p>";
    }
    
    if (!$attempt) {
        echo "<p style='color: red;'>❌ Quiz attempt 3 not found</p>";
        exit;
    }
    
    $quiz = $attempt->quiz;
    
    echo "<p>✅ Quiz attempt found: ID {$attempt->attempt_id}</p>";
    echo "<p>✅ Quiz: {$quiz->quiz_title}</p>";
    echo "<p>✅ Status: {$attempt->status}</p>";
    echo "<p>✅ Questions: {$quiz->questions->count()}</p>";
    
    // Step 2: Create a simplified quiz interface
    echo "<div style='border: 2px solid #007cba; padding: 20px; margin: 20px 0; border-radius: 10px;'>";
    echo "<h2>📝 {$quiz->quiz_title}</h2>";
    echo "<p><strong>Student:</strong> {$student->student_fname} {$student->student_lname}</p>";
    echo "<p><strong>Status:</strong> {$attempt->status}</p>";
    echo "<p><strong>Questions:</strong> {$quiz->questions->count()}</p>";
    
    if ($attempt->status === 'in_progress') {
        echo "<form id='quizForm'>";
        echo "<input type='hidden' name='_token' value='" . csrf_token() . "'>";
        echo "<input type='hidden' name='attempt_id' value='{$attempt->attempt_id}'>";
        
        foreach ($quiz->questions as $index => $question) {
            echo "<div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;'>";
            echo "<h4>Question " . ($index + 1) . "</h4>";
            echo "<p><strong>{$question->question_text}</strong></p>";
            
            if ($question->question_type === 'multiple_choice' && $question->options) {
                foreach ($question->options as $key => $option) {
                    $letter = is_numeric($key) ? chr(65 + $key) : $key;
                    echo "<div>";
                    echo "<input type='radio' name='answers[{$question->id}]' value='{$key}' id='q{$question->id}_{$key}'>";
                    echo "<label for='q{$question->id}_{$key}'> {$letter}. {$option}</label>";
                    echo "</div>";
                }
            } elseif ($question->question_type === 'true_false') {
                echo "<div>";
                echo "<input type='radio' name='answers[{$question->id}]' value='True' id='q{$question->id}_true'>";
                echo "<label for='q{$question->id}_true'> True</label>";
                echo "</div>";
                echo "<div>";
                echo "<input type='radio' name='answers[{$question->id}]' value='False' id='q{$question->id}_false'>";
                echo "<label for='q{$question->id}_false'> False</label>";
                echo "</div>";
            } else {
                echo "<textarea name='answers[{$question->id}]' style='width: 100%; height: 100px;' placeholder='Enter your answer here...'></textarea>";
            }
            echo "</div>";
        }
        
        echo "<button type='button' onclick='submitQuiz()' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;'>Submit Quiz</button>";
        echo "</form>";
        
        echo "<script>
        function submitQuiz() {
            const form = document.getElementById('quizForm');
            const formData = new FormData(form);
            const answers = {};
            
            // Collect answers
            const inputs = form.querySelectorAll('input[name^=\"answers\"], textarea[name^=\"answers\"]');
            inputs.forEach(input => {
                if (input.type === 'radio' && input.checked) {
                    const match = input.name.match(/answers\\[(\\d+)\\]/);
                    if (match) answers[match[1]] = input.value;
                } else if (input.type !== 'radio' && input.value.trim()) {
                    const match = input.name.match(/answers\\[(\\d+)\\]/);
                    if (match) answers[match[1]] = input.value;
                }
            });
            
            console.log('Submitting answers:', answers);
            
            fetch('/student/quiz/submit/{$attempt->attempt_id}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name=\"_token\"]').value
                },
                body: JSON.stringify({ answers: answers })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    alert('Quiz submitted successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error: ' + error.message);
            });
        }
        </script>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ Quiz is not in progress (Status: {$attempt->status})</p>";
        
        if ($attempt->status === 'completed') {
            echo "<p>✅ Quiz completed!</p>";
            echo "<p>Score: {$attempt->score} / {$attempt->total_questions}</p>";
            echo "<p>Correct: {$attempt->correct_answers}</p>";
        }
    }
    
    echo "</div>";
    
    // Step 3: Test buttons
    echo "<h2>🔧 Test Actions</h2>";
    echo "<a href='/student/quiz/take/3' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Take Quiz (Laravel Route)</a>";
    echo "<a href='set_test_session.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Setup Session</a>";
    echo "<a href='debug_quiz_session.php' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Debug Session</a>";
    
    // Step 4: Reset attempt if needed
    echo "<br><br>";
    echo "<button onclick='resetAttempt()' style='background: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>Reset Attempt to In Progress</button>";
    
    echo "<script>
    function resetAttempt() {
        if (confirm('Reset this quiz attempt to in_progress status?')) {
            fetch('/debug-reset-attempt/{$attempt->attempt_id}')
            .then(response => response.text())
            .then(data => {
                alert('Attempt reset! Refresh page to see changes.');
                window.location.reload();
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
    }
    </script>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
