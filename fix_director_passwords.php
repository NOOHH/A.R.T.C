<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Director;
use Illuminate\Support\Facades\Hash;

echo "=== FIXING DIRECTOR PASSWORD ENCRYPTION ===\n";

try {
    // Find directors with plain text passwords (length < 20 characters)
    $directorsWithPlainPasswords = Director::whereRaw('LENGTH(directors_password) < 20')->get();
    
    echo "\nFound " . $directorsWithPlainPasswords->count() . " directors with plain text passwords\n\n";
    
    if ($directorsWithPlainPasswords->count() === 0) {
        echo "✅ All directors already have encrypted passwords!\n";
        return;
    }
    
    foreach ($directorsWithPlainPasswords as $director) {
        echo "Processing: {$director->directors_name} ({$director->directors_email})\n";
        echo "Current password: {$director->directors_password}\n";
        
        // Hash the plain text password
        $hashedPassword = Hash::make($director->directors_password);
        
        // Update the director with hashed password
        $director->update(['directors_password' => $hashedPassword]);
        
        echo "✅ Password encrypted successfully\n";
        echo "New password hash: " . substr($hashedPassword, 0, 30) . "...\n\n";
    }
    
    // Verify the fix
    echo "\n=== VERIFICATION ===\n";
    $remainingPlainPasswords = Director::whereRaw('LENGTH(directors_password) < 20')->count();
    $hashedPasswords = Director::whereRaw('LENGTH(directors_password) >= 20')->count();
    
    echo "Directors with plain text passwords: $remainingPlainPasswords\n";
    echo "Directors with hashed passwords: $hashedPasswords\n";
    
    if ($remainingPlainPasswords === 0) {
        echo "\n🎉 SUCCESS: All director passwords are now properly encrypted!\n";
    } else {
        echo "\n⚠️ WARNING: Some passwords still need fixing\n";
    }
    
} catch (Exception $e) {
    echo "Error during password fixing: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
