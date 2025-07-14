<?php
echo "Creating test batch data...\n";

try {
    require_once 'vendor/autoload.php';
    
    // Load Laravel without full bootstrap to avoid request issues
    $app = require_once 'bootstrap/app.php';
    
    // Set up the database connection manually
    $app->singleton('db', function () {
        $config = [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'artc',
            'username' => 'root',
            'password' => '',
        ];
        
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
        return new PDO($dsn, $config['username'], $config['password']);
    });
    
    $pdo = $app->make('db');
    
    // Check programs first
    $stmt = $pdo->query("SELECT * FROM programs LIMIT 5");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($programs) . " programs:\n";
    foreach ($programs as $program) {
        echo "- {$program['program_id']}: {$program['program_name']}\n";
    }
    
    // Check batches for the first available program
    $programId = $programs[0]['program_id'];
    $stmt = $pdo->query("SELECT * FROM student_batches WHERE program_id = $programId");
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nFound " . count($batches) . " batches for program $programId:\n";
    foreach ($batches as $batch) {
        echo "- {$batch['batch_name']} (Status: {$batch['batch_status']})\n";
    }
    
    // Create a test batch if none exist for this program
    if (count($batches) === 0 && count($programs) > 0) {
        echo "\nCreating a test batch for program $programId...\n";
        $stmt = $pdo->prepare("
            INSERT INTO student_batches 
            (batch_name, program_id, max_capacity, current_capacity, batch_status, start_date, registration_deadline, created_at, updated_at)
            VALUES 
            (?, ?, 30, 0, 'available', ?, ?, NOW(), NOW())
        ");
        
        $startDate = date('Y-m-d', strtotime('+7 days'));
        $regDeadline = date('Y-m-d', strtotime('+5 days'));
        
        $stmt->execute(['Test Batch Alpha', $programId, $startDate, $regDeadline]);
        echo "Test batch created successfully!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>
