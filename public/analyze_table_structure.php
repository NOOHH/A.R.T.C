<?php
echo "<h1>🔍 Quiz Attempts Table Analysis</h1>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Database connected</p>";
    
    // Check the table structure
    echo "<h2>📋 Quiz Attempts Table Structure</h2>";
    $stmt = $pdo->prepare("SHOW COLUMNS FROM quiz_attempts");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    echo "<table style='border-collapse: collapse; width: 100%; margin: 15px 0;'>";
    echo "<tr style='background: #e9ecef;'>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Column</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Type</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Null</th>";
    echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Default</th>";
    echo "</tr>";
    
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px; font-weight: bold;'>" . $col['Field'] . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $col['Type'] . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $col['Null'] . "</td>";
        echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check what the existing attempt #3 looks like
    echo "<h2>🔍 Existing Attempt #3 Structure</h2>";
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE attempt_id = 3");
    $stmt->execute();
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
        echo "<h4>Attempt #3 Data:</h4>";
        foreach ($existing as $key => $value) {
            echo "<p><strong>$key:</strong> " . ($value ?? 'NULL') . "</p>";
        }
        echo "</div>";
        
        // Try to create a new attempt with the same structure but correct student_id
        echo "<h2>🆕 Create Correct Attempt</h2>";
        
        // First, let's see what columns are required and what their constraints are
        $stmt = $pdo->prepare("SHOW CREATE TABLE quiz_attempts");
        $stmt->execute();
        $createTable = $stmt->fetch();
        
        echo "<h4>📋 Table Creation SQL (for constraint info):</h4>";
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 12px; overflow-x: auto;'>";
        echo htmlspecialchars($createTable['Create Table']);
        echo "</div>";
        
        // Create a simplified insert with minimal required fields
        echo "<h3>🛠️ Creating Minimal Attempt</h3>";
        
        try {
            $insertStmt = $pdo->prepare("
                INSERT INTO quiz_attempts (student_id, quiz_id, status) 
                VALUES (?, ?, 'in_progress')
            ");
            
            $result = $insertStmt->execute(['2025-08-00003', $existing['quiz_id']]);
            
            if ($result) {
                $newId = $pdo->lastInsertId();
                echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; color: #155724; margin: 20px 0;'>";
                echo "<h3>🎉 SUCCESS!</h3>";
                echo "<p>Created new quiz attempt with ID: <strong>$newId</strong></p>";
                echo "<p>Student: 2025-08-00003</p>";
                echo "<p>Quiz ID: " . $existing['quiz_id'] . "</p>";
                echo "</div>";
                
                echo "<div style='text-align: center; margin: 30px 0;'>";
                echo "<a href='/A.R.T.C/public/student/quiz/take/$newId' target='_blank' style='background: #28a745; color: white; padding: 25px 40px; text-decoration: none; border-radius: 15px; margin: 15px; display: inline-block; font-weight: bold; font-size: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);'>🎯 Take Quiz - Attempt #$newId</a>";
                echo "</div>";
                
                // Show comparison
                echo "<h3>📊 Comparison</h3>";
                echo "<table style='border-collapse: collapse; width: 100%; margin: 15px 0;'>";
                echo "<tr style='background: #e9ecef;'>";
                echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Aspect</th>";
                echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Old Attempt #3</th>";
                echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>New Attempt #$newId</th>";
                echo "</tr>";
                echo "<tr>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px; font-weight: bold;'>Student ID</td>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px; background: #f8d7da;'>" . $existing['student_id'] . " ❌</td>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px; background: #d4edda;'>2025-08-00003 ✅</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px; font-weight: bold;'>Quiz ID</td>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $existing['quiz_id'] . "</td>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $existing['quiz_id'] . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px; font-weight: bold;'>Status</td>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $existing['status'] . "</td>";
                echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>in_progress</td>";
                echo "</tr>";
                echo "</table>";
                
            } else {
                echo "<p>❌ Insert failed</p>";
            }
            
        } catch (Exception $insertError) {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>";
            echo "<p><strong>❌ Insert Error:</strong> " . $insertError->getMessage() . "</p>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; color: #721c24;'>";
    echo "<p><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
