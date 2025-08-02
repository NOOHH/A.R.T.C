<?php
// Admin Quiz Edit Modal Test Script
require_once __DIR__ . '/vendor/autoload.php';

// Set error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    .code-block { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; margin: 10px 0; }
</style>";

echo "<h1>Admin Quiz Edit Modal Verification</h1>";

// Check database for quizzes
try {
    $db = new PDO("mysql:host=localhost;dbname=artc", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get a sample quiz to test with
    $stmt = $db->query("SELECT * FROM quizzes ORDER BY quiz_id DESC LIMIT 1");
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($quiz) {
        echo "<h2>Testing Edit Modal for Quiz #{$quiz['quiz_id']}</h2>";
        echo "<p>Quiz Title: <strong>{$quiz['quiz_title']}</strong></p>";
        
        // Route check
        echo "<h3>1. Route Check</h3>";
        echo "<p>The edit modal requires a route to fetch quiz data:</p>";
        echo "<div class='code-block'>GET /admin/quiz-generator/quiz/{$quiz['quiz_id']}</div>";
        
        echo "<p>This route should be defined in <code>routes/web.php</code> as:</p>";
        echo "<div class='code-block'>Route::get('quiz/{quizId}', [QuizGeneratorController::class, 'getQuiz']);</div>";
        
        // Controller method
        echo "<h3>2. Controller Method Check</h3>";
        echo "<p>The <code>getQuiz</code> method in <code>Admin\QuizGeneratorController</code> should be defined as:</p>";
        
        echo "<div class='code-block'>
public function getQuiz($quizId)
{
    try {
        \$quiz = Quiz::with(['questions.options', 'contentItem.program', 'contentItem.module', 'contentItem.course'])
            ->findOrFail($quizId);
        
        return response()->json([
            'success' => true,
            'quiz' => \$quiz
        ]);
    } catch (\Exception \$e) {
        Log::error('Error fetching quiz: ' . \$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch quiz'
        ], 500);
    }
}
</div>";
        
        // JavaScript function
        echo "<h3>3. JavaScript Edit Function Check</h3>";
        echo "<p>The <code>editQuiz</code> function in <code>index.blade.php</code> should be defined as:</p>";
        
        echo "<div class='code-block'>
function editQuiz(quizId) {
    console.log('Edit quiz:', quizId);
    
    // Clear the form
    document.getElementById('edit-form').reset();
    
    // Get the quiz data
    fetch(`/admin/quiz-generator/quiz/${quizId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const quiz = data.quiz;
                
                // Populate the form
                document.getElementById('edit-quiz-id').value = quiz.quiz_id;
                document.getElementById('edit-quiz-title').value = quiz.quiz_title;
                // Populate other fields as needed...
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('editQuizModal'));
                modal.show();
            } else {
                showAlert('danger', data.message || 'Failed to fetch quiz data');
            }
        })
        .catch(error => {
            console.error('Error fetching quiz data:', error);
            showAlert('danger', 'An error occurred while fetching quiz data');
        });
}
</div>";

        // Manual test
        echo "<h3>4. Manual Test</h3>";
        echo "<p>This script allows you to manually test the quiz edit endpoint:</p>";
        
        echo "<button onclick=\"testEditQuiz({$quiz['quiz_id']})\">Test Edit Quiz Endpoint</button>";
        echo "<div id='result' style='margin-top: 20px;'></div>";
        echo "<pre id='json-result' style='display:none; max-height: 400px; overflow-y: auto;'></pre>";
        
        echo "<script>
            function testEditQuiz(quizId) {
                document.getElementById('result').innerHTML = '<p>Sending request...</p>';
                document.getElementById('json-result').style.display = 'none';
                
                fetch('/admin/quiz-generator/quiz/' + quizId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('result').innerHTML = 
                                '<p class=\"success\">Successfully fetched quiz data!</p>';
                            
                            // Display the JSON data
                            document.getElementById('json-result').textContent = 
                                JSON.stringify(data, null, 2);
                            document.getElementById('json-result').style.display = 'block';
                        } else {
                            document.getElementById('result').innerHTML = 
                                '<p class=\"error\">' + (data.message || 'Failed to fetch quiz data') + '</p>';
                        }
                    })
                    .catch(error => {
                        document.getElementById('result').innerHTML = 
                            '<p class=\"error\">Error: ' + error.message + '</p>';
                    });
            }
        </script>";
        
        // Modal requirements
        echo "<h3>5. Modal Structure Check</h3>";
        echo "<p>Make sure the edit modal has the correct structure in <code>index.blade.php</code>:</p>";
        
        echo "<div class='code-block'>
&lt;div class=\"modal fade\" id=\"editQuizModal\" tabindex=\"-1\" aria-labelledby=\"editQuizModalLabel\" aria-hidden=\"true\"&gt;
    &lt;div class=\"modal-dialog modal-lg\"&gt;
        &lt;div class=\"modal-content\"&gt;
            &lt;div class=\"modal-header\"&gt;
                &lt;h5 class=\"modal-title\" id=\"editQuizModalLabel\"&gt;Edit Quiz&lt;/h5&gt;
                &lt;button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"&gt;&lt;/button&gt;
            &lt;/div&gt;
            &lt;div class=\"modal-body\"&gt;
                &lt;form id=\"edit-form\"&gt;
                    &lt;input type=\"hidden\" id=\"edit-quiz-id\" name=\"quiz_id\"&gt;
                    
                    &lt;!-- Form fields --&gt;
                    &lt;div class=\"mb-3\"&gt;
                        &lt;label for=\"edit-quiz-title\" class=\"form-label\"&gt;Quiz Title&lt;/label&gt;
                        &lt;input type=\"text\" class=\"form-control\" id=\"edit-quiz-title\" name=\"quiz_title\" required&gt;
                    &lt;/div&gt;
                    
                    &lt;!-- Other form fields --&gt;
                    
                &lt;/form&gt;
            &lt;/div&gt;
            &lt;div class=\"modal-footer\"&gt;
                &lt;button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\"&gt;Cancel&lt;/button&gt;
                &lt;button type=\"button\" class=\"btn btn-primary\" onclick=\"updateQuiz()\"&gt;Save Changes&lt;/button&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;
</div>";
    } else {
        echo "<p class='error'>No quizzes found in database to test with.</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Database Error: " . $e->getMessage() . "</p>";
}
?>
