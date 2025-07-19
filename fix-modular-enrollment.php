<?php

// Bootstrap Laravel
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "=== FIXING MODULAR ENROLLMENT ISSUES ===\n\n";

// 1. Check and fix education levels
echo "1. Checking Education Levels:\n";
$educationLevels = DB::table('education_levels')->get();

foreach ($educationLevels as $level) {
    echo "Level ID: {$level->id}\n";
    
    // Check if it has a name field or different field name
    $fields = get_object_vars($level);
    foreach ($fields as $key => $value) {
        if (in_array($key, ['name', 'level_name', 'education_level'])) {
            echo "  Name field '{$key}': {$value}\n";
        }
        if ($key === 'file_requirements') {
            if ($value) {
                $decoded = json_decode($value, true);
                if ($decoded === null) {
                    echo "  ❌ Invalid JSON in file_requirements: {$value}\n";
                    
                    // Try to fix common JSON issues
                    $fixed = str_replace("'", '"', $value); // Replace single quotes with double quotes
                    $fixed = preg_replace('/(\w+)=/', '"$1":', $fixed); // Fix key=value to "key":value
                    
                    $decodedFixed = json_decode($fixed, true);
                    if ($decodedFixed !== null) {
                        echo "  ✅ Fixed JSON format\n";
                        DB::table('education_levels')
                            ->where('id', $level->id)
                            ->update(['file_requirements' => json_encode($decodedFixed)]);
                        echo "  ✅ Updated in database\n";
                    }
                } else {
                    echo "  ✅ Valid JSON with " . count($decoded) . " requirements\n";
                }
            } else {
                echo "  ⚠️  No file requirements\n";
            }
        }
    }
    echo "\n";
}

// 2. Check if all required database columns exist
echo "2. Checking Required Database Columns:\n";

$requiredColumns = [
    'registrations' => ['registration_id', 'user_id', 'program_id', 'package_id', 'education_level', 'Start_Date', 'learning_mode', 'enrollment_type', 'selected_modules'],
    'enrollments' => ['enrollment_id', 'user_id', 'program_id', 'package_id', 'learning_mode', 'enrollment_type', 'Start_Date', 'education_level'],
];

foreach ($requiredColumns as $table => $columns) {
    echo "Checking table: {$table}\n";
    
    try {
        $tableColumns = DB::select("SHOW COLUMNS FROM {$table}");
        $existingColumns = array_column($tableColumns, 'Field');
        
        foreach ($columns as $column) {
            if (in_array($column, $existingColumns)) {
                echo "  ✅ {$column}\n";
            } else {
                echo "  ❌ {$column} - MISSING!\n";
            }
        }
    } catch (Exception $e) {
        echo "  ❌ Error checking {$table}: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// 3. Test form submission data validation
echo "3. Testing Form Data Validation:\n";

$testData = [
    'user_firstname' => 'John',
    'user_lastname' => 'Doe',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'package_id' => '18',
    'program_id' => '33',
    'learning_mode' => 'synchronous',
    'education_level' => 'Graduate',
    'enrollment_type' => 'Modular',
    'selected_modules' => '[{"id":46,"name":"Module 1 - Creation of Food","selected_courses":["11","14"]}]',
    'Start_Date' => date('Y-m-d'),
    'referral_code' => '',
    'plan_id' => '2'
];

// Basic validation rules from the controller
$validationRules = [
    'program_id' => 'required|exists:programs,program_id',
    'package_id' => 'required|exists:packages,package_id',
    'learning_mode' => 'required|in:synchronous,asynchronous',
    'selected_modules' => 'required|string',
    'education_level' => 'required|string',
    'Start_Date' => 'required|date',
    'enrollment_type' => 'required|in:Modular',
];

foreach ($validationRules as $field => $rule) {
    $value = $testData[$field] ?? null;
    $rules = explode('|', $rule);
    
    echo "Field: {$field} = '{$value}'\n";
    
    foreach ($rules as $singleRule) {
        if ($singleRule === 'required') {
            echo "  " . (empty($value) ? "❌" : "✅") . " Required check\n";
        } elseif (strpos($singleRule, 'exists:') === 0) {
            [$table, $column] = explode(',', substr($singleRule, 7));
            $exists = DB::table($table)->where($column, $value)->exists();
            echo "  " . ($exists ? "✅" : "❌") . " Exists in {$table}.{$column}\n";
        } elseif (strpos($singleRule, 'in:') === 0) {
            $allowedValues = explode(',', substr($singleRule, 3));
            $isValid = in_array($value, $allowedValues);
            echo "  " . ($isValid ? "✅" : "❌") . " In allowed values: " . implode(', ', $allowedValues) . "\n";
        }
    }
    echo "\n";
}

echo "=== FIX COMPLETE ===\n";
