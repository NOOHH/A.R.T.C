<?php
require_once '../config/database.php';
require_once '../config/app.php';

echo "<h1>🗄️ Database Quiz Check</h1>";

try {
    // Connect to database
    $dsn = "mysql:host=127.0.0.1;dbname=a.r.t.c;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Database connected</p>";
    
    // Check quiz attempt #3
    echo "<h2>📝 Quiz Attempt #3</h2>";
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE attempt_id = ?");
    $stmt->execute([3]);
    $attempt = $stmt->fetch();
    
    if ($attempt) {
        echo "<p>✅ <strong>Quiz Attempt Found:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Attempt ID:</strong> " . $attempt['attempt_id'] . "</li>";
        echo "<li><strong>Student ID:</strong> " . $attempt['student_id'] . "</li>";
        echo "<li><strong>Quiz ID:</strong> " . $attempt['quiz_id'] . "</li>";
        echo "<li><strong>Status:</strong> " . $attempt['status'] . "</li>";
        echo "<li><strong>Created:</strong> " . $attempt['created_at'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p>❌ Quiz attempt #3 not found</p>";
        exit;
    }
    
    // Check student with user_id 15
    echo "<h2>👤 Student for User ID 15</h2>";
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([15]);
    $student = $stmt->fetch();
    
    if ($student) {
        echo "<p>✅ <strong>Student Found:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Student ID:</strong> " . $student['student_id'] . "</li>";
        echo "<li><strong>User ID:</strong> " . $student['user_id'] . "</li>";
        echo "<li><strong>First Name:</strong> " . $student['first_name'] . "</li>";
        echo "<li><strong>Last Name:</strong> " . $student['last_name'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p>❌ No student found for user_id 15</p>";
    }
    
    // Check if the quiz attempt should belong to this student
    echo "<h2>🔍 Ownership Analysis</h2>";
    if ($attempt && $student) {
        if ($attempt['student_id'] === $student['student_id']) {
            echo "<p>✅ <strong>MATCH:</strong> Quiz attempt belongs to the correct student</p>";
        } else {
            echo "<p>❌ <strong>MISMATCH:</strong> Quiz attempt belongs to '" . $attempt['student_id'] . "' but current student is '" . $student['student_id'] . "'</p>";
            
            // Fix the ownership
            echo "<h3>🔧 Fixing Ownership</h3>";
            $updateStmt = $pdo->prepare("UPDATE quiz_attempts SET student_id = ? WHERE attempt_id = ?");
            $result = $updateStmt->execute([$student['student_id'], 3]);
            
            if ($result) {
                echo "<p>✅ Quiz attempt ownership updated successfully</p>";
                echo "<p>Changed from: <code>" . $attempt['student_id'] . "</code></p>";
                echo "<p>Changed to: <code>" . $student['student_id'] . "</code></p>";
            } else {
                echo "<p>❌ Failed to update quiz attempt ownership</p>";
            }
        }
    }
    
    // Show other students for comparison
    echo "<h2>👥 All Students</h2>";
    $stmt = $pdo->prepare("SELECT student_id, user_id, first_name, last_name FROM students ORDER BY user_id");
    $stmt->execute();
    $allStudents = $stmt->fetchAll();
    
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f1f1f1;'><th style='border: 1px solid #ddd; padding: 8px;'>Student ID</th><th style='border: 1px solid #ddd; padding: 8px;'>User ID</th><th style='border: 1px solid #ddd; padding: 8px;'>Name</th></tr>";
    foreach ($allStudents as $s) {
        $highlight = ($s['user_id'] == 15) ? 'background: #ffffcc;' : '';
        echo "<tr style='$highlight'>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $s['student_id'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $s['user_id'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $s['first_name'] . " " . $s['last_name'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<br><hr>";
echo "<h2>🔗 Test Links</h2>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Try Quiz Route</a>";
echo "<a href='direct_quiz_test.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Direct Quiz Test</a>";
echo "<a href='complete_quiz_test.php' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Complete Quiz Test</a>";
?>
