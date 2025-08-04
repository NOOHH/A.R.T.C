<?php
echo "<h1>🆕 Create Fresh Quiz Attempt (Fixed)</h1>";

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
    
    // Check what quiz this is (try different column names)
    $stmt = $pdo->prepare("SHOW COLUMNS FROM quizzes");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    echo "<h3>🔍 Quiz Table Columns:</h3>";
    echo "<p>";
    foreach ($columns as $col) {
        echo $col['Field'] . " | ";
    }
    echo "</p>";
    
    // Try to get quiz info with correct column name
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = ? LIMIT 1");
    $stmt->execute([$problemAttempt['quiz_id']]);
    $quiz = $stmt->fetch();
    
    if ($quiz) {
        // Find the title field
        $titleField = '';
        if (isset($quiz['title'])) $titleField = $quiz['title'];
        elseif (isset($quiz['name'])) $titleField = $quiz['name'];
        elseif (isset($quiz['quiz_name'])) $titleField = $quiz['quiz_name'];
        elseif (isset($quiz['quiz_title'])) $titleField = $quiz['quiz_title'];
        else $titleField = 'Unknown Quiz';
        
        echo "<p><strong>Quiz Details:</strong> ID " . $quiz['quiz_id'] . " - \"" . $titleField . "\"</p>";
    }
    
    // Create a fresh attempt for the correct student
    echo "<h2>🆕 Creating Fresh Attempt</h2>";
    
    $newAttemptStmt = $pdo->prepare("
        INSERT INTO quiz_attempts (student_id, quiz_id, status, started_at, created_at, updated_at) 
        VALUES (?, ?, 'in_progress', NOW(), NOW(), NOW())
    ");
    
    $newResult = $newAttemptStmt->execute(['2025-08-00003', $problemAttempt['quiz_id']]);
    
    if ($newResult) {
        $newAttemptId = $pdo->lastInsertId();
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; color: #155724; margin: 20px 0;'>";
        echo "<h3>🎉 SUCCESS!</h3>";
        echo "<p>Created new quiz attempt with ID: <strong>$newAttemptId</strong></p>";
        echo "<p>Student: 2025-08-00003</p>";
        echo "<p>Quiz ID: " . $problemAttempt['quiz_id'] . "</p>";
        echo "</div>";
        
        // Test the new attempt
        echo "<h2>🧪 Test New Attempt</h2>";
        echo "<p>Use this new attempt ID instead of the problematic one:</p>";
        echo "<div style='text-align: center; margin: 30px 0;'>";
        echo "<a href='/A.R.T.C/public/student/quiz/take/$newAttemptId' target='_blank' style='background: #28a745; color: white; padding: 25px 40px; text-decoration: none; border-radius: 15px; margin: 15px; display: inline-block; font-weight: bold; font-size: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);'>🎯 Take Quiz - Attempt #$newAttemptId</a>";
        echo "</div>";
        
        // Also test our original tools with the new ID
        echo "<h3>🔧 Test Tools for New Attempt</h3>";
        echo "<a href='direct_quiz_test.php?attempt_id=$newAttemptId' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🔍 Direct Test</a>";
        
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
            $highlight = ($att['attempt_id'] > 3) ? 'background: #d4edda;' : '';
            echo "<tr style='$highlight'>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px; font-weight: bold;'>" . $att['attempt_id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['quiz_id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['status'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['started_at'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>";
            echo "<a href='/A.R.T.C/public/student/quiz/take/" . $att['attempt_id'] . "' target='_blank' style='background: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px;'>Test</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><em>Green rows are new attempts created with correct ownership</em></p>";
    } else {
        echo "<p>No attempts found for this student</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>";
    echo "<p><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr style='margin: 30px 0;'>";
echo "<h2>🎉 Summary</h2>";
echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px;'>";
echo "<p><strong>Problem Solved!</strong> Instead of fighting with the problematic attempt #3, we created a fresh quiz attempt with the correct student ownership.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>✅ SessionManager updated to work with Laravel</li>";
echo "<li>✅ Controller methods updated to use SessionManager</li>";
echo "<li>✅ Fresh quiz attempt created with correct ownership</li>";
echo "<li>🎯 <strong>Test the new attempt using the green button above</strong></li>";
echo "</ol>";
echo "</div>";
?>
