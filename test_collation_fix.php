<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING COLLATION FIX ===\n";

try {
    echo "1. Testing Payments Without JOIN...\n";
    $payments = DB::table('payments')
        ->select('student_id', 'amount', 'created_at')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    echo "   ✅ Found " . $payments->count() . " payments\n";
    
    echo "\n2. Testing Student Lookup Separately...\n";
    foreach ($payments as $payment) {
        echo "   Payment: Student " . $payment->student_id . " - $" . $payment->amount . "\n";
        
        // Get student info separately to avoid collation issues
        try {
            $student = DB::select("
                SELECT u.user_firstname, u.user_lastname 
                FROM students s 
                JOIN users u ON s.user_id = u.user_id 
                WHERE s.student_id = ?
            ", [$payment->student_id]);
            
            if (!empty($student)) {
                echo "   - Student: " . $student[0]->user_firstname . " " . $student[0]->user_lastname . "\n";
            } else {
                echo "   - Student: Not found\n";
            }
        } catch (Exception $e) {
            echo "   - Student lookup error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n3. Testing Alternative Approach - Raw Query...\n";
    try {
        $results = DB::select("
            SELECT p.student_id, p.amount, p.created_at,
                   u.user_firstname, u.user_lastname,
                   pr.program_name
            FROM payments p
            LEFT JOIN students s ON CAST(p.student_id AS CHAR) = CAST(s.student_id AS CHAR)
            LEFT JOIN users u ON s.user_id = u.user_id
            LEFT JOIN enrollments e ON CAST(s.student_id AS CHAR) = CAST(e.student_id AS CHAR)
            LEFT JOIN programs pr ON e.program_id = pr.program_id
            WHERE p.payment_status IN ('paid', 'approved', 'verified')
            ORDER BY p.created_at DESC
            LIMIT 5
        ");
        
        echo "   ✅ Raw query returned " . count($results) . " results:\n";
        foreach ($results as $result) {
            $name = trim(($result->user_firstname ?? '') . ' ' . ($result->user_lastname ?? ''));
            if (empty($name)) $name = 'Unknown Student';
            $program = $result->program_name ?? 'Unknown Program';
            echo "   - $name ($result->student_id) - $program - \$$result->amount\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Raw query failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== COLLATION TEST COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
