<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Database configuration (adjust as needed)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'artc', 
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== MODULAR ENROLLMENT DEBUG ===\n\n";

// 1. Check database tables exist and structure
echo "1. Checking Database Tables:\n";

$tables = [
    'users', 'registrations', 'enrollments', 'packages', 'programs', 
    'modules', 'courses', 'student_batches', 'education_levels'
];

foreach ($tables as $table) {
    try {
        $count = Capsule::table($table)->count();
        echo "✅ {$table}: {$count} records\n";
        
        // Check key columns for critical tables
        if ($table === 'registrations') {
            $columns = Capsule::select("SHOW COLUMNS FROM {$table}");
            $columnNames = array_column($columns, 'Field');
            echo "   Columns: " . implode(', ', $columnNames) . "\n";
        }
        
        if ($table === 'packages') {
            $modularPackages = Capsule::table($table)->where('package_type', 'modular')->count();
            echo "   Modular packages: {$modularPackages}\n";
        }
        
    } catch (Exception $e) {
        echo "❌ {$table}: Error - " . $e->getMessage() . "\n";
    }
}

echo "\n2. Checking Package-Program Relationships:\n";
try {
    $packages = Capsule::table('packages')
        ->where('package_type', 'modular')
        ->get();
    
    foreach ($packages as $package) {
        echo "📦 Package {$package->package_id}: {$package->package_name}\n";
        echo "   Program ID: {$package->program_id}\n";
        
        // Check if program exists
        $program = Capsule::table('programs')->where('program_id', $package->program_id)->first();
        if ($program) {
            echo "   ✅ Program exists: {$program->program_name}\n";
        } else {
            echo "   ❌ Program not found for ID: {$package->program_id}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking packages: " . $e->getMessage() . "\n";
}

echo "\n3. Checking Program-Module Relationships:\n";
try {
    $programs = Capsule::table('programs')->get();
    foreach ($programs as $program) {
        $moduleCount = Capsule::table('modules')->where('program_id', $program->program_id)->count();
        echo "🎓 Program {$program->program_id}: {$program->program_name} ({$moduleCount} modules)\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking programs: " . $e->getMessage() . "\n";
}

echo "\n4. Checking Education Levels:\n";
try {
    $educationLevels = Capsule::table('education_levels')->get();
    foreach ($educationLevels as $level) {
        $levelName = isset($level->name) ? $level->name : (isset($level->level_name) ? $level->level_name : 'Unknown');
        echo "🎓 {$level->id}: {$levelName}\n";
        if (isset($level->file_requirements) && $level->file_requirements) {
            $requirements = json_decode($level->file_requirements, true);
            if (is_array($requirements)) {
                echo "   Requirements: " . count($requirements) . " files\n";
            } else {
                echo "   Requirements: Invalid JSON format\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking education levels: " . $e->getMessage() . "\n";
}

echo "\n5. Test Data Validation Rules:\n";

// Simulate the form data that's being sent
$testFormData = [
    'user_firstname' => 'Modular_enrollment1@gmail.com',  // This looks wrong - should be a name, not email
    'user_lastname' => 'Modular_enrollment1@gmail.com',   // This looks wrong - should be a name, not email
    'email' => 'Modular_enrollment1@gmail.com',
    'password' => 'Modular_enrollment1@gmail.com',        // This looks wrong - should be a password
    'password_confirmation' => 'Modular_enrollment1@gmail.com',
    'package_id' => '18',
    'program_id' => '33',
    'learning_mode' => 'synchronous',
    'education_level' => 'Graduate',
    'enrollment_type' => 'Modular',
    'selected_modules' => '[{"id":46,"name":"Module 1 - Creation of Food2 courses selected","selected_courses":["11","14"]}]',
    'Start_Date' => '2025-07-19',
    'referral_code' => '',
    'plan_id' => '2'
];

echo "Testing validation with form data:\n";

// Check if referenced IDs exist
$packageExists = Capsule::table('packages')->where('package_id', $testFormData['package_id'])->exists();
echo "Package ID {$testFormData['package_id']} exists: " . ($packageExists ? '✅' : '❌') . "\n";

$programExists = Capsule::table('programs')->where('program_id', $testFormData['program_id'])->exists();
echo "Program ID {$testFormData['program_id']} exists: " . ($programExists ? '✅' : '❌') . "\n";

// Parse selected modules
$selectedModules = json_decode($testFormData['selected_modules'], true);
if ($selectedModules) {
    foreach ($selectedModules as $moduleData) {
        $moduleId = $moduleData['id'];
        $moduleExists = Capsule::table('modules')->where('modules_id', $moduleId)->exists();
        echo "Module ID {$moduleId} exists: " . ($moduleExists ? '✅' : '❌') . "\n";
        
        if (isset($moduleData['selected_courses'])) {
            foreach ($moduleData['selected_courses'] as $courseId) {
                $courseExists = Capsule::table('courses')->where('subject_id', $courseId)->exists();
                echo "  Course ID {$courseId} exists: " . ($courseExists ? '✅' : '❌') . "\n";
            }
        }
    }
}

// Check for potential field naming issues
echo "\n6. Potential Issues Found:\n";

if ($testFormData['user_firstname'] === $testFormData['email']) {
    echo "❌ CRITICAL: user_firstname is same as email - this suggests form field mapping issue\n";
}

if ($testFormData['password'] === $testFormData['email']) {
    echo "❌ CRITICAL: password is same as email - this suggests form field mapping issue\n";
}

if (empty($testFormData['referral_code'])) {
    echo "⚠️  referral_code is empty (this is ok if optional)\n";
}

// Check date format
$startDate = $testFormData['Start_Date'];
if (!DateTime::createFromFormat('Y-m-d', $startDate)) {
    echo "❌ CRITICAL: Start_Date format is invalid: {$startDate}\n";
} else {
    echo "✅ Start_Date format is valid: {$startDate}\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
