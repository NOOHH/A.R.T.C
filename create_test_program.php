<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "➕ Creating test program in tenant database...\n\n";

$tenant = 'test2';

try {
    // Switch to tenant database
    \App\Models\Tenant::switchToTenant($tenant);
    
    // Create a test program with the correct column structure
    $testProgram = \App\Models\Program::create([
        'program_name' => 'Test Program for t/ Route - ' . date('Y-m-d H:i:s'),
        'program_description' => 'Test program created to validate t/ prefixed routes',
        'is_archived' => 0,
        'director_id' => null
    ]);
    
    echo "✅ Program created successfully!\n";
    echo "   New program ID: {$testProgram->id}\n";
    echo "   Program name: {$testProgram->program_name}\n";
    
    // Check total programs
    $totalPrograms = \App\Models\Program::count();
    echo "   Total programs in tenant database: {$totalPrograms}\n";
    
    // Switch back to main database
    \App\Models\Tenant::switchToMain();
    
    // Verify main database is unchanged
    $mainPrograms = \App\Models\Program::count();
    echo "   Main database programs: {$mainPrograms} (unchanged)\n";
    
} catch (Exception $e) {
    echo "❌ Program creation error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test program creation completed!\n";
