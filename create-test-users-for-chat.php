<?php

// Create Test Users for Chat System
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔧 Creating Test Users for Chat System\n";
    echo "=====================================\n\n";

    // Create test student
    echo "👨‍🎓 Creating Test Student...\n";
    $stmt = $pdo->prepare("SELECT user_id FROM students WHERE email = ?");
    $stmt->execute(['student@test.com']);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO students (user_id, firstname, lastname, email, student_school, contact_number, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([999, 'Test', 'Student', 'student@test.com', 'Test University', '09123456789']);
        echo "✅ Test Student created (ID: 999)\n";
    } else {
        echo "✅ Test Student already exists\n";
    }

    // Create test director
    echo "👨‍💻 Creating Test Director...\n";
    $stmt = $pdo->prepare("SELECT directors_id FROM directors WHERE directors_email = ?");
    $stmt->execute(['director@test.com']);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO directors (directors_id, admin_id, directors_name, directors_first_name, directors_last_name, directors_email, directors_password, has_all_program_access, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([999, 1, 'Test Director', 'Test', 'Director', 'director@test.com', $hashedPassword, 1]);
        echo "✅ Test Director created (ID: 999)\n";
    } else {
        echo "✅ Test Director already exists\n";
    }

    echo "\n🎉 Test Users Setup Complete!\n";
    echo "Available Test Users:\n";
    echo "- Student: student@test.com / password123\n";
    echo "- Professor: robert@gmail.com / password123\n";
    echo "- Admin: admin@artc.com / admin123\n";
    echo "- Director: director@test.com / password123\n\n";

} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
