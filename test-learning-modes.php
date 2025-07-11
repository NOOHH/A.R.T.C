<?php

// Test script to verify learning mode functionality
// This script tests the Plan model and learning mode configuration

require_once 'vendor/autoload.php';

use App\Models\Plan;

// Test Plan model functionality
echo "=== Testing Plan Model Learning Mode Configuration ===\n";

// Get Full Plan
$fullPlan = Plan::where('plan_id', 1)->first();
if ($fullPlan) {
    echo "Full Plan found:\n";
    echo "- ID: {$fullPlan->plan_id}\n";
    echo "- Name: {$fullPlan->plan_name}\n";
    echo "- Enable Synchronous: " . ($fullPlan->enable_synchronous ? 'Yes' : 'No') . "\n";
    echo "- Enable Asynchronous: " . ($fullPlan->enable_asynchronous ? 'Yes' : 'No') . "\n";
    echo "- Available Learning Modes: " . implode(', ', $fullPlan->getAvailableLearningModes()) . "\n";
    echo "- Is Synchronous Enabled: " . ($fullPlan->isLearningModeEnabled('synchronous') ? 'Yes' : 'No') . "\n";
    echo "- Is Asynchronous Enabled: " . ($fullPlan->isLearningModeEnabled('asynchronous') ? 'Yes' : 'No') . "\n";
} else {
    echo "Full Plan not found!\n";
}

echo "\n";

// Get Modular Plan
$modularPlan = Plan::where('plan_id', 2)->first();
if ($modularPlan) {
    echo "Modular Plan found:\n";
    echo "- ID: {$modularPlan->plan_id}\n";
    echo "- Name: {$modularPlan->plan_name}\n";
    echo "- Enable Synchronous: " . ($modularPlan->enable_synchronous ? 'Yes' : 'No') . "\n";
    echo "- Enable Asynchronous: " . ($modularPlan->enable_asynchronous ? 'Yes' : 'No') . "\n";
    echo "- Available Learning Modes: " . implode(', ', $modularPlan->getAvailableLearningModes()) . "\n";
    echo "- Is Synchronous Enabled: " . ($modularPlan->isLearningModeEnabled('synchronous') ? 'Yes' : 'No') . "\n";
    echo "- Is Asynchronous Enabled: " . ($modularPlan->isLearningModeEnabled('asynchronous') ? 'Yes' : 'No') . "\n";
} else {
    echo "Modular Plan not found!\n";
}

echo "\n=== Test Complete ===\n";
