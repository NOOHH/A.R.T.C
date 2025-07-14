<?php
echo "Checking batches for all programs...\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=artc', 'root', '');
    
    // Check all programs and their batches
    $stmt = $pdo->query("
        SELECT p.program_id, p.program_name, 
               COUNT(b.batch_id) as batch_count,
               GROUP_CONCAT(CONCAT(b.batch_name, ' (', b.batch_status, ')') SEPARATOR ', ') as batch_info
        FROM programs p 
        LEFT JOIN student_batches b ON p.program_id = b.program_id 
        GROUP BY p.program_id, p.program_name
        ORDER BY p.program_id
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Program Batch Summary:\n";
    echo "=====================\n";
    
    foreach ($results as $row) {
        echo "Program {$row['program_id']}: {$row['program_name']}\n";
        echo "  Batches: {$row['batch_count']}\n";
        if ($row['batch_info']) {
            echo "  Details: {$row['batch_info']}\n";
        }
        echo "\n";
    }
    
    // Create test batches for programs without any
    $missingBatches = array_filter($results, function($row) {
        return $row['batch_count'] == 0;
    });
    
    if (!empty($missingBatches)) {
        echo "Creating test batches for programs without batches...\n";
        
        foreach ($missingBatches as $program) {
            $programId = $program['program_id'];
            $programName = $program['program_name'];
            
            // Create available batch
            $stmt = $pdo->prepare("
                INSERT INTO student_batches 
                (batch_name, program_id, max_capacity, current_capacity, batch_status, start_date, registration_deadline, created_at, updated_at)
                VALUES 
                (?, ?, 25, 0, 'available', ?, ?, NOW(), NOW())
            ");
            
            $startDate = date('Y-m-d', strtotime('+10 days'));
            $regDeadline = date('Y-m-d', strtotime('+7 days'));
            
            $stmt->execute(["{$programName} Batch A", $programId, $startDate, $regDeadline]);
            
            // Create ongoing batch
            $stmt = $pdo->prepare("
                INSERT INTO student_batches 
                (batch_name, program_id, max_capacity, current_capacity, batch_status, start_date, registration_deadline, created_at, updated_at)
                VALUES 
                (?, ?, 30, 15, 'ongoing', ?, ?, NOW(), NOW())
            ");
            
            $startDate = date('Y-m-d', strtotime('-5 days'));
            $regDeadline = date('Y-m-d', strtotime('+2 days'));
            
            $stmt->execute(["{$programName} Batch B", $programId, $startDate, $regDeadline]);
            
            echo "  Created batches for {$programName}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
