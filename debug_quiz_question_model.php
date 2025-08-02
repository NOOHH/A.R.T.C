<?php
// Quiz Questions Model Structure Debug
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
</style>";

echo "<h1>Quiz Questions Database Structure</h1>";

try {
    // Connect to the database
    $db = new PDO("mysql:host=localhost;dbname=artc", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get table structure
    $stmt = $db->query("DESCRIBE quiz_questions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Table Structure</h2>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Get sample questions
    $stmt = $db->query("SELECT * FROM quiz_questions ORDER BY id DESC LIMIT 5");
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Sample Questions</h2>";
    
    if (count($questions) > 0) {
        echo "<table>";
        echo "<tr>";
        foreach (array_keys($questions[0]) as $header) {
            echo "<th>" . htmlspecialchars($header) . "</th>";
        }
        echo "</tr>";
        
        foreach ($questions as $question) {
            echo "<tr>";
            foreach ($question as $key => $value) {
                if ($key === 'options' || $key === 'correct_answers') {
                    echo "<td><pre>" . htmlspecialchars(print_r(json_decode($value, true), true)) . "</pre></td>";
                } else {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p class='warning'>No questions found in database.</p>";
    }
    
    // Check for any issues in the data
    $stmt = $db->query("SELECT COUNT(*) as count FROM quiz_questions WHERE quiz_id IS NULL");
    $nullQuizId = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM quiz_questions WHERE question_text IS NULL");
    $nullQuestionText = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<h2>Data Integrity Check</h2>";
    echo "<ul>";
    echo "<li>Questions with NULL quiz_id: <strong>" . ($nullQuizId > 0 ? "<span class='error'>{$nullQuizId}</span>" : "<span class='success'>0</span>") . "</strong></li>";
    echo "<li>Questions with NULL question_text: <strong>" . ($nullQuestionText > 0 ? "<span class='error'>{$nullQuestionText}</span>" : "<span class='success'>0</span>") . "</strong></li>";
    echo "</ul>";
    
    // Show the Quiz model structure
    echo "<h2>Related Tables</h2>";
    
    $stmt = $db->query("DESCRIBE quizzes");
    $quizColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Quizzes Table</h3>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($quizColumns as $column) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Test quiz save data format
    echo "<h2>Test Quiz Save JSON Format</h2>";
    
    $testData = [
        'title' => 'Test Quiz',
        'description' => 'Test Description',
        'program_id' => '41',
        'module_id' => '79',
        'course_id' => '52',
        'admin_id' => 1,
        'questions' => [
            [
                'question_text' => 'Test Question 1',
                'question_type' => 'multiple_choice',
                'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                'correct_answer' => 0,
                'explanation' => 'Test Explanation',
                'points' => 1
            ],
            [
                'question' => 'Test Question 2',
                'question_type' => 'multiple_choice',
                'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                'correct_answers' => [1],
                'explanation' => 'Test Explanation 2',
                'points' => 2
            ]
        ]
    ];
    
    echo "<pre>";
    echo htmlspecialchars(json_encode($testData, JSON_PRETTY_PRINT));
    echo "</pre>";
    
    echo "<p>This is the expected format of quiz data being sent from the JavaScript to the PHP controller.</p>";
    
    // Show the saveQuiz JavaScript function
    echo "<h2>JavaScript saveQuiz Function</h2>";
    echo "<p>This function constructs the data to be sent to the server:</p>";
    
    echo "<pre>";
    echo htmlspecialchars(
'// Prepare questions with quiz_id for updates
const preparedQuestions = questions.map(question => {
    if (isEdit) {
        question.quiz_id = quizId; // Add quiz_id for existing questions
    }
    return question;
});

const quizData = {
    title: title,
    description: document.getElementById("quiz_description").value.trim(),
    program_id: programId,
    module_id: document.getElementById("module_id").value || null,
    course_id: document.getElementById("course_id").value || null,
    admin_id: adminId,
    quiz_id: quizId, // Add quiz_id for updates
    questions: preparedQuestions,
    is_draft: isDraft,
    status: isDraft ? "draft" : "published"
};'
    );
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>
