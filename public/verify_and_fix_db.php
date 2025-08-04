<?php
echo "<h1>🔍 Database Verification & Force Fix</h1>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Database connected</p>";
    
    // Check current state of quiz attempt #3
    echo "<h2>📝 Current Quiz Attempt #3 State</h2>";
    $stmt = $pdo->prepare("SELECT attempt_id, student_id, quiz_id, status, started_at FROM quiz_attempts WHERE attempt_id = ?");
    $stmt->execute([3]);
    $attempt = $stmt->fetch();
    
    if ($attempt) {
        echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 15px 0;'>";
        echo "<p><strong>🆔 Attempt ID:</strong> " . $attempt['attempt_id'] . "</p>";
        echo "<p><strong>👤 Student ID:</strong> <code>" . $attempt['student_id'] . "</code></p>";
        echo "<p><strong>📝 Quiz ID:</strong> " . $attempt['quiz_id'] . "</p>";
        echo "<p><strong>📊 Status:</strong> " . $attempt['status'] . "</p>";
        echo "<p><strong>⏰ Started:</strong> " . $attempt['started_at'] . "</p>";
        echo "</div>";
        
        // Check if it needs fixing
        if ($attempt['student_id'] !== '2025-08-00003') {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24; margin: 15px 0;'>";
            echo "<p><strong>❌ PROBLEM:</strong> Quiz attempt belongs to <code>" . $attempt['student_id'] . "</code> but should belong to <code>2025-08-00003</code></p>";
            echo "</div>";
            
            // Force fix it
            echo "<h3>🔧 Force Fixing...</h3>";
            $updateStmt = $pdo->prepare("UPDATE quiz_attempts SET student_id = ? WHERE attempt_id = ?");
            $result = $updateStmt->execute(['2025-08-00003', 3]);
            
            if ($result) {
                echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; color: #155724; margin: 15px 0;'>";
                echo "<p><strong>✅ FIXED!</strong> Quiz attempt ownership updated to 2025-08-00003</p>";
                echo "</div>";
                
                // Verify the fix
                $verifyStmt = $pdo->prepare("SELECT student_id FROM quiz_attempts WHERE attempt_id = ?");
                $verifyStmt->execute([3]);
                $newOwner = $verifyStmt->fetch();
                
                echo "<p><strong>🔍 Verification:</strong> Now belongs to <code>" . $newOwner['student_id'] . "</code></p>";
            } else {
                echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>";
                echo "<p><strong>❌ ERROR:</strong> Failed to update quiz attempt ownership</p>";
                echo "</div>";
            }
        } else {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; color: #155724; margin: 15px 0;'>";
            echo "<p><strong>✅ CORRECT:</strong> Quiz attempt already belongs to the right student!</p>";
            echo "</div>";
        }
    } else {
        echo "<p>❌ Quiz attempt #3 not found!</p>";
        exit;
    }
    
    // Show the correct student for user_id 15
    echo "<h2>👤 Student for User ID 15</h2>";
    $stmt = $pdo->prepare("SELECT student_id, user_id FROM students WHERE user_id = ?");
    $stmt->execute([15]);
    $student = $stmt->fetch();
    
    if ($student) {
        echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
        echo "<p><strong>Student ID:</strong> <code>" . $student['student_id'] . "</code></p>";
        echo "<p><strong>User ID:</strong> " . $student['user_id'] . "</p>";
        echo "</div>";
    }
    
    // Show all quiz attempts for comparison
    echo "<h2>📊 All Quiz Attempts (for debugging)</h2>";
    $stmt = $pdo->query("SELECT attempt_id, student_id, quiz_id, status FROM quiz_attempts ORDER BY attempt_id LIMIT 10");
    $allAttempts = $stmt->fetchAll();
    
    echo "<table style='border-collapse: collapse; width: 100%; margin: 15px 0;'>";
    echo "<tr style='background: #e9ecef;'>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Attempt ID</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Student ID</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Quiz ID</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Status</th>";
    echo "</tr>";
    
    foreach ($allAttempts as $att) {
        $highlight = ($att['attempt_id'] == 3) ? 'background: #fff3cd;' : '';
        echo "<tr style='$highlight'>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['attempt_id'] . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($att['student_id']) . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['quiz_id'] . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $att['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>";
    echo "<p><strong>❌ Database Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr style='margin: 30px 0;'>";
echo "<h2>🧪 Test After Fix</h2>";
echo "<p>After ensuring the database is correct, test these:</p>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>🎯 Try Quiz Route</a>";
echo "<a href='final_quiz_test.php' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>🔍 Final Test</a>";
?>
