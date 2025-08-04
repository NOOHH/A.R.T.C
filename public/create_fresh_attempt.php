<?php
echo "<h1>🆕 Create Fresh Quiz Attempt</h1>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Database connected</p>";
    
    // Check the current problematic attempt
    echo "<h2>🔍 Current Problem</h2>";
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE attempt_id = 3");
    $stmt->execute();
    $problemAttempt = $stmt->fetch();
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
    echo "<p><strong>Attempt ID 3:</strong></p>";
    echo "<p>Student ID: <code>" . $problemAttempt['student_id'] . "</code></p>";
    echo "<p>Quiz ID: " . $problemAttempt['quiz_id'] . "</p>";
    echo "<p>Status: " . $problemAttempt['status'] . "</p>";
    echo "</div>";
    
    // Check what quiz this is
    $stmt = $pdo->prepare("SELECT quiz_id, title FROM quizzes WHERE quiz_id = ?");
    $stmt->execute([$problemAttempt['quiz_id']]);
    $quiz = $stmt->fetch();
    
    echo "<p><strong>Quiz Details:</strong> ID " . $quiz['quiz_id'] . " - \"" . $quiz['title'] . "\"</p>";
    
    // Create a fresh attempt for the correct student
    echo "<h2>🆕 Creating Fresh Attempt</h2>";
    
    $newAttemptStmt = $pdo->prepare("
        INSERT INTO quiz_attempts (student_id, quiz_id, status, started_at, created_at, updated_at) 
        VALUES (?, ?, 'in_progress', NOW(), NOW(), NOW())
    ");
    
    $newResult = $newAttemptStmt->execute(['2025-08-00003', $quiz['quiz_id']]);
    
    if ($newResult) {
        $newAttemptId = $pdo->lastInsertId();
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; color: #155724; margin: 20px 0;'>";
        echo "<h3>🎉 SUCCESS!</h3>";
        echo "<p>Created new quiz attempt with ID: <strong>$newAttemptId</strong></p>";
        echo "<p>Student: 2025-08-00003</p>";
        echo "<p>Quiz: " . $quiz['title'] . " (ID: " . $quiz['quiz_id'] . ")</p>";
        echo "</div>";
        
        // Test the new attempt
        echo "<h2>🧪 Test New Attempt</h2>";
        echo "<p>Use this new attempt ID instead of the problematic one:</p>";
        echo "<a href='/A.R.T.C/public/student/quiz/take/$newAttemptId' target='_blank' style='background: #28a745; color: white; padding: 20px 30px; text-decoration: none; border-radius: 10px; margin: 15px; display: inline-block; font-weight: bold; font-size: 18px;'>🎯 Try Quiz Attempt #$newAttemptId</a>";
        
        // Also provide the original for comparison
        echo "<br><br>";
        echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #dc3545; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>❌ Old Attempt #3 (Should Fail)</a>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; color: #721c24;'>";
        echo "<h3>❌ FAILED!</h3>";
        echo "<p>Could not create new quiz attempt</p>";
        echo "</div>";
    }
    
    // Show all attempts for this student
    echo "<h2>📊 All Attempts for Student 2025-08-00003</h2>";
    $stmt = $pdo->prepare("SELECT attempt_id, quiz_id, status, started_at FROM quiz_attempts WHERE student_id = ? ORDER BY attempt_id DESC");
    $stmt->execute(['2025-08-00003']);
    $studentAttempts = $stmt->fetchAll();
    
    if ($studentAttempts) {
        echo "<table style='border-collapse: collapse; width: 100%; margin: 15px 0;'>";
        echo "<tr style='background: #e9ecef;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Attempt ID</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Quiz ID</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Status</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Started</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Action</th>";
        echo "</tr>";
        
        foreach ($studentAttempts as $att) {
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['attempt_id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['quiz_id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['status'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['started_at'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>";
            echo "<a href='/A.R.T.C/public/student/quiz/take/" . $att['attempt_id'] . "' target='_blank' style='background: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px;'>Test</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No attempts found for this student</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>";
    echo "<p><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr style='margin: 30px 0;'>";
echo "<h2>📝 Summary</h2>";
echo "<p>Instead of fighting with the problematic attempt #3, we created a fresh quiz attempt for the correct student. This should work perfectly with our updated SessionManager and controller fixes.</p>";
?>
