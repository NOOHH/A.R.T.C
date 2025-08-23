<?php

// Debug the exact path and pattern matching
echo "🔍 Debug Authentication Middleware Pattern Matching\n";
echo "===================================================\n\n";

// Test the path pattern matching
$testPaths = [
    't/draft/smartprep/admin-dashboard',
    't/draft/smartprep/admin/students',
    't/draft/test1/admin-dashboard',
    't/draft/test1/admin/professors',
    'admin-dashboard',
    'admin/students',
];

foreach ($testPaths as $path) {
    echo "🧪 Testing path: '$path'\n";
    
    // Check the current patterns
    $startsWithTDraft = str_starts_with($path, 't/draft/');
    $containsAdmin = str_contains($path, '/admin/');
    $containsAdminDashboard = str_contains($path, '/admin-dashboard');
    
    echo "   - str_starts_with('t/draft/'): " . ($startsWithTDraft ? '✅ YES' : '❌ NO') . "\n";
    echo "   - str_contains('/admin/'): " . ($containsAdmin ? '✅ YES' : '❌ NO') . "\n";
    echo "   - str_contains('/admin-dashboard'): " . ($containsAdminDashboard ? '✅ YES' : '❌ NO') . "\n";
    
    $shouldBypass = $startsWithTDraft && ($containsAdmin || $containsAdminDashboard);
    echo "   - SHOULD BYPASS AUTH: " . ($shouldBypass ? '✅ YES' : '❌ NO') . "\n\n";
}

echo "✅ Pattern analysis complete!\n";
