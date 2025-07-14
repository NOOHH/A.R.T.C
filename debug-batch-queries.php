<?php
echo "Debug batch queries...\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=artc', 'root', '');
    
    $programs = [32, 33, 34];
    
    foreach ($programs as $programId) {
        echo "\n=== Program ID: $programId ===\n";
        
        // Raw query to see all batches
        $stmt = $pdo->prepare("
            SELECT batch_name, batch_status, current_capacity, max_capacity, 
                   start_date, registration_deadline,
                   (max_capacity - current_capacity) as available_slots
            FROM student_batches 
            WHERE program_id = ?
        ");
        $stmt->execute([$programId]);
        $allBatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "All batches:\n";
        foreach ($allBatches as $batch) {
            echo "  - {$batch['batch_name']}: {$batch['batch_status']}, {$batch['available_slots']} slots, deadline: {$batch['registration_deadline']}\n";
        }
        
        // Filtered query similar to controller
        $stmt = $pdo->prepare("
            SELECT batch_name, batch_status, current_capacity, max_capacity, 
                   start_date, registration_deadline,
                   (max_capacity - current_capacity) as available_slots
            FROM student_batches 
            WHERE program_id = ? 
            AND batch_status IN ('available', 'ongoing')
            AND (max_capacity - current_capacity) > 0
            AND (
                (batch_status = 'available' AND (registration_deadline >= CURDATE() OR registration_deadline IS NULL))
                OR batch_status = 'ongoing'
            )
        ");
        $stmt->execute([$programId]);
        $filteredBatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Filtered batches (API would return):\n";
        if (empty($filteredBatches)) {
            echo "  ❌ NO BATCHES FOUND!\n";
        } else {
            foreach ($filteredBatches as $batch) {
                echo "  ✅ {$batch['batch_name']}: {$batch['batch_status']}, {$batch['available_slots']} slots\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
