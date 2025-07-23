<?php
/**
 * Quick test to check resubmitted registrations
 */

// Check if we can access the database
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== RESUBMITTED REGISTRATIONS CHECK ===\n";
    
    // Check if there are any resubmitted registrations
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'resubmitted'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Resubmitted registrations in database: {$result['count']}\n";
    
    // If none exist, let's create one for testing
    if ($result['count'] == 0) {
        echo "Creating a test resubmitted registration...\n";
        
        // Find a rejected registration to convert
        $stmt = $pdo->query("SELECT * FROM registrations WHERE status = 'rejected' LIMIT 1");
        $rejected = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rejected) {
            $stmt = $pdo->prepare("UPDATE registrations SET status = 'resubmitted', resubmitted_at = NOW() WHERE registration_id = ?");
            $stmt->execute([$rejected['registration_id']]);
            echo "✓ Created test resubmitted registration (ID: {$rejected['registration_id']})\n";
        } else {
            echo "No rejected registrations found to convert\n";
        }
    }
    
    // Display current status counts
    $statuses = ['pending', 'approved', 'rejected', 'resubmitted'];
    echo "\n=== REGISTRATION STATUS SUMMARY ===\n";
    foreach ($statuses as $status) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM registrations WHERE status = ?");
        $stmt->execute([$status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 {$status}: {$result['count']}\n";
    }
    
    echo "\n✅ Test complete!\n";
    echo "Now navigate to: /admin-student-registration/resubmitted to see the resubmitted registrations\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
