<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking tenant database and fixing issues...\n\n";

$tenant = 'test2';

// Check tenant database structure
echo "📋 Test 1: Tenant Database Structure Check\n";
echo "==========================================\n";

try {
    // Switch to tenant database
    \App\Models\Tenant::switchToTenant($tenant);
    
    // Check if programs table exists and get its structure
    $columns = \DB::select("DESCRIBE programs");
    echo "✅ Programs table structure:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field}: {$column->Type} {$column->Null} {$column->Key}\n";
    }
    
    // Check programs count
    $programsCount = \App\Models\Program::count();
    echo "\n✅ Tenant database has {$programsCount} programs\n";
    
    if ($programsCount > 0) {
        $programs = \App\Models\Program::take(5)->get();
        echo "Sample programs:\n";
        foreach ($programs as $program) {
            echo "   - ID: {$program->program_id}, Name: {$program->program_name}\n";
        }
    }
    
    // Switch back to main database
    \App\Models\Tenant::switchToMain();
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Check if we need to copy data from artc tenant
echo "📋 Test 2: Check artc tenant data\n";
echo "==================================\n";

try {
    // Switch to artc tenant database
    \App\Models\Tenant::switchToTenant('artc');
    
    $artcProgramsCount = \App\Models\Program::count();
    echo "✅ ARTC tenant database has {$artcProgramsCount} programs\n";
    
    if ($artcProgramsCount > 0) {
        $artcPrograms = \App\Models\Program::take(3)->get();
        echo "Sample ARTC programs:\n";
        foreach ($artcPrograms as $program) {
            echo "   - ID: {$program->program_id}, Name: {$program->program_name}\n";
        }
    }
    
    // Switch back to main database
    \App\Models\Tenant::switchToMain();
    
} catch (Exception $e) {
    echo "❌ ARTC database error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Copy data from artc to test2 if needed
echo "📋 Test 3: Copy data from ARTC to test2\n";
echo "=======================================\n";

try {
    // Check if test2 has data
    \App\Models\Tenant::switchToTenant($tenant);
    $test2ProgramsCount = \App\Models\Program::count();
    
    if ($test2ProgramsCount == 0) {
        echo "🔄 Test2 tenant has no programs, copying from ARTC...\n";
        
        // Switch back to main database
        \App\Models\Tenant::switchToMain();
        
        // Use mysqldump to copy the database
        $artcDb = 'smartprep_artc';
        $test2Db = 'smartprep_test2';
        
        // Drop and recreate test2 database
        $dropCommand = "mysql -u root -e \"DROP DATABASE IF EXISTS {$test2Db}; CREATE DATABASE {$test2Db};\"";
        exec($dropCommand);
        
        // Copy data from artc to test2
        $copyCommand = "mysqldump -u root {$artcDb} | mysql -u root {$test2Db}";
        exec($copyCommand);
        
        echo "✅ Database copied successfully\n";
        
        // Verify the copy
        \App\Models\Tenant::switchToTenant($tenant);
        $newProgramsCount = \App\Models\Program::count();
        echo "✅ Test2 tenant now has {$newProgramsCount} programs\n";
        
    } else {
        echo "✅ Test2 tenant already has {$test2ProgramsCount} programs\n";
    }
    
    // Switch back to main database
    \App\Models\Tenant::switchToMain();
    
} catch (Exception $e) {
    echo "❌ Copy error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Test program creation with correct column names
echo "📋 Test 4: Test Program Creation\n";
echo "=================================\n";

try {
    // Switch to tenant database
    \App\Models\Tenant::switchToTenant($tenant);
    
    // Get the actual column names
    $columns = \DB::select("DESCRIBE programs");
    $columnNames = array_column($columns, 'Field');
    
    echo "Available columns: " . implode(', ', $columnNames) . "\n";
    
    // Create a test program with correct column names
    $programData = [
        'program_name' => 'Test Program for t/ Route - ' . date('Y-m-d H:i:s'),
        'is_active' => 1,
        'is_archived' => 0,
        'program_description' => 'Test program created to validate t/ prefixed routes'
    ];
    
    // Add created_by_admin_id if it exists
    if (in_array('created_by_admin_id', $columnNames)) {
        $programData['created_by_admin_id'] = 1;
    }
    
    // Add director_id if it exists
    if (in_array('director_id', $columnNames)) {
        $programData['director_id'] = null;
    }
    
    $testProgram = \App\Models\Program::create($programData);
    
    echo "✅ Program created successfully!\n";
    echo "   New program ID: {$testProgram->program_id}\n";
    echo "   Program name: {$testProgram->program_name}\n";
    
    // Switch back to main database
    \App\Models\Tenant::switchToMain();
    
} catch (Exception $e) {
    echo "❌ Program creation error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎉 Tenant Database Check Completed!\n";
echo "===================================\n";
echo "The tenant database should now have the correct data and structure.\n";
