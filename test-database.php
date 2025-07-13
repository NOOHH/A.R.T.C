<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $app->loadEnvironmentFrom('.env');
    
    echo "<h1>Database Structure Test</h1>";
    
    // Test database connection
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    echo "<p>✅ Database connection successful</p>";
    
    // Test required tables
    $tables = ['quizzes', 'quiz_questions', 'deadlines', 'student_batches', 'programs'];
    echo "<h2>Required Tables:</h2>";
    
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "<p>✅ Table '$table' exists</p>";
            
            // Show structure for key tables
            if (in_array($table, ['quizzes', 'quiz_questions', 'deadlines'])) {
                $structure = $pdo->query("DESCRIBE $table");
                echo "<details><summary>Show structure</summary><pre>";
                foreach ($structure as $row) {
                    echo $row['Field'] . " (" . $row['Type'] . ")\n";
                }
                echo "</pre></details>";
            }
        } else {
            echo "<p>❌ Table '$table' missing</p>";
        }
    }
    
    // Test models
    echo "<h2>Model Tests:</h2>";
    try {
        $quizCount = \App\Models\Quiz::count();
        echo "<p>✅ Quiz model works - $quizCount quizzes found</p>";
    } catch (Exception $e) {
        echo "<p>❌ Quiz model error: " . $e->getMessage() . "</p>";
    }
    
    try {
        $programCount = \App\Models\Program::count();
        echo "<p>✅ Program model works - $programCount programs found</p>";
    } catch (Exception $e) {
        echo "<p>❌ Program model error: " . $e->getMessage() . "</p>";
    }
    
    try {
        $deadlineCount = \App\Models\Deadline::count();
        echo "<p>✅ Deadline model works - $deadlineCount deadlines found</p>";
    } catch (Exception $e) {
        echo "<p>❌ Deadline model error: " . $e->getMessage() . "</p>";
    }
    
    // Test batch endpoint
    echo "<h2>Endpoint Tests:</h2>";
    echo "<p><a href='/admin/programs/1/batches' target='_blank'>Test batch endpoint</a></p>";
    echo "<p><a href='/admin/quiz-generator' target='_blank'>Test quiz generator</a></p>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>❌ " . $e->getMessage() . "</p>";
}
?>
