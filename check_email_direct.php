<?php
// Simple direct database check for email
$email = 'bmjustimbaste2003@gmail.com';

try {
    // Connect to MySQL database
    $host = '127.0.0.1';
    $dbname = 'artc';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Searching for email: $email ===\n\n";
    
    // Check students table
    $stmt = $pdo->prepare("SELECT student_id, firstname, lastname, email FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo "✅ FOUND in STUDENTS table:\n";
        echo "   Student ID: {$student['student_id']}\n";
        echo "   Name: {$student['firstname']} {$student['lastname']}\n";
        echo "   Email: {$student['email']}\n\n";
    } else {
        echo "❌ NOT found in students table\n";
    }
    
    // Check admins table
    try {
        $stmt = $pdo->prepare("DESCRIBE admins");
        $stmt->execute();
        $adminColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Admin table columns: " . implode(', ', $adminColumns) . "\n";
        
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "✅ FOUND in ADMINS table:\n";
            foreach ($admin as $key => $value) {
                echo "   $key: $value\n";
            }
            echo "\n";
        } else {
            echo "❌ NOT found in admins table\n";
        }
    } catch (Exception $e) {
        echo "❌ Admin table check failed: " . $e->getMessage() . "\n";
    }
    
    // Check professors table
    $stmt = $pdo->prepare("SELECT professor_id, professor_first_name, professor_last_name, professor_email FROM professors WHERE professor_email = ?");
    $stmt->execute([$email]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($professor) {
        echo "✅ FOUND in PROFESSORS table:\n";
        echo "   Professor ID: {$professor['professor_id']}\n";
        echo "   Name: {$professor['professor_first_name']} {$professor['professor_last_name']}\n";
        echo "   Email: {$professor['professor_email']}\n\n";
    } else {
        echo "❌ NOT found in professors table\n";
    }
    
    // Check directors table
    $stmt = $pdo->prepare("SELECT director_id, directors_first_name, directors_last_name, directors_email FROM directors WHERE directors_email = ?");
    $stmt->execute([$email]);
    $director = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($director) {
        echo "✅ FOUND in DIRECTORS table:\n";
        echo "   Director ID: {$director['director_id']}\n";
        echo "   Name: {$director['directors_first_name']} {$director['directors_last_name']}\n";
        echo "   Email: {$director['directors_email']}\n\n";
    } else {
        echo "❌ NOT found in directors table\n";
    }
    
    // Check users table (if exists)
    try {
        $stmt = $pdo->prepare("SELECT id, firstname, lastname, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "✅ FOUND in USERS table:\n";
            echo "   User ID: {$user['id']}\n";
            echo "   Name: {$user['firstname']} {$user['lastname']}\n";
            echo "   Email: {$user['email']}\n\n";
        } else {
            echo "❌ NOT found in users table\n";
        }
    } catch (Exception $e) {
        echo "❌ Users table doesn't exist or error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Recommendation ===\n";
    echo "If your email is not found in any table, you need to:\n";
    echo "1. Register/create an account first\n";
    echo "2. Or add your email to one of the user tables\n";
    echo "3. Password reset only works for existing accounts\n";
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
