<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "Testing Analytics Fixes...\n";

try {
    $controller = new App\Http\Controllers\AdminAnalyticsController();
    $request = new Illuminate\Http\Request();
    $filters = [];

    echo "\n1. Testing Board Passers...\n";
    $boardPassers = $controller->getBoardPassers($filters);
    echo "Board Passers found: " . count($boardPassers) . "\n";
    if (!empty($boardPassers)) {
        $sample = $boardPassers[0];
        echo "Sample: " . $sample['full_name'] . " - " . $sample['program_name'] . " - " . $sample['result'] . "\n";
    }

    echo "\n2. Testing Recently Completed...\n";
    $recentlyCompleted = $controller->getRecentlyCompleted($filters);
    echo "Recently Completed found: " . count($recentlyCompleted) . "\n";
    if (!empty($recentlyCompleted)) {
        $sample = $recentlyCompleted[0];
        echo "Sample: " . $sample['name'] . " - " . $sample['program'] . " - " . $sample['final_score'] . "\n";
    }

    echo "\n3. Testing Recent Payments...\n";
    $recentPayments = $controller->getRecentPayments($filters);
    echo "Recent Payments found: " . count($recentPayments) . "\n";
    if (!empty($recentPayments)) {
        $sample = $recentPayments[0];
        echo "Sample: " . $sample['student_name'] . " - " . $sample['amount'] . " - " . $sample['status'] . "\n";
    }

} catch (Exception $e) {
    echo "Error testing analytics: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
