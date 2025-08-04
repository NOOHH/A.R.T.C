<?php
echo "<h1>🛠️ Robust Database Fix</h1>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Database connected</p>";
    
    // Step 1: Show current state
    echo "<h2>Step 1: Current State</h2>";
    $stmt = $pdo->prepare("SELECT attempt_id, student_id, quiz_id, status FROM quiz_attempts WHERE attempt_id = ?");
    $stmt->execute([3]);
    $before = $stmt->fetch();
    
    echo "<p><strong>Before:</strong> student_id = <code>" . $before['student_id'] . "</code></p>";
    
    // Step 2: Force update with explicit transaction
    echo "<h2>Step 2: Force Update</h2>";
    
    $pdo->beginTransaction();
    
    $updateStmt = $pdo->prepare("UPDATE quiz_attempts SET student_id = ? WHERE attempt_id = ?");
    $result = $updateStmt->execute(['2025-08-00003', 3]);
    
    if ($result) {
        $rowsAffected = $updateStmt->rowCount();
        echo "<p>✅ Update executed, rows affected: $rowsAffected</p>";
    } else {
        echo "<p>❌ Update failed</p>";
        $pdo->rollBack();
        exit;
    }
    
    $pdo->commit();
    
    // Step 3: Verify the change
    echo "<h2>Step 3: Verification</h2>";
    $stmt = $pdo->prepare("SELECT attempt_id, student_id, quiz_id, status FROM quiz_attempts WHERE attempt_id = ?");
    $stmt->execute([3]);
    $after = $stmt->fetch();
    
    echo "<p><strong>After:</strong> student_id = <code>" . $after['student_id'] . "</code></p>";
    
    if ($after['student_id'] === '2025-08-00003') {
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; color: #155724; margin: 20px 0;'>";
        echo "<h3>🎉 SUCCESS!</h3>";
        echo "<p>Quiz attempt #3 now correctly belongs to student <strong>2025-08-00003</strong></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; color: #721c24; margin: 20px 0;'>";
        echo "<h3>❌ FAILED!</h3>";
        echo "<p>Update did not work. Still shows: <code>" . $after['student_id'] . "</code></p>";
        echo "</div>";
    }
    
    // Step 4: Also check/create a fresh quiz attempt for this student if needed
    echo "<h2>Step 4: Alternative - Create New Attempt</h2>";
    
    // Check if student has their own quiz attempt
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE student_id = ? AND quiz_id = ? AND status = 'in_progress'");
    $stmt->execute(['2025-08-00003', 63]);
    $ownAttempt = $stmt->fetch();
    
    if ($ownAttempt) {
        echo "<p>✅ Student already has their own attempt: #" . $ownAttempt['attempt_id'] . "</p>";
        echo "<p><strong>Suggestion:</strong> Use attempt ID " . $ownAttempt['attempt_id'] . " instead of 3</p>";
        echo "<a href='/A.R.T.C/public/student/quiz/take/" . $ownAttempt['attempt_id'] . "' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>🎯 Try Their Own Attempt</a>";
    } else {
        echo "<p>ℹ️ Student doesn't have their own attempt yet</p>";
        echo "<p>We can either fix attempt #3 or create a new one</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>";
    echo "<p><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr style='margin: 30px 0;'>";
echo "<h2>🧪 Test the Fixed Quiz</h2>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>🎯 Test Quiz Route</a>";
?>
