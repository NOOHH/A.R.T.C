<?php
/**
 * Test to verify student dashboard shows correct rejection status
 */

echo "=== STUDENT DASHBOARD REJECTION STATUS TEST ===\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find a student with rejected registration
    $stmt = $pdo->query("
        SELECT 
            r.registration_id,
            r.user_id,
            r.status,
            r.program_id,
            u.email,
            p.program_name
        FROM registrations r
        LEFT JOIN users u ON r.user_id = u.user_id
        LEFT JOIN programs p ON r.program_id = p.program_id
        WHERE r.status = 'rejected'
        LIMIT 1
    ");
    
    $rejected = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($rejected) {
        echo "Found rejected registration:\n";
        echo "- User ID: {$rejected['user_id']}\n";
        echo "- Email: {$rejected['email']}\n";
        echo "- Program: {$rejected['program_name']}\n";
        echo "- Status: {$rejected['status']}\n";
        echo "- Registration ID: {$rejected['registration_id']}\n\n";
        
        // Check if there's also an enrollment for the same user/program
        $stmt = $pdo->prepare("
            SELECT 
                enrollment_id,
                enrollment_status,
                payment_status
            FROM enrollments 
            WHERE user_id = ? AND program_id = ?
        ");
        $stmt->execute([$rejected['user_id'], $rejected['program_id']]);
        $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($enrollment) {
            echo "⚠️  FOUND CONFLICTING ENROLLMENT:\n";
            echo "- Enrollment ID: {$enrollment['enrollment_id']}\n";
            echo "- Enrollment Status: {$enrollment['enrollment_status']}\n";
            echo "- Payment Status: {$enrollment['payment_status']}\n";
            echo "\n❗ This explains why student dashboard shows enrollment status instead of registration status!\n";
            echo "📝 The controller fix should prioritize rejected registration over pending enrollment.\n";
        } else {
            echo "✅ No conflicting enrollment found - rejected registration should display correctly.\n";
        }
        
        // Test what the student should see
        echo "\n=== EXPECTED STUDENT DASHBOARD DISPLAY ===\n";
        echo "Button text should be: 'Registration Rejected - Click to Edit'\n";
        echo "Button class should be: 'resume-btn rejected'\n";
        echo "enrollment_status should be: 'rejected'\n";
        
    } else {
        echo "❌ No rejected registrations found in database.\n";
        echo "Creating a test rejected registration...\n";
        
        // Create a test user if needed
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'test.rejected@example.com'");
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute(['Test Rejected User', 'test.rejected@example.com', password_hash('password', PASSWORD_DEFAULT)]);
            $user_id = $pdo->lastInsertId();
        } else {
            $user_id = $user['id'];
        }
        
        // Get a program
        $stmt = $pdo->query("SELECT program_id FROM programs LIMIT 1");
        $program = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($program) {
            // Create rejected registration
            $stmt = $pdo->prepare("
                INSERT INTO registrations 
                (user_id, program_id, status, rejected_fields, rejection_reason, rejected_at, created_at, updated_at)
                VALUES (?, ?, 'rejected', ?, 'Test rejection reason', NOW(), NOW(), NOW())
            ");
            $rejected_fields = json_encode(['first_name' => 'Invalid format', 'email' => 'Invalid domain']);
            $stmt->execute([$user_id, $program['program_id'], $rejected_fields]);
            
            echo "✅ Created test rejected registration (ID: " . $pdo->lastInsertId() . ")\n";
            echo "🔍 Now test by logging in as: test.rejected@example.com (password: password)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Login as a student with rejected registration\n";
echo "2. Check if dashboard shows 'Registration Rejected - Click to Edit'\n";
echo "3. If still showing 'Pending Admin Approval', the enrollment is overriding the registration\n";
echo "4. The controller fix should resolve this by prioritizing rejected registrations\n";
?>
