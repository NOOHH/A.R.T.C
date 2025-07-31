<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== COMPREHENSIVE A.R.T.C SYSTEM TESTING ===" . PHP_EOL;
echo "Testing: Routes, Database, Controllers, Auth, Sessions, Storage, Certificates" . PHP_EOL . PHP_EOL;

$results = [];

// 1. Test Route Registration
echo "1. TESTING ROUTES..." . PHP_EOL;
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    
    $criticalRoutes = [
        'admin.students.archived',
        'admin.students.index', 
        'admin.students.export',
        'admin.certificates',
        'certificate.show',
        'certificate.download'
    ];
    
    foreach ($criticalRoutes as $routeName) {
        if ($routes->hasNamedRoute($routeName)) {
            echo "   ✅ Route '{$routeName}' registered" . PHP_EOL;
            $results['routes'][$routeName] = 'OK';
        } else {
            echo "   ❌ Route '{$routeName}' missing" . PHP_EOL;
            $results['routes'][$routeName] = 'MISSING';
        }
    }
} catch (Exception $e) {
    echo "   ❌ Route testing failed: " . $e->getMessage() . PHP_EOL;
    $results['routes'] = 'FAILED';
}

// 2. Test Database Connection and Tables
echo PHP_EOL . "2. TESTING DATABASE..." . PHP_EOL;
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "   ✅ Database connection successful" . PHP_EOL;
    $results['database']['connection'] = 'OK';
    
    $tables = ['students', 'enrollments', 'programs', 'users', 'certificates'];
    foreach ($tables as $table) {
        try {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            echo "   ✅ Table '{$table}' exists with {$count} records" . PHP_EOL;
            $results['database']['tables'][$table] = $count;
        } catch (Exception $e) {
            echo "   ❌ Table '{$table}' missing or error: " . $e->getMessage() . PHP_EOL;
            $results['database']['tables'][$table] = 'ERROR';
        }
    }
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . PHP_EOL;
    $results['database'] = 'FAILED';
}

// 3. Test Controllers
echo PHP_EOL . "3. TESTING CONTROLLERS..." . PHP_EOL;
try {
    // Test CertificateController
    $certificateController = new \App\Http\Controllers\CertificateController();
    echo "   ✅ CertificateController instantiated" . PHP_EOL;
    $results['controllers']['CertificateController'] = 'OK';
    
    // Test AdminStudentListController
    $studentController = new \App\Http\Controllers\AdminStudentListController();
    echo "   ✅ AdminStudentListController instantiated" . PHP_EOL;
    $results['controllers']['AdminStudentListController'] = 'OK';
    
} catch (Exception $e) {
    echo "   ❌ Controller testing failed: " . $e->getMessage() . PHP_EOL;
    $results['controllers'] = 'FAILED';
}

// 4. Test Progress Calculation System
echo PHP_EOL . "4. TESTING PROGRESS CALCULATION..." . PHP_EOL;
try {
    // Find a student with enrollments
    $student = \Illuminate\Support\Facades\DB::table('students')
        ->join('enrollments', 'students.student_id', '=', 'enrollments.student_id')
        ->select('students.*', 'enrollments.progress_percentage', 'enrollments.enrollment_status')
        ->first();
    
    if ($student) {
        echo "   ✅ Found student for testing: {$student->firstname} {$student->lastname}" . PHP_EOL;
        echo "   ✅ Current progress: {$student->progress_percentage}%" . PHP_EOL;
        echo "   ✅ Enrollment status: {$student->enrollment_status}" . PHP_EOL;
        
        // Test if student is eligible for certificate
        $isEligible = $student->progress_percentage >= 80 || $student->enrollment_status === 'completed';
        echo "   " . ($isEligible ? "✅" : "⚠️") . " Certificate eligibility: " . ($isEligible ? "ELIGIBLE" : "NOT ELIGIBLE") . PHP_EOL;
        
        $results['progress']['sample_student'] = [
            'name' => $student->firstname . ' ' . $student->lastname,
            'progress' => $student->progress_percentage,
            'status' => $student->enrollment_status,
            'eligible' => $isEligible
        ];
    } else {
        echo "   ⚠️ No students with enrollments found for testing" . PHP_EOL;
        $results['progress'] = 'NO_DATA';
    }
} catch (Exception $e) {
    echo "   ❌ Progress calculation testing failed: " . $e->getMessage() . PHP_EOL;
    $results['progress'] = 'FAILED';
}

