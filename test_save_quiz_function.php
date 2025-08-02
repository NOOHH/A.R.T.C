<?php
// Test script for saveQuizWithQuestions
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
    .test-button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; }
    .test-button.secondary { background: #2196F3; }
    .test-button.danger { background: #f44336; }
    .results { margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px; }
</style>";

echo "<h1>Test saveQuizWithQuestions Function</h1>";

// Define a test quiz
$testQuiz = [
    'title' => 'Debug Test Quiz ' . date('Y-m-d H:i:s'),
    'description' => 'Test Description',
    'program_id' => '41', // Use a real program ID from your database
    'module_id' => '79', // Use a real module ID from your database
    'course_id' => '52', // Use a real course ID from your database
    'admin_id' => 1, // Use the admin ID
    'time_limit' => 60,
    'max_attempts' => 1,
    'infinite_retakes' => false,
    'has_deadline' => false,
    'is_draft' => true,
    'status' => 'draft',
    'questions' => [
        [
            'question_text' => 'Debug Test Question 1',
            'question_type' => 'multiple_choice',
            'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
            'correct_answers' => [0],
            'explanation' => 'Test Explanation 1',
            'points' => 1
        ],
        [
            'question' => 'Debug Test Question 2 (using question instead of question_text)',
            'question_type' => 'multiple_choice',
            'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
            'correct_answer' => 1, // Using correct_answer instead of correct_answers
            'explanation' => 'Test Explanation 2',
            'points' => 2
        ]
    ],
    '_token' => csrf_token()
];

// Function to test saveQuizWithQuestions
function testSaveQuiz($testData) {
    // Create cURL request
    $ch = curl_init();
    
    // Set URL and other options
    curl_setopt($ch, CURLOPT_URL, url('/admin/quiz-generator/save-quiz'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-CSRF-TOKEN: ' . csrf_token(),
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    // Execute request
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $error = curl_error($ch);
    
    // Close curl handle
    curl_close($ch);
    
    // Return the response
    return [
        'response' => $response,
        'status_code' => $info['http_code'],
        'error' => $error
    ];
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'test') {
        echo "<div class='results'>";
        echo "<h2>Test Results</h2>";
        
        // Run the test
        $result = testSaveQuiz($testQuiz);
        
        echo "<h3>Status Code: " . $result['status_code'] . "</h3>";
        
        if ($result['error']) {
            echo "<p class='error'>Error: " . htmlspecialchars($result['error']) . "</p>";
        }
        
        echo "<h3>Response:</h3>";
        echo "<pre>";
        
        // Attempt to pretty-print JSON
        $json = json_decode($result['response']);
        if ($json !== null) {
            echo htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT));
        } else {
            echo htmlspecialchars($result['response']);
        }
        
        echo "</pre>";
        echo "</div>";
    }
}

// Display test form
echo "<h2>Test Form</h2>";
echo "<p>Use this form to test the saveQuizWithQuestions function with predefined test data.</p>";

echo "<form method='post'>";
echo "<input type='hidden' name='action' value='test'>";
echo "<button type='submit' class='test-button'>Run Test</button>";
echo "</form>";

// Display the test data
echo "<h2>Test Data</h2>";
echo "<pre>";
echo htmlspecialchars(json_encode($testQuiz, JSON_PRETTY_PRINT));
echo "</pre>";

// Display information about saveQuizWithQuestions
echo "<h2>Controller Method</h2>";
echo "<p>The test will call the <code>saveQuizWithQuestions</code> method in <code>App\\Http\\Controllers\\Admin\\QuizGeneratorController</code>.</p>";

echo "<p>Key fixes implemented:</p>";
echo "<ol>";
echo "<li>Added support for both <code>question_text</code> and <code>question</code> field names</li>";
echo "<li>Added support for both <code>correct_answer</code> and <code>correct_answers</code> formats</li>";
echo "<li>Made module_id and course_id validation nullable</li>";
echo "<li>Enhanced error logging and messages</li>";
echo "</ol>";
?>
