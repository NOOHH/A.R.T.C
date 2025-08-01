<?php
session_start();

// Set up professor session if not already set
if (!isset($_SESSION['professor_id'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['professor_id'] = 8;
    $_SESSION['user_role'] = 'professor';
    $_SESSION['user_type'] = 'professor';
    $_SESSION['user_id'] = 8;
}

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Quiz;
use App\Models\Professor;

echo "<h2>Debug Quiz Modal API - Professor ID: " . session('professor_id') . "</h2>";

// Test if professor exists
$professor = Professor::find(session('professor_id'));
if (!$professor) {
    echo "<p style='color: red;'>❌ Professor not found!</p>";
    exit;
}
echo "<p>✅ Professor found: {$professor->professor_first_name} {$professor->professor_last_name}</p>";

// Test quiz with ID 38
$quizId = 38;
$quiz = Quiz::find($quizId);
if (!$quiz) {
    echo "<p style='color: red;'>❌ Quiz {$quizId} not found!</p>";
    exit;
}
echo "<p>✅ Quiz found: {$quiz->quiz_title}</p>";

// Check ownership
if ($quiz->professor_id !== $professor->professor_id) {
    echo "<p style='color: red;'>❌ Quiz ownership mismatch. Quiz professor_id: {$quiz->professor_id}, Current professor_id: {$professor->professor_id}</p>";
    exit;
}
echo "<p>✅ Quiz ownership verified</p>";

// Test questions loading
$questions = $quiz->questions()->orderBy('question_order')->get();
echo "<p>✅ Quiz has " . $questions->count() . " questions</p>";

// Test the API endpoint manually
echo "<h3>Testing API endpoint response</h3>";
try {
    $url = "http://127.0.0.1:8000/professor/quiz-generator/api/questions/{$quizId}";
    
    // Create a session-aware request
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Cookie: laravel_session=' . session_id(),
                'X-Requested-With: XMLHttpRequest'
            ]
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    echo "<p>✅ API Response received:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ API Error: " . $e->getMessage() . "</p>";
}
?>
