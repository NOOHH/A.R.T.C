<?php
// Create test rejected payment data
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'artc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Update the existing payment to rejected status for testing
    $updateSql = "UPDATE payments SET 
        payment_status = 'rejected',
        rejection_reason = 'Invalid reference number and unclear payment proof image',
        rejected_fields = JSON_ARRAY('reference_number', 'payment_proof'),
        rejected_at = NOW(),
        rejected_by = 1
        WHERE payment_id = 1";
    
    $pdo->exec($updateSql);
    echo "Updated payment ID 1 to rejected status for testing.\n";
    
    // Show the updated payment
    $stmt = $pdo->query("SELECT payment_id, student_id, payment_status, rejection_reason, rejected_fields, rejected_at FROM payments WHERE payment_status = 'rejected'");
    $rejectedPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nRejected payments in database:\n";
    foreach ($rejectedPayments as $payment) {
        echo "- Payment ID: {$payment['payment_id']}\n";
        echo "  Student ID: {$payment['student_id']}\n";
        echo "  Status: {$payment['payment_status']}\n";
        echo "  Reason: {$payment['rejection_reason']}\n";
        echo "  Rejected Fields: {$payment['rejected_fields']}\n";
        echo "  Rejected At: {$payment['rejected_at']}\n";
        echo "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
