<?php
echo "=== COMPREHENSIVE AUTHENTICATION DIAGNOSIS ===\n\n";

try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'smartprep';
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "✅ Connected to SmartPrep database\n\n";
    
    // 1. Check all tables
    echo "=== AVAILABLE TABLES ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    echo "\n";
    
    // 2. Check admins table structure and data
    echo "=== ADMINS TABLE ===\n";
    echo "Structure:\n";
    $stmt = $pdo->query("DESCRIBE admins");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  {$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']}\n";
    }
    
    echo "\nData:\n";
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM admins");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($admins)) {
        echo "  No admins found!\n";
    } else {
        foreach ($admins as $admin) {
            echo "  ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}\n";
        }
    }
    echo "\n";
    
    // 3. Check users table structure and data
    echo "=== USERS TABLE ===\n";
    echo "Structure:\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  {$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']}\n";
    }
    
    echo "\nData (last 5 users):\n";
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($users)) {
        echo "  No users found!\n";
    } else {
        foreach ($users as $user) {
            echo "  ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
        }
    }
    echo "\n";
    
    // 4. Test password hashes
    echo "=== PASSWORD VERIFICATION TEST ===\n";
    
    // Check specific admin account
    $stmt = $pdo->prepare("SELECT name, email, password FROM admins WHERE email = 'admin@smartprep.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "Admin found: {$admin['name']} ({$admin['email']})\n";
        $passwordCheck = password_verify('admin123', $admin['password']);
        echo "Password 'admin123' verification: " . ($passwordCheck ? "✅ VALID" : "❌ INVALID") . "\n";
        echo "Password hash: " . substr($admin['password'], 0, 60) . "...\n";
    } else {
        echo "❌ Admin admin@smartprep.com not found in admins table\n";
    }
    
    // Check a client account
    echo "\nClient accounts:\n";
    $stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE role = 'client' LIMIT 3");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($clients)) {
        echo "❌ No client accounts found\n";
    } else {
        foreach ($clients as $client) {
            echo "  Client: {$client['name']} ({$client['email']}) - Role: {$client['role']}\n";
        }
    }
    echo "\n";
    
    // 5. Check for any remaining admin users in users table
    echo "=== CHECKING FOR MISPLACED ADMIN USERS ===\n";
    $stmt = $pdo->query("SELECT id, name, email, role FROM users WHERE role = 'admin'");
    $misplacedAdmins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($misplacedAdmins)) {
        echo "✅ No admin users found in users table (correct)\n";
    } else {
        echo "⚠️ Found admin users in users table:\n";
        foreach ($misplacedAdmins as $admin) {
            echo "  ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}\n";
        }
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>
