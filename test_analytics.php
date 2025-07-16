<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Referral Analytics ===\n";

try {
    // Test the Referral model methods
    echo "\n1. Testing getMonthlyStats method:\n";
    $monthlyStats = \App\Models\Referral::getMonthlyStats(2025);
    echo "Monthly stats: " . json_encode($monthlyStats, JSON_PRETTY_PRINT) . "\n";

    echo "\n2. Testing getTopReferrers method:\n";
    $topReferrers = \App\Models\Referral::getTopReferrers(5);
    echo "Top referrers: " . json_encode($topReferrers, JSON_PRETTY_PRINT) . "\n";

    echo "\n3. Testing getOverallAnalytics method:\n";
    $overallStats = \App\Models\Referral::getOverallAnalytics();
    echo "Overall analytics: " . json_encode($overallStats, JSON_PRETTY_PRINT) . "\n";

    echo "\n4. Testing basic counts:\n";
    $totalReferrals = \App\Models\Referral::count();
    $totalThisMonth = \App\Models\Referral::whereMonth('used_at', now()->month)
                                          ->whereYear('used_at', now()->year)
                                          ->count();
    
    echo "Total referrals: $totalReferrals\n";
    echo "This month: $totalThisMonth\n";

    echo "\n✅ All analytics methods working correctly!\n";

} catch (Exception $e) {
    echo "❌ Error testing analytics: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
