<?php
echo "<h1>🎯 Create Working Quiz Attempt</h1>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Database connected</p>";
    
    // Get the existing attempt details
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE attempt_id = 3");
    $stmt->execute();
    $existing = $stmt->fetch();
    
    // Get the quiz details to know total questions
    $stmt = $pdo->prepare("SELECT quiz_id, COUNT(*) as total_questions FROM quiz_questions WHERE quiz_id = ? GROUP BY quiz_id");
    $stmt->execute([$existing['quiz_id']]);
    $quizInfo = $stmt->fetch();
    
    $totalQuestions = $quizInfo['total_questions'] ?? 1;
    
    echo "<h2>📝 Quiz Information</h2>";
    echo "<p><strong>Quiz ID:</strong> " . $existing['quiz_id'] . "</p>";
    echo "<p><strong>Total Questions:</strong> $totalQuestions</p>";
    
    // Create the new attempt with all required fields
    echo "<h2>🆕 Creating Complete Attempt</h2>";
    
    $insertStmt = $pdo->prepare("
        INSERT INTO quiz_attempts 
        (quiz_id, student_id, answers, total_questions, correct_answers, status, started_at, created_at, updated_at) 
        VALUES (?, ?, ?, ?, 0, 'in_progress', NOW(), NOW(), NOW())
    ");
    
    $result = $insertStmt->execute([
        $existing['quiz_id'],
        '2025-08-00003',
        '[]', // Valid empty JSON array
        $totalQuestions
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "<div style='background: #d4edda; padding: 25px; border-radius: 12px; color: #155724; margin: 25px 0; text-align: center;'>";
        echo "<h2>🎉 SUCCESS!</h2>";
        echo "<p style='font-size: 18px; margin: 15px 0;'>Created new quiz attempt with ID: <strong style='font-size: 24px; color: #0d5aa7;'>$newId</strong></p>";
        echo "<p><strong>Student:</strong> 2025-08-00003 ✅</p>";
        echo "<p><strong>Quiz ID:</strong> " . $existing['quiz_id'] . "</p>";
        echo "<p><strong>Total Questions:</strong> $totalQuestions</p>";
        echo "<p><strong>Status:</strong> in_progress</p>";
        echo "</div>";
        
        // The moment of truth - test the quiz!
        echo "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 20px; text-align: center; margin: 40px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.3);'>";
        echo "<h2 style='margin: 0 0 20px 0; font-size: 28px;'>🎯 THE MOMENT OF TRUTH</h2>";
        echo "<p style='font-size: 18px; margin-bottom: 30px;'>Click the button below to test if our quiz fixes work!</p>";
        echo "<a href='/A.R.T.C/public/student/quiz/take/$newId' target='_blank' style='background: linear-gradient(45deg, #ff6b6b, #4ecdc4); color: white; padding: 20px 40px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.3); text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s;'>🚀 Take Quiz - Attempt #$newId</a>";
        echo "</div>";
        
        // Verification section
        echo "<h2>🔍 Verification</h2>";
        
        // Check what we created
        $verifyStmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE attempt_id = ?");
        $verifyStmt->execute([$newId]);
        $newAttempt = $verifyStmt->fetch();
        
        echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
        echo "<h4>📊 New Attempt Details:</h4>";
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        foreach ($newAttempt as $key => $value) {
            $highlight = ($key === 'student_id') ? 'background: #4caf50; color: white;' : '';
            echo "<tr>";
            echo "<td style='border: 1px solid #ddd; padding: 8px; font-weight: bold; $highlight'>$key</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px; $highlight'>" . ($value ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        
        // Show all our test tools
        echo "<h2>🛠️ Additional Test Tools</h2>";
        echo "<div style='display: flex; gap: 15px; flex-wrap: wrap; margin: 20px 0;'>";
        echo "<a href='direct_quiz_test.php' target='_blank' style='background: #2196f3; color: white; padding: 15px 20px; text-decoration: none; border-radius: 8px; flex: 1; text-align: center; min-width: 200px;'>🔍 Direct Quiz Test</a>";
        echo "<a href='complete_quiz_test.php' target='_blank' style='background: #9c27b0; color: white; padding: 15px 20px; text-decoration: none; border-radius: 8px; flex: 1; text-align: center; min-width: 200px;'>📋 Complete Test</a>";
        echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #4caf50; color: white; padding: 15px 20px; text-decoration: none; border-radius: 8px; flex: 1; text-align: center; min-width: 200px;'>📊 Dashboard</a>";
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; color: #721c24;'>";
        echo "<h3>❌ FAILED!</h3>";
        echo "<p>Could not create new quiz attempt</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; color: #721c24;'>";
    echo "<p><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr style='margin: 40px 0;'>";
echo "<div style='background: #fff3cd; padding: 25px; border-radius: 12px; border: 2px solid #ffeaa7;'>";
echo "<h2>📋 Complete Fix Summary</h2>";
echo "<h3>✅ What We Fixed:</h3>";
echo "<ol style='font-size: 16px; line-height: 1.6;'>";
echo "<li><strong>Session System Mismatch:</strong> Updated SessionManager to work with both PHP and Laravel sessions</li>";
echo "<li><strong>Controller Methods:</strong> Modified takeQuiz() and submitQuizAttempt() to use SessionManager instead of Laravel session</li>";
echo "<li><strong>Database Ownership:</strong> Created a fresh quiz attempt with correct student ownership (2025-08-00003)</li>";
echo "<li><strong>Authentication Flow:</strong> Fixed middleware and session handling to work seamlessly</li>";
echo "</ol>";
echo "<h3>🎯 Expected Result:</h3>";
echo "<p style='font-size: 16px;'>The quiz route should now work perfectly! You should be able to see the quiz question and take the quiz without any redirects to login or dashboard.</p>";
echo "</div>";
?>
