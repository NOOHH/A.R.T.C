<?php
echo "=== UNIFIED PASSWORD RESET FOR ANY USER ===\n\n";

// Configure these variables as needed
$targetEmail = 'robert@gmail.com';  // Change this to any email
$newPlainPassword = 'client123';     // Change this to desired password

try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'smartprep';
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "Attempting to reset password for: $targetEmail\n";
    echo "New password will be: $newPlainPassword\n\n";
    
    // Step 1: Check if it's an admin user (check admins table first)
    echo "=== CHECKING ADMINS TABLE ===\n";
    $stmt = $pdo->prepare("SELECT id, name, email FROM admins WHERE email = ?");
    $stmt->execute([$targetEmail]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Found admin: {$admin['name']} (ID: {$admin['id']})\n";
        
        // Update admin password
        $hashedPassword = password_hash($newPlainPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ?, updated_at = NOW() WHERE email = ?");
        $stmt->execute([$hashedPassword, $targetEmail]);
        
        // Verify the update
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE email = ?");
        $stmt->execute([$targetEmail]);
        $storedHash = $stmt->fetchColumn();
        $verification = password_verify($newPlainPassword, $storedHash);
        
        echo "Password updated in admins table\n";
        echo "Verification: " . ($verification ? "✅ SUCCESS" : "❌ FAILED") . "\n";
        
        echo "\n✅ ADMIN PASSWORD RESET COMPLETE!\n";
        echo "Login with:\n";
        echo "  Email: $targetEmail\n";
        echo "  Password: $newPlainPassword\n";
        echo "  URL: http://localhost:8000/smartprep/login\n";
        echo "  Expected: Redirect to admin dashboard\n";
        
    } else {
        // Step 2: Check if it's a regular user (check users table)
        echo "❌ Not found in admins table\n\n";
        echo "=== CHECKING USERS TABLE ===\n";
        
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE email = ?");
        $stmt->execute([$targetEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "✅ Found user: {$user['name']} (ID: {$user['id']}, Role: {$user['role']})\n";
            
            // Update user password
            $hashedPassword = password_hash($newPlainPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?");
            $stmt->execute([$hashedPassword, $targetEmail]);
            
            // Verify the update
            $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->execute([$targetEmail]);
            $storedHash = $stmt->fetchColumn();
            $verification = password_verify($newPlainPassword, $storedHash);
            
            echo "Password updated in users table\n";
            echo "Verification: " . ($verification ? "✅ SUCCESS" : "❌ FAILED") . "\n";
            
            echo "\n✅ USER PASSWORD RESET COMPLETE!\n";
            echo "Login with:\n";
            echo "  Email: $targetEmail\n";
            echo "  Password: $newPlainPassword\n";
            echo "  URL: http://localhost:8000/smartprep/login\n";
            echo "  Expected: Redirect to " . ($user['role'] === 'admin' ? 'admin' : 'client') . " dashboard\n";
            
        } else {
            echo "❌ Email '$targetEmail' not found in either admins or users table\n\n";
            
            echo "=== AVAILABLE ACCOUNTS ===\n";
            echo "Admins:\n";
            $stmt = $pdo->query("SELECT name, email FROM admins");
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($admins as $a) {
                echo "  - {$a['name']} ({$a['email']})\n";
            }
            
            echo "\nUsers (first 5):\n";
            $stmt = $pdo->query("SELECT name, email, role FROM users LIMIT 5");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $u) {
                echo "  - {$u['name']} ({$u['email']}) - {$u['role']}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
