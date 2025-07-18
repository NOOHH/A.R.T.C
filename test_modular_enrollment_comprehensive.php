<?php
/**
 * Comprehensive Test for Modular Enrollment System
 * This file tests all aspects of the modular enrollment process
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\Package;
use App\Models\Program;
use App\Models\EducationLevel;
use App\Models\FormRequirement;

echo "<h1>Modular Enrollment System Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

// Test 1: Database Connection
echo "<div class='test-section'>";
echo "<h2>1. Database Connection Test</h2>";
try {
    DB::connection()->getPdo();
    echo "<p class='success'>✅ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit();
}
echo "</div>";

// Test 2: Check Required Tables
echo "<div class='test-section'>";
echo "<h2>2. Required Tables Check</h2>";
$requiredTables = [
    'users', 'students', 'registrations', 'enrollments', 'packages', 
    'programs', 'education_levels', 'form_requirements', 'student_batches'
];

foreach ($requiredTables as $table) {
    try {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        if ($exists) {
            $count = DB::table($table)->count();
            echo "<p class='success'>✅ Table '{$table}' exists with {$count} records</p>";
        } else {
            echo "<p class='error'>❌ Table '{$table}' does not exist</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error checking table '{$table}': " . $e->getMessage() . "</p>";
    }
}
echo "</div>";

// Test 3: Check Database Schema for Registration Table
echo "<div class='test-section'>";
echo "<h2>3. Registration Table Schema Check</h2>";
try {
    $columns = DB::getSchemaBuilder()->getColumnListing('registrations');
    $requiredColumns = [
        'id', 'user_id', 'firstname', 'lastname', 'email', 'package_id', 
        'program_id', 'learning_mode', 'enrollment_type', 'education_level',
        'selected_modules', 'status', 'created_at', 'updated_at'
    ];
    
    echo "<table>";
    echo "<tr><th>Required Column</th><th>Exists</th></tr>";
    foreach ($requiredColumns as $column) {
        $exists = in_array($column, $columns);
        $status = $exists ? "<span class='success'>✅ Yes</span>" : "<span class='error'>❌ No</span>";
        echo "<tr><td>{$column}</td><td>{$status}</td></tr>";
    }
    echo "</table>";
    
    echo "<h4>All columns in registrations table:</h4>";
    echo "<p class='info'>" . implode(', ', $columns) . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking registration table schema: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 4: Check Packages for Modular Enrollment
echo "<div class='test-section'>";
echo "<h2>4. Modular Packages Check</h2>";
try {
    $modularPackages = Package::where('package_type', 'modular')->get();
    if ($modularPackages->count() > 0) {
        echo "<p class='success'>✅ Found {$modularPackages->count()} modular packages</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Amount</th><th>Type</th><th>Program ID</th></tr>";
        foreach ($modularPackages as $package) {
            echo "<tr>";
            echo "<td>{$package->package_id}</td>";
            echo "<td>{$package->package_name}</td>";
            echo "<td>₱" . number_format($package->amount, 2) . "</td>";
            echo "<td>{$package->package_type}</td>";
            echo "<td>{$package->program_id}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No modular packages found. Please create some via admin panel.</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking modular packages: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 5: Check Programs
echo "<div class='test-section'>";
echo "<h2>5. Programs Check</h2>";
try {
    $programs = Program::where('is_archived', false)->get();
    if ($programs->count() > 0) {
        echo "<p class='success'>✅ Found {$programs->count()} active programs</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Has Packages</th></tr>";
        foreach ($programs as $program) {
            $hasPackages = $program->packages()->count() > 0;
            $packageStatus = $hasPackages ? "<span class='success'>✅ Yes</span>" : "<span class='warning'>⚠️ No</span>";
            echo "<tr>";
            echo "<td>{$program->program_id}</td>";
            echo "<td>{$program->program_name}</td>";
            echo "<td>" . ($program->program_description ?? 'N/A') . "</td>";
            echo "<td>{$packageStatus}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No active programs found. Please create some via admin panel.</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking programs: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 6: Check Education Levels
echo "<div class='test-section'>";
echo "<h2>6. Education Levels Check</h2>";
try {
    $educationLevels = EducationLevel::where('is_active', true)->get();
    if ($educationLevels->count() > 0) {
        echo "<p class='success'>✅ Found {$educationLevels->count()} active education levels</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Full Plan</th><th>Modular Plan</th><th>File Requirements</th></tr>";
        foreach ($educationLevels as $level) {
            $fullPlan = $level->available_full_plan ? "✅" : "❌";
            $modularPlan = $level->available_modular_plan ? "✅" : "❌";
            $fileReqs = $level->file_requirements ? "✅ Yes" : "❌ No";
            echo "<tr>";
            echo "<td>{$level->id}</td>";
            echo "<td>{$level->level_name}</td>";
            echo "<td>{$fullPlan}</td>";
            echo "<td>{$modularPlan}</td>";
            echo "<td>{$fileReqs}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No active education levels found. Please create some via admin panel.</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking education levels: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 7: Check Form Requirements
echo "<div class='test-section'>";
echo "<h2>7. Form Requirements Check</h2>";
try {
    $formRequirements = FormRequirement::where('is_active', true)
        ->where(function($q) {
            $q->where('program_type', 'modular')
              ->orWhere('program_type', 'both');
        })
        ->get();
    
    if ($formRequirements->count() > 0) {
        echo "<p class='success'>✅ Found {$formRequirements->count()} active form requirements for modular</p>";
        echo "<table>";
        echo "<tr><th>Field Name</th><th>Field Type</th><th>Label</th><th>Required</th><th>Section</th></tr>";
        foreach ($formRequirements as $req) {
            $required = $req->is_required ? "✅ Yes" : "❌ No";
            echo "<tr>";
            echo "<td>{$req->field_name}</td>";
            echo "<td>{$req->field_type}</td>";
            echo "<td>" . ($req->field_label ?? 'N/A') . "</td>";
            echo "<td>{$required}</td>";
            echo "<td>" . ($req->section_name ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No form requirements found for modular. Please create some via admin panel.</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking form requirements: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 8: Test Routes Accessibility
echo "<div class='test-section'>";
echo "<h2>8. Routes Accessibility Test</h2>";
$routes = [
    '/enrollment/modular' => 'Modular Enrollment Form',
    '/get-programs' => 'Get Programs API',
    '/check-email-availability' => 'Check Email Availability API',
    '/enrollment/send-otp' => 'Send OTP API',
    '/enrollment/verify-otp' => 'Verify OTP API',
    '/enrollment/validate-referral' => 'Validate Referral API'
];

foreach ($routes as $route => $description) {
    // Note: We can't test POST routes without proper setup, but we can check if they're defined
    echo "<p class='info'>🔗 {$description}: {$route} (route should be defined in web.php)</p>";
}
echo "</div>";

// Test 9: Test Sample Data Creation (Simulation)
echo "<div class='test-section'>";
echo "<h2>9. Sample Registration Data Simulation</h2>";
try {
    // This would be the data structure that should be saved
    $sampleRegistrationData = [
        'user_firstname' => 'Test',
        'user_lastname' => 'Student',
        'email' => 'test.student@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'program_id' => 1,
        'package_id' => 1,
        'learning_mode' => 'synchronous',
        'enrollment_type' => 'Modular',
        'education_level' => 'Undergraduate',
        'selected_modules' => '[{"id": 1, "name": "Module 1"}]',
        'Start_Date' => '2025-02-01',
        'referral_code' => 'PROF01TEST'
    ];

    echo "<p class='success'>✅ Sample registration data structure prepared</p>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    foreach ($sampleRegistrationData as $field => $value) {
        if ($field !== 'password' && $field !== 'password_confirmation') {
            echo "<tr><td>{$field}</td><td>{$value}</td></tr>";
        } else {
            echo "<tr><td>{$field}</td><td>[HIDDEN]</td></tr>";
        }
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error preparing sample data: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 10: Check Admin Settings for Referral
echo "<div class='test-section'>";
echo "<h2>10. Admin Settings Check</h2>";
try {
    $referralEnabled = DB::table('admin_settings')->where('setting_key', 'referral_enabled')->value('setting_value');
    $referralRequired = DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value');
    
    echo "<table>";
    echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";
    echo "<tr><td>Referral Enabled</td><td>" . ($referralEnabled ?? 'Not Set') . "</td><td>" . 
         ($referralEnabled === '1' ? "<span class='success'>✅ Enabled</span>" : "<span class='warning'>⚠️ Disabled</span>") . "</td></tr>";
    echo "<tr><td>Referral Required</td><td>" . ($referralRequired ?? 'Not Set') . "</td><td>" . 
         ($referralRequired === '1' ? "<span class='info'>📋 Required</span>" : "<span class='info'>📋 Optional</span>") . "</td></tr>";
    echo "</table>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking admin settings: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 11: Check for Missing Models
echo "<div class='test-section'>";
echo "<h2>11. Model Classes Check</h2>";
$modelClasses = [
    'App\Models\User',
    'App\Models\Student', 
    'App\Models\Registration',
    'App\Models\Enrollment',
    'App\Models\Package',
    'App\Models\Program',
    'App\Models\EducationLevel',
    'App\Models\FormRequirement',
    'App\Models\StudentBatch',
    'App\Models\RegistrationModule'
];

foreach ($modelClasses as $modelClass) {
    if (class_exists($modelClass)) {
        echo "<p class='success'>✅ Model {$modelClass} exists</p>";
    } else {
        echo "<p class='error'>❌ Model {$modelClass} does not exist</p>";
    }
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Test Summary</h2>";
echo "<p class='info'>🎯 <strong>Test completed!</strong> Review the results above to identify any issues with the modular enrollment system.</p>";
echo "<p class='info'>📝 <strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Fix any missing tables or columns identified above</li>";
echo "<li>Ensure packages and programs are created via admin panel</li>";
echo "<li>Test the actual enrollment form at <a href='/enrollment/modular'>/enrollment/modular</a></li>";
echo "<li>Verify OTP and referral validation functions work</li>";
echo "<li>Test form submission and database saving</li>";
echo "</ul>";
echo "</div>";
?>
