<?php
echo "Creating available batches for all programs...\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=artc', 'root', '');
    
    // Get all programs
    $stmt = $pdo->query("SELECT program_id, program_name FROM programs");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($programs as $program) {
        $programId = $program['program_id'];
        $programName = $program['program_name'];
        
        // Check if program has available batches
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as available_count 
            FROM student_batches 
            WHERE program_id = ? AND batch_status = 'available'
        ");
        $stmt->execute([$programId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['available_count'] == 0) {
            echo "Creating available batch for {$programName}...\n";
            
            // Create available batch
            $stmt = $pdo->prepare("
                INSERT INTO student_batches 
                (batch_name, program_id, max_capacity, current_capacity, batch_status, start_date, registration_deadline, created_at, updated_at)
                VALUES 
                (?, ?, 25, 5, 'available', ?, ?, NOW(), NOW())
            ");
            
            $startDate = date('Y-m-d', strtotime('+14 days'));
            $regDeadline = date('Y-m-d', strtotime('+10 days'));
            
            $stmt->execute(["{$programName} Available Batch", $programId, $startDate, $regDeadline]);
            echo "  ✅ Created available batch for {$programName}\n";
        } else {
            echo "  ℹ️  {$programName} already has {$result['available_count']} available batch(es)\n";
        }
    }
    
    // Show final summary
    echo "\nFinal batch summary:\n";
    $stmt = $pdo->query("
        SELECT p.program_name, 
               SUM(CASE WHEN b.batch_status = 'available' THEN 1 ELSE 0 END) as available,
               SUM(CASE WHEN b.batch_status = 'ongoing' THEN 1 ELSE 0 END) as ongoing,
               COUNT(b.batch_id) as total
        FROM programs p 
        LEFT JOIN student_batches b ON p.program_id = b.program_id 
        GROUP BY p.program_id, p.program_name
        ORDER BY p.program_name
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $row) {
        echo "{$row['program_name']}: {$row['available']} available, {$row['ongoing']} ongoing, {$row['total']} total\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
