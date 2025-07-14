<?php
echo "Fixing registration deadlines...\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=artc', 'root', '');
    
    // Update past deadlines for available batches
    $stmt = $pdo->prepare("
        UPDATE student_batches 
        SET registration_deadline = DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        WHERE batch_status = 'available' 
        AND registration_deadline < CURDATE()
    ");
    
    $stmt->execute();
    $rowsUpdated = $stmt->rowCount();
    
    echo "Updated {$rowsUpdated} batches with past deadlines\n";
    
    // Show updated batches
    $stmt = $pdo->query("
        SELECT p.program_name, b.batch_name, b.batch_status, b.registration_deadline
        FROM student_batches b
        JOIN programs p ON b.program_id = p.program_id
        WHERE b.batch_status = 'available'
        ORDER BY p.program_name, b.batch_name
    ");
    
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nAvailable batches with deadlines:\n";
    foreach ($batches as $batch) {
        echo "  {$batch['program_name']}: {$batch['batch_name']} - deadline: {$batch['registration_deadline']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
