<?php
require_once __DIR__ . '/vendor/autoload.php';

// Simple test script to check database connection and user data
try {
    // Create a PDO connection using Laravel's config
    $config = include(__DIR__ . '/config/database.php');
    $db = $config['connections']['mysql'];
    
    $dsn = "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db['username'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection successful!\n\n";
    
    // Check users table
    echo "=== USERS TABLE ===\n";
    $stmt = $pdo->query("SELECT user_id, user_firstname, user_lastname, email, role FROM users ORDER BY user_id DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['user_id']}, Name: {$row['user_firstname']} {$row['user_lastname']}, Email: {$row['email']}, Role: {$row['role']}\n";
    }
    
    // Check professors table
    echo "\n=== PROFESSORS TABLE ===\n";
    $stmt = $pdo->query("SELECT professor_id, professor_name, professor_first_name, professor_last_name, professor_email FROM professors WHERE professor_archived = 0");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['professor_id']}, Name: {$row['professor_name']}, First: {$row['professor_first_name']}, Last: {$row['professor_last_name']}, Email: {$row['professor_email']}\n";
    }
    
    // Check recent chat messages
    echo "\n=== RECENT CHAT MESSAGES ===\n";
    $stmt = $pdo->query("SELECT chat_id, sender_id, receiver_id, sent_at FROM chats ORDER BY sent_at DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo "Chat ID: {$row['chat_id']}, From: {$row['sender_id']}, To: {$row['receiver_id']}, Time: {$row['sent_at']}\n";
    }
    
    // Check students table structure (see if user_id exists)
    echo "\n=== STUDENTS TABLE SAMPLE ===\n";
    $stmt = $pdo->query("SELECT student_id, user_id, firstname, lastname, email FROM students LIMIT 3");
    while ($row = $stmt->fetch()) {
        echo "Student ID: {$row['student_id']}, User ID: {$row['user_id']}, Name: {$row['firstname']} {$row['lastname']}, Email: {$row['email']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
