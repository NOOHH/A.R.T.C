<?php

// Test dynamic registration form improvements
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FormRequirement;
use App\Models\Module;
use Illuminate\Support\Facades\Schema;

echo "Testing Dynamic Registration Form Improvements\n";
echo "===============================================\n\n";

// Test 1: Check sort_order functionality
echo "Test 1: Checking sort_order functionality...\n";
$requirements = FormRequirement::active()->ordered()->get();
echo "Found " . $requirements->count() . " requirements in sorted order:\n";
foreach ($requirements as $req) {
    echo "  - {$req->sort_order}: {$req->field_name} ({$req->field_label})\n";
}
echo "\n";

// Test 2: Check field_label vs field_name separation
echo "Test 2: Checking field_label vs field_name separation...\n";
$educationField = FormRequirement::where('field_name', 'education_level')->first();
if ($educationField) {
    echo "✓ Education Level field:\n";
    echo "  - field_name: {$educationField->field_name}\n";
    echo "  - field_label: {$educationField->field_label}\n";
    echo "  - Different labels: " . ($educationField->field_name !== $educationField->field_label ? 'Yes' : 'No') . "\n";
}
echo "\n";

// Test 3: Check module selection functionality
echo "Test 3: Checking module selection functionality...\n";
$moduleField = FormRequirement::where('field_name', 'selected_modules')->first();
if ($moduleField) {
    echo "✓ Module selection field found:\n";
    echo "  - field_type: {$moduleField->field_type}\n";
    echo "  - program_type: {$moduleField->program_type}\n";
    echo "  - is_active: " . ($moduleField->is_active ? 'Yes' : 'No') . "\n";
} else {
    echo "✗ Module selection field not found\n";
}

// Check available modules
$moduleCount = Module::where('is_archived', false)->count();
echo "Available modules: {$moduleCount}\n";

// Test 4: Check registration_modules table
echo "\nTest 4: Checking registration_modules pivot table...\n";
$tableExists = Schema::hasTable('registration_modules');
echo "Table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";

if ($tableExists) {
    $columns = Schema::getColumnListing('registration_modules');
    echo "Columns: " . implode(', ', $columns) . "\n";
}

echo "\nTest completed!\n";
