<?php
echo "<h1>🗄️ Simple Database Quiz Check</h1>";

try {
    // Direct database connection
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc;charset=utf8mb4', 'root', '', [
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
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "<p><strong>Attempt ID:</strong> " . $attempt['attempt_id'] . "</p>";
        echo "<p><strong>Student ID:</strong> " . $attempt['student_id'] . "</p>";
        echo "<p><strong>Quiz ID:</strong> " . $attempt['quiz_id'] . "</p>";
        echo "<p><strong>Status:</strong> " . $attempt['status'] . "</p>";
        echo "<p><strong>Started:</strong> " . $attempt['started_at'] . "</p>";
        echo "</div>";
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
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "<p><strong>Student ID:</strong> " . $student['student_id'] . "</p>";
        echo "<p><strong>User ID:</strong> " . $student['user_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . $student['first_name'] . " " . $student['last_name'] . "</p>";
        echo "</div>";
    } else {
        echo "<p>❌ No student found for user_id 15</p>";
        exit;
    }
    
    // Check ownership match
    echo "<h2>🔍 Ownership Check</h2>";
    if ($attempt['student_id'] === $student['student_id']) {
        echo "<p style='background: #d4edda; padding: 15px; border-radius: 8px; color: #155724;'>✅ <strong>CORRECT:</strong> Quiz attempt belongs to the right student</p>";
    } else {
        echo "<p style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>❌ <strong>WRONG:</strong> Ownership mismatch!</p>";
        echo "<p>Quiz attempt belongs to: <code>" . $attempt['student_id'] . "</code></p>";
        echo "<p>Current student is: <code>" . $student['student_id'] . "</code></p>";
        
        // Fix it
        echo "<h3>🔧 Fixing ownership...</h3>";
        $updateStmt = $pdo->prepare("UPDATE quiz_attempts SET student_id = ? WHERE attempt_id = ?");
        $result = $updateStmt->execute([$student['student_id'], 3]);
        
        if ($result) {
            echo "<p style='background: #d4edda; padding: 15px; border-radius: 8px; color: #155724;'>✅ Fixed! Quiz attempt now belongs to " . $student['student_id'] . "</p>";
        } else {
            echo "<p style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>❌ Failed to fix ownership</p>";
        }
    }
    
    // Check all students
    echo "<h2>👥 All Students (showing user_id mapping)</h2>";
    $stmt = $pdo->query("SELECT student_id, user_id, first_name, last_name FROM students ORDER BY user_id LIMIT 10");
    $allStudents = $stmt->fetchAll();
    
    echo "<table style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #e9ecef;'>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Student ID</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>User ID</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Name</th>";
    echo "</tr>";
    
    foreach ($allStudents as $s) {
        $highlight = ($s['user_id'] == 15) ? 'background: #fff3cd;' : '';
        echo "<tr style='$highlight'>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($s['student_id']) . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $s['user_id'] . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($s['first_name'] . " " . $s['last_name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr style='margin: 30px 0;'>";
echo "<h2>🧪 Next Steps</h2>";
echo "<p>After fixing the database, try these tests:</p>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; margin: 5px; display: inline-block;'>🎯 Try Quiz Route</a>";
echo "<a href='direct_quiz_test.php' target='_blank' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; margin: 5px; display: inline-block;'>🔍 Direct Quiz Test</a>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; margin: 5px; display: inline-block;'>📊 Dashboard</a>";
?>
