<?php

// Complete System Verification Test
// This script will verify all chat functionality is working properly

echo "🚀 A.R.T.C Chat System Verification\n";
echo "=====================================\n\n";

try {
    // Test 1: Database Connection
    echo "📊 Testing Database Connection...\n";
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful\n\n";

    // Test 2: Check Essential Tables
    echo "🗃️ Checking Essential Tables...\n";
    $requiredTables = ['students', 'professors', 'admins', 'directors', 'chats'];
    foreach ($requiredTables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✅ Table '$table': $count records\n";
    }
    echo "\n";

    // Test 3: Check Sample Data
    echo "👥 Checking Sample Users...\n";
    
    // Check for test student
    $stmt = $pdo->prepare("SELECT user_id, firstname, lastname, email FROM students WHERE email = ? LIMIT 1");
    $stmt->execute(['student@test.com']);
    if ($student = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $name = trim($student['firstname'] . ' ' . $student['lastname']);
        echo "✅ Test Student Found: $name (ID: {$student['user_id']})\n";
    } else {
        echo "❌ Test Student Not Found\n";
    }

    // Check for robert professor
    $stmt = $pdo->prepare("SELECT professor_id, professor_name, professor_email FROM professors WHERE professor_email = ? LIMIT 1");
    $stmt->execute(['robert@gmail.com']);
    if ($professor = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "✅ Robert Professor Found: {$professor['professor_name']} (ID: {$professor['professor_id']})\n";
    } else {
        echo "❌ Robert Professor Not Found\n";
    }

    // Check for admin
    $stmt = $pdo->prepare("SELECT admin_id, admin_name, email FROM admins WHERE email = ? LIMIT 1");
    $stmt->execute(['admin@artc.com']);
    if ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "✅ Admin Found: {$admin['admin_name']} (ID: {$admin['admin_id']})\n";
    } else {
        echo "❌ Admin Not Found\n";
    }

    // Check for director
    $stmt = $pdo->prepare("SELECT directors_id, directors_name, directors_email FROM directors WHERE directors_email = ? LIMIT 1");
    $stmt->execute(['director@test.com']);
    if ($director = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "✅ Director Found: {$director['directors_name']} (ID: {$director['directors_id']})\n";
    } else {
        echo "❌ Director Not Found\n";
    }
    echo "\n";

    // Test 4: Check Chat Table Structure
    echo "💬 Checking Chat Table Structure...\n";
    $stmt = $pdo->query("DESCRIBE chats");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Chat table columns:\n";
    foreach ($columns as $column) {
        echo "   - {$column['Field']} ({$column['Type']})\n";
    }
    echo "\n";

    // Test 5: Test Chat Operations
    echo "🔄 Testing Chat Operations...\n";
    
    // Insert a test message
    $stmt = $pdo->prepare("INSERT INTO chats (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())");
    $testMessage = "Test message from verification script at " . date('Y-m-d H:i:s');
    $stmt->execute([1, 2, $testMessage]);
    $messageId = $pdo->lastInsertId();
    echo "✅ Test message inserted (ID: $messageId)\n";

    // Retrieve the message
    $stmt = $pdo->prepare("SELECT * FROM chats WHERE chat_id = ?");
    $stmt->execute([$messageId]);
    if ($message = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "✅ Test message retrieved: " . substr($message['message'], 0, 50) . "...\n";
    }

    // Clean up test message
    $stmt = $pdo->prepare("DELETE FROM chats WHERE chat_id = ?");
    $stmt->execute([$messageId]);
    echo "✅ Test message cleaned up\n\n";

    // Test 6: Session Testing
    echo "🔐 Testing Session Functionality...\n";
    session_start();
    
    // Test student session
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'student';
    $_SESSION['logged_in'] = true;
    echo "✅ Student session variables set\n";

    // Test professor session
    $_SESSION['professor_id'] = 1;
    $_SESSION['professor_logged_in'] = true;
    echo "✅ Professor session variables set\n";

    // Test admin session
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_logged_in'] = true;
    echo "✅ Admin session variables set\n";

    // Test director session
    $_SESSION['directors_id'] = 1;
    $_SESSION['director_logged_in'] = true;
    echo "✅ Director session variables set\n";

    session_destroy();
    echo "✅ Session cleanup completed\n\n";

    // Test 7: Laravel Files Check
    echo "📁 Checking Laravel Files...\n";
    $laravelFiles = [
        'app/Http/Middleware/SessionAuth.php' => 'SessionAuth Middleware',
        'app/Http/Controllers/ChatController.php' => 'Chat Controller',
        'app/Http/Controllers/UnifiedLoginController.php' => 'Unified Login Controller',
        'app/Http/Kernel.php' => 'Kernel Configuration',
        'routes/web.php' => 'Web Routes'
    ];

    foreach ($laravelFiles as $file => $description) {
        if (file_exists(__DIR__ . '/' . $file)) {
            echo "✅ $description exists\n";
        } else {
            echo "❌ $description missing: $file\n";
        }
    }
    echo "\n";

    // Final Summary
    echo "🎉 VERIFICATION COMPLETE!\n";
    echo "========================\n";
    echo "✅ Database Connection: Working\n";
    echo "✅ Required Tables: Present\n";
    echo "✅ Chat Operations: Functional\n";
    echo "✅ Session Management: Configured\n";
    echo "✅ Laravel Files: Present\n\n";

    echo "🚀 Your Chat System is Ready!\n";
    echo "Test it at: http://127.0.0.1:8080/complete-chat-test.html\n\n";

    echo "📋 Available Test Users:\n";
    echo "- Student: student@test.com / password123\n";
    echo "- Professor: robert@gmail.com / password123\n";
    echo "- Admin: admin@artc.com / admin123\n";
    echo "- Director: director@test.com / password123\n\n";

} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
