<?php
// Debug script for quiz update functionality
require_once __DIR__ . '/vendor/autoload.php';

// Set error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Quiz;

echo "<style>
    body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
    h1, h2, h3 { color: #333; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; white-space: pre-wrap; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
</style>";

echo "<h1>Quiz Update Debug Tool</h1>";

// Connect to the database
try {
    $db = new PDO("mysql:host=localhost;dbname=artc", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get quiz ID from request or use most recent
    $quizId = $_GET['quiz_id'] ?? null;
    
    if (!$quizId) {
        // Get most recent quiz
        $stmt = $db->query("SELECT * FROM quizzes ORDER BY quiz_id DESC LIMIT 1");
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
        $quizId = $quiz['quiz_id'] ?? null;
    } else {
        // Get specific quiz
        $stmt = $db->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$quiz) {
        echo "<div class='error'>No quiz found with ID $quizId</div>";
        exit;
    }
    
    echo "<h2>Quiz Details</h2>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    foreach ($quiz as $key => $value) {
        echo "<tr><td>$key</td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
    }
    echo "</table>";
    
    // Get quiz questions
    $stmt = $db->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
    $stmt->execute([$quizId]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Quiz Questions</h2>";
    
    if (count($questions) > 0) {
        echo "<table>";
        echo "<tr>
            <th>ID</th>
            <th>Quiz ID</th>
            <th>Question</th>
            <th>Type</th>
            <th>Options</th>
            <th>Correct Answer</th>
        </tr>";
        
        foreach ($questions as $question) {
            echo "<tr>";
            echo "<td>{$question['id']}</td>";
            echo "<td>{$question['quiz_id']}</td>";
            echo "<td>" . htmlspecialchars($question['question_text'] ?? '') . "</td>";
            echo "<td>{$question['question_type']}</td>";
            echo "<td>" . htmlspecialchars($question['options'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($question['correct_answer'] ?? '') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p class='warning'>No questions found for this quiz.</p>";
    }
    
    // Route check
    echo "<h2>Route Check</h2>";
    echo "<div>Admin quiz routes that should be defined:</div>";
    echo "<pre>
// For editing quizzes
Route::get('/admin/quiz-generator/quiz/{quizId}', [App\\Http\\Controllers\\Admin\\QuizGeneratorController::class, 'getQuiz']);
Route::put('/admin/quiz-generator/update-quiz/{quizId}', [App\\Http\\Controllers\\Admin\\QuizGeneratorController::class, 'updateQuiz']);

// For creating quizzes
Route::post('/admin/quiz-generator/save-quiz', [App\\Http\\Controllers\\Admin\\QuizGeneratorController::class, 'saveQuizWithQuestions']);
</pre>";
    
    // Controller method check
    echo "<h2>Controller Method Check</h2>";
    echo "<div>The updateQuiz method should use \$quizId parameter instead of Quiz \$quiz model binding:</div>";
    echo "<pre>
public function updateQuiz(Request \$request, \$quizId)
{
    try {
        // Find the quiz
        \$quiz = Quiz::findOrFail(\$quizId);
        
        // Update logic...
    } catch (\Exception \$e) {
        Log::error('Error updating quiz: ' . \$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update quiz'
        ], 500);
    }
}
</pre>";

    // JavaScript check
    echo "<h2>JavaScript Check</h2>";
    echo "<div>The editQuiz function should correctly set window.currentQuizId:</div>";
    echo "<pre>
function editQuiz(quizId) {
    console.log('Edit quiz:', quizId);
    window.currentQuizId = quizId;  // This is crucial for updateQuiz to work
    
    // Rest of the function...
}
</pre>";

    echo "<div>The saveQuiz function should include quiz_id in the questions:</div>";
    echo "<pre>
// Prepare questions with quiz_id for updates
const preparedQuestions = questions.map(question => {
    if (isEdit) {
        question.quiz_id = quizId;
    }
    return question;
});
</pre>";

    // Manual test form
    echo "<h2>Manual Test</h2>";
    echo "<p>Use this form to test updating a quiz:</p>";
    
    echo "<form id='testForm'>";
    echo "<input type='hidden' name='quiz_id' value='$quizId'>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Quiz Title</label>";
    echo "<input type='text' name='title' value='" . htmlspecialchars($quiz['quiz_title']) . "' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Description</label>";
    echo "<textarea name='description' style='width: 100%; padding: 8px;'>" . htmlspecialchars($quiz['quiz_description'] ?? '') . "</textarea>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Program ID</label>";
    echo "<input type='text' name='program_id' value='" . htmlspecialchars($quiz['program_id'] ?? '') . "' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<button type='button' onclick='testUpdate()' style='padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer;'>Test Update</button>";
    echo "</form>";
    
    echo "<div id='result' style='margin-top: 20px;'></div>";
    
    // JavaScript for testing
    echo "<script>
    function testUpdate() {
        const form = document.getElementById('testForm');
        const quizId = form.querySelector('input[name=\"quiz_id\"]').value;
        
        // Create a simple question array for testing
        const testData = {
            title: form.querySelector('input[name=\"title\"]').value,
            description: form.querySelector('textarea[name=\"description\"]').value,
            program_id: form.querySelector('input[name=\"program_id\"]').value,
            is_draft: true,
            questions: [
                {
                    quiz_id: quizId,
                    question_text: 'Test question',
                    question_type: 'multiple_choice',
                    options: ['Option A', 'Option B', 'Option C', 'Option D'],
                    correct_answers: [0],
                    explanation: 'Test explanation',
                    points: 1
                }
            ]
        };
        
        document.getElementById('result').innerHTML = '<p>Sending update request...</p>';
        
        fetch('/admin/quiz-generator/update-quiz/' + quizId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]') ? document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content') : '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(testData)
        })
        .then(function(response) {
            if (!response.ok) {
                return response.text().then(function(text) {
                    throw new Error('Server returned ' + response.status + ': ' + text);
                });
            }
            return response.json();
        })
        .then(function(data) {
            document.getElementById('result').innerHTML = 
                '<pre style=\"background: #eef; padding: 15px;\">' + JSON.stringify(data, null, 2) + '</pre>';
            
            if (data.success) {
                document.getElementById('result').innerHTML += 
                    '<p class=\"success\">Update successful! Reloading in 3 seconds...</p>';
                setTimeout(function() { location.reload(); }, 3000);
            }
        })
        .catch(function(error) {
            document.getElementById('result').innerHTML = 
                '<p class=\"error\">Error: ' + error.message + '</p>';
        });
    }
    </script>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

// Add CSRF meta tag for testing
echo "<meta name='csrf-token' content='{{ csrf_token() }}' />";
?>
