<?php
/**
 * Simple Quiz System Debug Script
 * Direct MySQL queries to check quiz data
 */

// Database connection
$host = 'localhost';
$dbname = 'artc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== QUIZ ADMIN SYSTEM DEBUG ===\n\n";
    
    // 1. Check database structure
    echo "1. CHECKING QUIZZES TABLE STRUCTURE:\n";
    echo "-----------------------------------\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM quizzes");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} ({$row['Type']}) - Null: {$row['Null']}\n";
    }
    
    // 2. Check existing quiz data
    echo "\n2. CHECKING EXISTING QUIZ DATA:\n";
    echo "------------------------------\n";
    
    // Total quizzes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM quizzes");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total quizzes: {$total}\n";
    
    // Admin created quizzes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM quizzes WHERE admin_id IS NOT NULL");
    $adminCreated = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Admin created quizzes: {$adminCreated}\n";
    
    // Professor created quizzes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM quizzes WHERE professor_id IS NOT NULL AND admin_id IS NULL");
    $professorCreated = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Professor created quizzes: {$professorCreated}\n";
    
    // Legacy quizzes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM quizzes WHERE admin_id IS NULL AND professor_id IS NULL");
    $legacy = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Legacy quizzes: {$legacy}\n";
    
    // 3. Quiz breakdown by status
    echo "\n3. QUIZ BREAKDOWN BY STATUS:\n";
    echo "---------------------------\n";
    
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM quizzes GROUP BY status");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['status']}: {$row['count']} quizzes\n";
    }
    
    // 4. Recent quizzes with creator info
    echo "\n4. RECENT QUIZZES WITH CREATOR INFO:\n";
    echo "-----------------------------------\n";
    
    $stmt = $pdo->query("
        SELECT 
            q.quiz_id, 
            q.quiz_title, 
            q.status, 
            q.admin_id, 
            q.professor_id, 
            q.created_at,
            p.program_name 
        FROM quizzes q 
        LEFT JOIN programs p ON q.program_id = p.program_id 
        ORDER BY q.created_at DESC 
        LIMIT 10
    ");
    
    while ($quiz = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $creator = 'Unknown';
        if ($quiz['admin_id']) {
            $creator = "Admin (ID: {$quiz['admin_id']})";
        } elseif ($quiz['professor_id']) {
            $creator = "Professor (ID: {$quiz['professor_id']})";
        }
        
        echo "  Quiz ID: {$quiz['quiz_id']}\n";
        echo "    Title: {$quiz['quiz_title']}\n";
        echo "    Status: {$quiz['status']}\n";
        echo "    Creator: {$creator}\n";
        echo "    Program: {$quiz['program_name']}\n";
        echo "    Created: {$quiz['created_at']}\n\n";
    }
    
    // 5. Check admin accounts
    echo "5. CHECKING ADMIN ACCOUNTS:\n";
    echo "--------------------------\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admins");
    $adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total admin accounts: {$adminCount}\n";
    
    if ($adminCount > 0) {
        echo "\nAdmin accounts:\n";
        $stmt = $pdo->query("SELECT admin_id, admin_name, email FROM admins");
        while ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - ID: {$admin['admin_id']}, Name: {$admin['admin_name']}, Email: {$admin['email']}\n";
        }
    }
    
    // 6. Test admin quiz query
    echo "\n6. TESTING ADMIN QUIZ QUERY:\n";
    echo "---------------------------\n";
    
    // Get first admin
    $stmt = $pdo->query("SELECT admin_id FROM admins LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        $adminId = $admin['admin_id'];
        echo "Testing with Admin ID: {$adminId}\n";
        
        // Query that the controller would use
        $stmt = $pdo->prepare("
            SELECT 
                quiz_id, 
                quiz_title, 
                status, 
                admin_id, 
                professor_id 
            FROM quizzes 
            WHERE admin_id = ? OR admin_id IS NULL
            ORDER BY created_at DESC
        ");
        $stmt->execute([$adminId]);
        
        $draftCount = 0;
        $publishedCount = 0;
        $archivedCount = 0;
        $totalAdminQuizzes = 0;
        
        while ($quiz = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $totalAdminQuizzes++;
            switch ($quiz['status']) {
                case 'draft':
                    $draftCount++;
                    break;
                case 'published':
                    $publishedCount++;
                    break;
                case 'archived':
                    $archivedCount++;
                    break;
            }
        }
        
        echo "Admin would see {$totalAdminQuizzes} quizzes:\n";
        echo "  - Draft: {$draftCount}\n";
        echo "  - Published: {$publishedCount}\n";
        echo "  - Archived: {$archivedCount}\n";
    }
    
    // 7. Test creating admin quiz
    echo "\n7. TESTING ADMIN QUIZ CREATION:\n";
    echo "------------------------------\n";
    
    if ($admin) {
        // Get first program
        $stmt = $pdo->query("SELECT program_id, program_name FROM programs WHERE is_archived = 0 LIMIT 1");
        $program = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($program) {
            echo "Using Program: {$program['program_name']} (ID: {$program['program_id']})\n";
            
            try {
                // Insert test quiz
                $stmt = $pdo->prepare("
                    INSERT INTO quizzes (
                        admin_id, 
                        professor_id, 
                        program_id, 
                        quiz_title, 
                        quiz_description, 
                        instructions, 
                        status, 
                        is_draft, 
                        is_active, 
                        total_questions, 
                        time_limit,
                        created_at,
                        updated_at
                    ) VALUES (?, NULL, ?, ?, ?, ?, 'draft', 1, 0, 0, 60, NOW(), NOW())
                ");
                
                $testTitle = 'DEBUG TEST QUIZ - Admin Created ' . date('Y-m-d H:i:s');
                $stmt->execute([
                    $adminId,
                    $program['program_id'],
                    $testTitle,
                    'This is a test quiz created by debug script',
                    'Test instructions'
                ]);
                
                $quizId = $pdo->lastInsertId();
                echo "✓ Successfully created test quiz with ID: {$quizId}\n";
                
                // Verify the quiz was created
                $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
                $stmt->execute([$quizId]);
                $createdQuiz = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($createdQuiz) {
                    echo "✓ Quiz verification successful:\n";
                    echo "    Title: {$createdQuiz['quiz_title']}\n";
                    echo "    Admin ID: {$createdQuiz['admin_id']}\n";
                    echo "    Professor ID: {$createdQuiz['professor_id']}\n";
                    echo "    Status: {$createdQuiz['status']}\n";
                }
                
                // Clean up - delete the test quiz
                $stmt = $pdo->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
                $stmt->execute([$quizId]);
                echo "✓ Test quiz cleaned up\n";
                
            } catch (Exception $e) {
                echo "ERROR creating test quiz: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ERROR: No active program found for testing!\n";
        }
    }
    
    echo "\n=== DEBUG COMPLETE ===\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
