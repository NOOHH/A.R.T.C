<?php
// Database structure and quiz debugging script
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
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .section { margin-bottom: 30px; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
</style>";

echo "<h1>Quiz Database Structure & Debugging</h1>";

try {
    $db = new PDO("mysql:host=localhost;dbname=artc", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check quizzes table structure
    echo "<div class='section'>";
    echo "<h2>Quizzes Table Structure</h2>";
    
    $stmt = $db->query("DESCRIBE quizzes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Check quiz_questions table structure
    echo "<div class='section'>";
    echo "<h2>Quiz Questions Table Structure</h2>";
    
    $stmt = $db->query("DESCRIBE quiz_questions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Check for existing data
    echo "<div class='section'>";
    echo "<h2>Existing Quiz Data</h2>";
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM quizzes");
    $totalQuizzes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Total quizzes in database: <strong>$totalQuizzes</strong></p>";
    
    if ($totalQuizzes > 0) {
        $stmt = $db->query("SELECT * FROM quizzes ORDER BY quiz_id DESC LIMIT 5");
        $recentQuizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Recent Quizzes (Last 5)</h3>";
        echo "<table>";
        if (!empty($recentQuizzes)) {
            echo "<tr>";
            foreach (array_keys($recentQuizzes[0]) as $key) {
                echo "<th>$key</th>";
            }
            echo "</tr>";
            
            foreach ($recentQuizzes as $quiz) {
                echo "<tr>";
                foreach ($quiz as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
        }
        echo "</table>";
    }
    echo "</div>";
    
    // Check programs, modules, courses
    echo "<div class='section'>";
    echo "<h2>Program/Module/Course Data</h2>";
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM programs");
    $totalPrograms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Total programs: <strong>$totalPrograms</strong></p>";
    
    if ($totalPrograms > 0) {
        $stmt = $db->query("SELECT * FROM programs LIMIT 3");
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>Sample Programs</h4>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th></tr>";
        foreach ($programs as $program) {
            echo "<tr>";
            echo "<td>{$program['program_id']}</td>";
            echo "<td>" . htmlspecialchars($program['program_name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM modules");
    $totalModules = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Total modules: <strong>$totalModules</strong></p>";
    
    if ($totalModules > 0) {
        $stmt = $db->query("SELECT * FROM modules LIMIT 3");
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>Sample Modules</h4>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Program ID</th></tr>";
        foreach ($modules as $module) {
            echo "<tr>";
            echo "<td>{$module['modules_id']}</td>";
            echo "<td>" . htmlspecialchars($module['module_name']) . "</td>";
            echo "<td>{$module['program_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM courses");
    $totalCourses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Total courses: <strong>$totalCourses</strong></p>";
    
    if ($totalCourses > 0) {
        $stmt = $db->query("SELECT * FROM courses LIMIT 3");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>Sample Courses</h4>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Module ID</th></tr>";
        foreach ($courses as $course) {
            echo "<tr>";
            echo "<td>{$course['subject_id']}</td>";
            echo "<td>" . htmlspecialchars($course['course_name']) . "</td>";
            echo "<td>{$course['modules_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // Check validation requirements
    echo "<div class='section'>";
    echo "<h2>Validation Requirements Check</h2>";
    
    echo "<h3>Required Fields for Quiz Creation</h3>";
    echo "<ul>";
    echo "<li><strong>title/quiz_title:</strong> Required string, max 255 characters</li>";
    echo "<li><strong>program_id:</strong> Must exist in programs table</li>";
    echo "<li><strong>module_id:</strong> Must exist in modules table (nullable)</li>";
    echo "<li><strong>course_id:</strong> Must exist in courses table (nullable)</li>";
    echo "<li><strong>questions:</strong> Required array with minimum 1 question</li>";
    echo "</ul>";
    
    echo "<h3>Required Fields for Each Question</h3>";
    echo "<ul>";
    echo "<li><strong>question_text:</strong> Required string</li>";
    echo "<li><strong>question_type:</strong> Required, must be: multiple_choice, true_false, short_answer, or essay</li>";
    echo "<li><strong>options:</strong> Nullable array</li>";
    echo "<li><strong>correct_answer/correct_answers:</strong> Nullable</li>";
    echo "<li><strong>explanation:</strong> Nullable string</li>";
    echo "<li><strong>points:</strong> Nullable numeric</li>";
    echo "</ul>";
    echo "</div>";
    
    // Test data suggestions
    echo "<div class='section'>";
    echo "<h2>Test Data Suggestions</h2>";
    
    if ($totalPrograms > 0 && $totalModules > 0 && $totalCourses > 0) {
        $stmt = $db->query("
            SELECT p.program_id, p.program_name, m.modules_id, m.module_name, c.subject_id, c.course_name
            FROM programs p
            LEFT JOIN modules m ON p.program_id = m.program_id
            LEFT JOIN courses c ON m.modules_id = c.modules_id
            WHERE m.modules_id IS NOT NULL AND c.subject_id IS NOT NULL
            LIMIT 1
        ");
        $testData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($testData) {
            echo "<p>Use this test data for creating quizzes:</p>";
            echo "<pre>";
            echo "program_id: {$testData['program_id']} ({$testData['program_name']})\n";
            echo "module_id: {$testData['modules_id']} ({$testData['module_name']})\n";
            echo "course_id: {$testData['subject_id']} ({$testData['course_name']})\n";
            echo "</pre>";
        } else {
            echo "<p class='warning'>No complete program-module-course chain found. You may need to create proper relationships.</p>";
        }
    } else {
        echo "<p class='error'>Missing basic data. You need to create programs, modules, and courses first.</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>Database Error</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
