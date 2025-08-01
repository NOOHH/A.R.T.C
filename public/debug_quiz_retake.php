<?php
// debug_quiz_retake.php
// Debug script to verify the retake quiz functionality

// Display PHP version and server info
echo "<h1>Quiz Retake Debug</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<hr>";

// Import Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Verify route existence
echo "<h2>Route Verification</h2>";
$router = $app->make('router');
$routes = $router->getRoutes();

// Check for quiz start routes
echo "<h3>Quiz Start Routes:</h3>";
echo "<ul>";
foreach ($routes as $route) {
    if (strpos($route->uri, 'quiz') !== false && strpos($route->uri, 'start') !== false) {
        $methods = implode(', ', $route->methods);
        echo "<li>[$methods] {$route->uri} → {$route->action['controller']} (name: {$route->action['as']})</li>";
    }
}
echo "</ul>";

// Display test form for POST requests
echo "<h2>Test Quiz Start Form</h2>";
echo "<form action='/student/quiz/1/start' method='POST'>";
echo "<input type='hidden' name='_token' value='" . csrf_token() . "' />";
echo "<input type='submit' value='Test POST to /student/quiz/1/start' />";
echo "</form>";

// Display test link for GET requests
echo "<h2>Test Links</h2>";
echo "<a href='/student/quiz/1/start'>Test GET to /student/quiz/1/start</a>";

// Check quiz attempts in database
echo "<h2>Recent Quiz Attempts</h2>";

try {
    $attempts = \Illuminate\Support\Facades\DB::table('quiz_attempts')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
        
    if (count($attempts) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Quiz ID</th><th>Student ID</th><th>Status</th><th>Created</th><th>Updated</th></tr>";
        
        foreach ($attempts as $attempt) {
            echo "<tr>";
            echo "<td>{$attempt->attempt_id}</td>";
            echo "<td>{$attempt->quiz_id}</td>";
            echo "<td>{$attempt->student_id}</td>";
            echo "<td>{$attempt->status}</td>";
            echo "<td>{$attempt->created_at}</td>";
            echo "<td>{$attempt->updated_at}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No quiz attempts found.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error accessing database: " . $e->getMessage() . "</p>";
}

// JavaScript to test the retakeQuiz function
echo "<h2>Test JavaScript Retake Function</h2>";
echo "<button id='testRetakeBtn' onclick='testRetake(1)'>Test retakeQuiz(1) Function</button>";

echo "<script>
function testRetake(quizId) {
    // Create a form to submit via POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/student/quiz/${quizId}/start`;
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '" . csrf_token() . "';
    form.appendChild(csrfInput);
    
    // Add to document and submit
    document.body.appendChild(form);
    form.submit();
}
</script>";

// Display server logs if available
echo "<h2>Recent Log Entries</h2>";
try {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $logEntries = array_filter(array_slice(explode('[', $logs), -20));
        
        echo "<pre>";
        foreach ($logEntries as $entry) {
            echo "[" . htmlspecialchars($entry) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<p>Log file not found at: $logFile</p>";
    }
} catch (Exception $e) {
    echo "<p>Error accessing logs: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><small>Generated at: " . date('Y-m-d H:i:s') . "</small></p>";
