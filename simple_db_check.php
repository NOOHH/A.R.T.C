<?php

// Simple database check
$host = '127.0.0.1';
$database = 'artc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful to: $database\n\n";
    
    // Show tables
    echo "=== TABLES IN DATABASE ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
    
    echo "\n=== ANNOUNCEMENT SETTINGS ===\n";
    $stmt = $pdo->prepare("SELECT * FROM admin_settings WHERE setting_name LIKE '%announcement%'");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Setting: {$row['setting_name']}\n";
        echo "Value: {$row['setting_value']}\n";
        echo "Whitelist: {$row['whitelisted_users']}\n\n";
    }
    
    echo "=== RECENT ANNOUNCEMENTS ===\n";
    $stmt = $pdo->prepare("SELECT id, title, admin_id, professor_id, created_at FROM announcements ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Title: {$row['title']}, Admin ID: {$row['admin_id']}, Professor ID: {$row['professor_id']}, Created: {$row['created_at']}\n";
    }
    
    echo "\n=== DIRECTORS DATA ===\n";
    $stmt = $pdo->prepare("SELECT director_id, first_name, last_name, admin_id FROM directors LIMIT 5");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Director ID: {$row['director_id']}, Name: {$row['first_name']} {$row['last_name']}, Admin ID: {$row['admin_id']}\n";
    }
    
    echo "\n=== ASSIGNMENT SUBMISSIONS STRUCTURE ===\n";
    $stmt = $pdo->query("DESCRIBE assignment_submissions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']}) " . 
             ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . 
             ($row['Key'] ? " {$row['Key']}" : '') . "\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}
