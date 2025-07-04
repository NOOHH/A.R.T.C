<?php

// Test file to verify education level field exists and dynamic form works
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FormRequirement;

echo "Testing Education Level Field Implementation\n";
echo "==========================================\n\n";

// Test 1: Check if education level field exists
echo "Test 1: Checking if education level field exists...\n";
$educationField = FormRequirement::where('field_name', 'education_level')->first();
if ($educationField) {
    echo "✓ Education level field found!\n";
    echo "  - Field Label: " . $educationField->field_label . "\n";
    echo "  - Field Type: " . $educationField->field_type . "\n";
    echo "  - Options: " . json_encode($educationField->field_options) . "\n";
    echo "  - Is Active: " . ($educationField->is_active ? 'Yes' : 'No') . "\n";
    echo "  - Is Required: " . ($educationField->is_required ? 'Yes' : 'No') . "\n\n";
} else {
    echo "✗ Education level field not found!\n\n";
}

// Test 2: Check all form requirements for complete program
echo "Test 2: Checking all form requirements for complete program...\n";
$completeRequirements = FormRequirement::active()->forProgram('complete')->ordered()->get();
echo "Found " . $completeRequirements->count() . " active requirements for complete program:\n";
foreach ($completeRequirements as $req) {
    echo "  - " . $req->field_name . " (" . $req->field_label . ")\n";
}
echo "\n";

// Test 3: Check modular program requirements
echo "Test 3: Checking all form requirements for modular program...\n";
$modularRequirements = FormRequirement::active()->forProgram('modular')->ordered()->get();
echo "Found " . $modularRequirements->count() . " active requirements for modular program:\n";
foreach ($modularRequirements as $req) {
    echo "  - " . $req->field_name . " (" . $req->field_label . ")\n";
}
echo "\n";

echo "Test completed!\n";