// 5. Test Storage and File System
echo PHP_EOL . "5. TESTING STORAGE..." . PHP_EOL;
try {
    $storagePath = storage_path('app');
    if (is_writable($storagePath)) {
        echo "   ✅ Storage directory writable: {$storagePath}" . PHP_EOL;
        $results['storage']['writable'] = 'OK';
        
        // Test certificate generation directory
        $certificatePath = storage_path('app/certificates');
        if (!file_exists($certificatePath)) {
            mkdir($certificatePath, 0755, true);
            echo "   ✅ Created certificates directory" . PHP_EOL;
        } else {
            echo "   ✅ Certificates directory exists" . PHP_EOL;
        }
        $results['storage']['certificates_dir'] = 'OK';
        
    } else {
        echo "   ❌ Storage directory not writable: {$storagePath}" . PHP_EOL;
        $results['storage'] = 'NOT_WRITABLE';
    }
} catch (Exception $e) {
    echo "   ❌ Storage testing failed: " . $e->getMessage() . PHP_EOL;
    $results['storage'] = 'FAILED';
}

// 6. Test Certificate Template
echo PHP_EOL . "6. TESTING CERTIFICATE TEMPLATE..." . PHP_EOL;
try {
    $templatePath = resource_path('views/components/certificate.blade.php');
    if (file_exists($templatePath)) {
        echo "   ✅ Certificate template exists: {$templatePath}" . PHP_EOL;
        $templateSize = filesize($templatePath);
        echo "   ✅ Template size: {$templateSize} bytes" . PHP_EOL;
        $results['certificate']['template'] = 'OK';
    } else {
        echo "   ❌ Certificate template missing: {$templatePath}" . PHP_EOL;
        $results['certificate']['template'] = 'MISSING';
    }
    
    // Check certificate view
    $indexPath = resource_path('views/admin/certificates/index.blade.php');
    if (file_exists($indexPath)) {
        echo "   ✅ Certificate management view exists" . PHP_EOL;
        $results['certificate']['management_view'] = 'OK';
    } else {
        echo "   ❌ Certificate management view missing" . PHP_EOL;
        $results['certificate']['management_view'] = 'MISSING';
    }
} catch (Exception $e) {
    echo "   ❌ Certificate template testing failed: " . $e->getMessage() . PHP_EOL;
    $results['certificate'] = 'FAILED';
}

// 7. Test Auto Progress Update System
echo PHP_EOL . "7. TESTING AUTO PROGRESS UPDATE..." . PHP_EOL;
try {
    // Check if calculateStudentProgress method exists
    $controller = new \App\Http\Controllers\CertificateController();
    $reflection = new ReflectionClass($controller);
    
    if ($reflection->hasMethod('calculateStudentProgress')) {
        echo "   ✅ calculateStudentProgress method exists" . PHP_EOL;
        $results['auto_progress']['method'] = 'OK';
    } else {
        echo "   ❌ calculateStudentProgress method missing" . PHP_EOL;
        $results['auto_progress']['method'] = 'MISSING';
    }
    
    // Check if auto-completion logic exists
    $filePath = app_path('Http/Controllers/CertificateController.php');
    $content = file_get_contents($filePath);
    
    if (strpos($content, 'progress_percentage') !== false) {
        echo "   ✅ Progress percentage logic found" . PHP_EOL;
        $results['auto_progress']['progress_logic'] = 'OK';
    } else {
        echo "   ❌ Progress percentage logic missing" . PHP_EOL;
        $results['auto_progress']['progress_logic'] = 'MISSING';
    }
    
} catch (Exception $e) {
    echo "   ❌ Auto progress testing failed: " . $e->getMessage() . PHP_EOL;
    $results['auto_progress'] = 'FAILED';
}

// 8. Final System Health Check
echo PHP_EOL . "8. SYSTEM HEALTH SUMMARY..." . PHP_EOL;
$totalTests = 0;
$passedTests = 0;

foreach ($results as $category => $result) {
    if (is_array($result)) {
        foreach ($result as $test => $status) {
            $totalTests++;
            if ($status === 'OK' || is_numeric($status)) {
                $passedTests++;
            }
        }
    } else {
        $totalTests++;
        if ($result === 'OK') {
            $passedTests++;
        }
    }
}

$healthScore = ($passedTests / $totalTests) * 100;
echo "   📊 System Health Score: {$healthScore}% ({$passedTests}/{$totalTests} tests passed)" . PHP_EOL;

if ($healthScore >= 90) {
    echo "   🎉 SYSTEM STATUS: EXCELLENT" . PHP_EOL;
} elseif ($healthScore >= 75) {
    echo "   ✅ SYSTEM STATUS: GOOD" . PHP_EOL;
} elseif ($healthScore >= 50) {
    echo "   ⚠️ SYSTEM STATUS: NEEDS ATTENTION" . PHP_EOL;
} else {
    echo "   ❌ SYSTEM STATUS: CRITICAL ISSUES" . PHP_EOL;
}

echo PHP_EOL . "=== TESTING COMPLETED ===" . PHP_EOL;
echo "Full results saved in system memory for debugging." . PHP_EOL;

// Save detailed results
file_put_contents('system_test_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Detailed results saved to: system_test_results.json" . PHP_EOL;
