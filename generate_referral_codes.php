<?php
// Generate referral codes for existing directors and professors

// Include the helper class
require_once 'app/Helpers/ReferralCodeGenerator.php';

// Database connection
$host = 'localhost';
$dbname = 'artc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n\n";
    
    // Generate codes for directors
    echo "Generating referral codes for directors:\n";
    echo "=====================================\n";
    
    $directors = $pdo->query("SELECT directors_id, directors_first_name FROM directors WHERE referral_code IS NULL")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($directors as $director) {
        $id = $director['directors_id'];
        $name = strtoupper($director['directors_first_name']);
        
        $referralCode = sprintf("DIR%02d%s", $id, $name);
        
        $stmt = $pdo->prepare("UPDATE directors SET referral_code = ? WHERE directors_id = ?");
        $stmt->execute([$referralCode, $id]);
        
        echo "Director ID $id: $referralCode\n";
    }
    
    // Generate codes for professors
    echo "\nGenerating referral codes for professors:\n";
    echo "========================================\n";
    
    $professors = $pdo->query("SELECT professor_id, professor_first_name FROM professors WHERE referral_code IS NULL")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($professors as $professor) {
        $id = $professor['professor_id'];
        $name = strtoupper($professor['professor_first_name']);
        
        $referralCode = sprintf("PROF%02d%s", $id, $name);
        
        $stmt = $pdo->prepare("UPDATE professors SET referral_code = ? WHERE professor_id = ?");
        $stmt->execute([$referralCode, $id]);
        
        echo "Professor ID $id: $referralCode\n";
    }
    
    echo "\nReferral code generation completed!\n";
    
    // Verify the results
    echo "\nVerification - Directors:\n";
    echo "========================\n";
    $directors = $pdo->query("SELECT directors_id, directors_name, referral_code FROM directors")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($directors as $director) {
        echo "ID {$director['directors_id']}: {$director['directors_name']} -> {$director['referral_code']}\n";
    }
    
    echo "\nVerification - Professors:\n";
    echo "=========================\n";
    $professors = $pdo->query("SELECT professor_id, professor_name, referral_code FROM professors")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($professors as $professor) {
        echo "ID {$professor['professor_id']}: {$professor['professor_name']} -> {$professor['referral_code']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
