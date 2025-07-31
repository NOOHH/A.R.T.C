<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the whitelist functionality
try {
    echo 'Testing whitelist functionality...' . PHP_EOL;
    
    // Test empty whitelist
    $emptyWhitelist = '';
    echo "Empty whitelist: '$emptyWhitelist'" . PHP_EOL;
    
    if (!empty($emptyWhitelist) && trim($emptyWhitelist) !== '') {
        $whitelistedIds = array_filter(array_map('trim', explode(',', $emptyWhitelist)), function($id) {
            return !empty($id) && $id !== '';
        });
        echo "Should not get here with empty whitelist" . PHP_EOL;
    } else {
        echo "✓ Empty whitelist passes (allows all professors)" . PHP_EOL;
    }
    
    // Test whitelist with actual IDs
    $realWhitelist = '1,2,3';
    echo "Real whitelist: '$realWhitelist'" . PHP_EOL;
    
    if (!empty($realWhitelist) && trim($realWhitelist) !== '') {
        $whitelistedIds = array_filter(array_map('trim', explode(',', $realWhitelist)), function($id) {
            return !empty($id) && $id !== '';
        });
        echo "Parsed IDs: " . print_r($whitelistedIds, true) . PHP_EOL;
        
        $testProfessorId = '2';
        if (!empty($whitelistedIds) && !in_array((string)$testProfessorId, $whitelistedIds)) {
            echo "✗ Professor $testProfessorId should be allowed but was blocked" . PHP_EOL;
        } else {
            echo "✓ Professor $testProfessorId is in whitelist and allowed" . PHP_EOL;
        }
        
        $testProfessorId2 = '5';
        if (!empty($whitelistedIds) && !in_array((string)$testProfessorId2, $whitelistedIds)) {
            echo "✓ Professor $testProfessorId2 correctly blocked (not in whitelist)" . PHP_EOL;
        } else {
            echo "✗ Professor $testProfessorId2 should be blocked but was allowed" . PHP_EOL;
        }
    }
    
    // Test whitelist with empty strings
    $badWhitelist = ',,,';
    echo "Bad whitelist: '$badWhitelist'" . PHP_EOL;
    
    if (!empty($badWhitelist) && trim($badWhitelist) !== '') {
        $whitelistedIds = array_filter(array_map('trim', explode(',', $badWhitelist)), function($id) {
            return !empty($id) && $id !== '';
        });
        
        if (empty($whitelistedIds)) {
            echo "✓ Bad whitelist treated as empty (allows all professors)" . PHP_EOL;
        } else {
            echo "✗ Bad whitelist should be treated as empty" . PHP_EOL;
        }
    }
    
    echo "Whitelist functionality test completed!" . PHP_EOL;
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo 'Stack trace: ' . $e->getTraceAsString() . PHP_EOL;
}
