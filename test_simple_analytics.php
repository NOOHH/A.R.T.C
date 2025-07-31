<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SIMPLE ANALYTICS TEST ===\n";

try {
    echo "1. Testing Controller Instantiation...\n";
    $controller = new App\Http\Controllers\AdminAnalyticsController();
    echo "   ✅ AdminAnalyticsController created\n";
    
    $boardController = new App\Http\Controllers\BoardPassersController();
    echo "   ✅ BoardPassersController created\n";
    
    echo "\n2. Testing Database...\n";
    $payments = DB::table('payments')->count();
    echo "   ✅ Payments table: $payments records\n";
    
    $students = DB::table('students')->count();
    echo "   ✅ Students table: $students records\n";
    
    $boardPassers = DB::table('board_passers')->count();
    echo "   ✅ Board passers table: $boardPassers records\n";
    
    echo "\n3. Testing Recent Payments Query...\n";
    $recentPayments = DB::table('payments')
        ->join('students', 'payments.student_id', '=', 'students.student_id')
        ->join('users', 'students.user_id', '=', 'users.user_id')
        ->select('payments.student_id', 'users.user_firstname', 'users.user_lastname', 'payments.amount')
        ->limit(3)
        ->get();
    
    echo "   ✅ Recent payments query returned " . $recentPayments->count() . " results\n";
    foreach ($recentPayments as $payment) {
        echo "   - Student: " . $payment->user_firstname . " " . $payment->user_lastname . " ($" . $payment->amount . ")\n";
    }
    
    echo "\n4. Testing Board Passers Model...\n";
    try {
        $passers = App\Models\BoardPasser::limit(3)->get();
        echo "   ✅ BoardPasser model works, found " . $passers->count() . " records\n";
    } catch (Exception $e) {
        echo "   ⚠️  BoardPasser model issue: " . $e->getMessage() . "\n";
    }
    
    echo "\n5. Testing View Files...\n";
    $views = [
        'resources/views/admin/admin-analytics/admin-analytics.blade.php',
        'resources/views/admin/board-passers/index.blade.php'
    ];
    
    foreach ($views as $view) {
        if (file_exists($view)) {
            echo "   ✅ $view exists\n";
        } else {
            echo "   ❌ $view missing\n";
        }
    }
    
    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
