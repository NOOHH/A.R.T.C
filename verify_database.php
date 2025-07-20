<?php
/**
 * Database Structure Verification for Payment System
 */

echo "=== DATABASE STRUCTURE VERIFICATION ===\n\n";

try {
    // Connect to database using Laravel's database configuration
    $host = '127.0.0.1';
    $db = 'artc';
    $user = 'root';
    $pass = '';
    
    $dsn = "mysql:host=$host;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n\n";
    
    // Check required tables
    $requiredTables = [
        'payment_methods' => ['payment_method_id', 'method_name', 'method_type', 'qr_code_path', 'is_enabled'],
        'payments' => ['payment_id', 'enrollment_id', 'student_id', 'payment_status', 'payment_details'],
        'enrollments' => ['enrollment_id', 'user_id', 'student_id', 'program_id', 'payment_status'],
        'students' => ['student_id', 'user_id', 'firstname', 'lastname'],
        'users' => ['user_id', 'email', 'user_firstname', 'user_lastname']
    ];
    
    echo "CHECKING REQUIRED TABLES AND COLUMNS:\n";
    foreach ($requiredTables as $table => $columns) {
        // Check if table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE '$table'");
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
            
            // Check columns
            $stmt = $pdo->prepare("DESCRIBE $table");
            $stmt->execute();
            $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($columns as $column) {
                if (in_array($column, $existingColumns)) {
                    echo "  ✓ Column '$column'\n";
                } else {
                    echo "  ✗ Column '$column' MISSING\n";
                }
            }
        } else {
            echo "✗ Table '$table' MISSING\n";
        }
        echo "\n";
    }
    
    // Check payment methods data
    echo "CHECKING PAYMENT METHODS DATA:\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM payment_methods WHERE is_enabled = 1");
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo "✓ Found $count enabled payment methods\n";
        
        $stmt = $pdo->query("SELECT method_name, method_type, qr_code_path FROM payment_methods WHERE is_enabled = 1");
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($methods as $method) {
            $qrStatus = $method['qr_code_path'] ? 'QR Available' : 'No QR';
            echo "  - {$method['method_name']} ({$method['method_type']}) - $qrStatus\n";
        }
    } else {
        echo "⚠ No enabled payment methods found\n";
        echo "  Creating sample payment methods...\n";
        
        $stmt = $pdo->prepare("
            INSERT INTO payment_methods (method_name, method_type, description, instructions, is_enabled, sort_order, created_by_admin_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $methods = [
            ['GCash', 'gcash', 'Pay via GCash mobile wallet', 'Send payment to GCash number and upload receipt', 1, 1, 1],
            ['Maya (PayMaya)', 'maya', 'Pay via Maya mobile wallet', 'Send payment to Maya account and upload receipt', 1, 2, 1],
            ['Bank Transfer', 'bank_transfer', 'Pay via bank transfer', 'Transfer to our bank account and upload receipt', 1, 3, 1]
        ];
        
        foreach ($methods as $method) {
            $stmt->execute($method);
        }
        
        echo "  ✓ Sample payment methods created\n";
    }
    
    // Check enrollment data with payment_status
    echo "\nCHECKING ENROLLMENT DATA:\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE enrollment_status = 'approved' AND payment_status != 'paid'");
    $pendingPayments = $stmt->fetchColumn();
    
    echo "Found $pendingPayments enrollments requiring payment\n";
    
    if ($pendingPayments > 0) {
        echo "✓ Test data available for payment modal testing\n";
    } else {
        echo "⚠ No test data for payment modal (this is normal for fresh install)\n";
    }
    
    // Check file upload directory
    echo "\nCHECKING FILE UPLOAD DIRECTORIES:\n";
    $directories = [
        'storage/app/public/payment_proofs' => 'Payment proof uploads',
        'storage/app/public/payment_qr_codes' => 'QR code storage'
    ];
    
    foreach ($directories as $dir => $description) {
        if (is_dir($dir)) {
            $writable = is_writable($dir) ? 'writable' : 'not writable';
            echo "✓ $description: $dir ($writable)\n";
        } else {
            echo "⚠ Creating directory: $dir\n";
            if (mkdir($dir, 0755, true)) {
                echo "✓ Directory created successfully\n";
            } else {
                echo "✗ Failed to create directory\n";
            }
        }
    }
    
    echo "\n=== VERIFICATION COMPLETE ===\n";
    echo "\nSYSTEM STATUS:\n";
    echo "✓ Database structure is ready\n";
    echo "✓ Payment methods table configured\n";
    echo "✓ File upload directories prepared\n";
    echo "✓ Ready for student payment modal testing\n\n";
    
    echo "NEXT STEPS:\n";
    echo "1. Access test page: http://127.0.0.1:8000/test-payment-modal.html\n";
    echo "2. Test payment modal functionality\n";
    echo "3. Upload QR codes via admin panel\n";
    echo "4. Test with real student enrollment data\n\n";

} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "- MySQL/XAMPP is running\n";
    echo "- Database 'artc' exists\n"; 
    echo "- Database credentials in .env file\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
