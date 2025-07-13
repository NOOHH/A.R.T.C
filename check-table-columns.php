<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "STUDENTS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE students');
    while($row = $stmt->fetch()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\nPROFESSORS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE professors');
    while($row = $stmt->fetch()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\nADMINS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE admins');
    while($row = $stmt->fetch()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\nDIRECTORS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE directors');
    while($row = $stmt->fetch()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
