<?php

/**
 * Simple Analytics Data Extraction Test
 * Extracts and validates the analytics numbers displayed on dashboard
 */

function extractAnalyticsFromDashboard($tenant) {
    $url = "http://127.0.0.1:8000/t/draft/{$tenant}/admin-dashboard";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return ["error" => "HTTP $httpCode"];
    }
    
    $analytics = [];
    
    // Extract analytics numbers using regex
    $patterns = [
        'total_students' => '/<div class="analytics-card students">.*?<div class="analytics-number">(\d+)<\/div>/s',
        'total_programs' => '/<div class="analytics-card programs">.*?<div class="analytics-number">(\d+)<\/div>/s',
        'total_modules' => '/<div class="analytics-card modules">.*?<div class="analytics-number">(\d+)<\/div>/s',
        'total_enrollments' => '/<div class="analytics-card enrollments">.*?<div class="analytics-number">(\d+)<\/div>/s',
    ];
    
    foreach ($patterns as $key => $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            $analytics[$key] = (int)$matches[1];
        } else {
            $analytics[$key] = 'Not found';
        }
    }
    
    return $analytics;
}

echo "=== Dashboard Analytics Extraction Test ===\n\n";

$tenants = ['test2', 'artc'];

foreach ($tenants as $tenant) {
    echo "Tenant: $tenant\n";
    echo str_repeat("-", 30) . "\n";
    
    $analytics = extractAnalyticsFromDashboard($tenant);
    
    if (isset($analytics['error'])) {
        echo "❌ Error: {$analytics['error']}\n";
    } else {
        foreach ($analytics as $metric => $value) {
            echo "  " . str_pad(ucfirst(str_replace('_', ' ', $metric)), 20) . ": $value\n";
        }
        
        // Show expected vs actual for test2
        if ($tenant === 'test2') {
            echo "\n  Expected vs Actual:\n";
            $expected = [
                'total_students' => 5,
                'total_programs' => 3,
                'total_modules' => 10,
                'total_enrollments' => 5
            ];
            
            foreach ($expected as $metric => $expectedValue) {
                $actualValue = $analytics[$metric];
                $status = ($actualValue == $expectedValue) ? "✅" : "❌";
                echo "    " . str_pad(ucfirst(str_replace('_', ' ', $metric)), 18) . ": Expected $expectedValue, Got $actualValue $status\n";
            }
        }
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "If you see numbers other than the expected values for test2,\n";
echo "then the tenant-aware analytics system is working correctly!\n";
echo "\nPrevious hardcoded values were: Students=156, Programs=8, Modules=24, Enrollments=342\n";
echo "New tenant-specific values should reflect actual database content.\n";
