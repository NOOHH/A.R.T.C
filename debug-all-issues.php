<?php
/*
 * Comprehensive Debug Script for A.R.T.C Issues
 * - Modular enrollment course selection issues
 * - File upload persistence issues  
 * - Batch capacity update issues
 */

require_once 'vendor/autoload.php';

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== A.R.T.C COMPREHENSIVE DEBUG ===\n\n";

    // 1. CHECK DATABASE CONNECTION
    echo "1. DATABASE CONNECTION:\n";
    try {
        $connection = DB::connection();
        $connection->getPdo();
        echo "✅ Database connected successfully\n";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
        exit(1);
    }

    // 2. CHECK ENROLLMENT_COURSES TABLE
    echo "\n2. ENROLLMENT_COURSES TABLE:\n";
    try {
        $schema = DB::select("DESCRIBE enrollment_courses");
        echo "✅ enrollment_courses table exists:\n";
        foreach ($schema as $col) {
            echo "  - {$col->Field} ({$col->Type})\n";
        }
        
        $count = DB::table('enrollment_courses')->count();
        echo "✅ Total enrollment_courses records: {$count}\n";
        
        // Check recent enrollment courses
        $recentCourses = DB::table('enrollment_courses')
            ->join('courses', 'enrollment_courses.course_id', '=', 'courses.subject_id')
            ->join('modules', 'enrollment_courses.module_id', '=', 'modules.modules_id')
            ->select('enrollment_courses.*', 'courses.subject_name', 'modules.module_name')
            ->orderBy('enrollment_courses.created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($recentCourses as $course) {
            echo "  - Enrollment {$course->enrollment_id}: {$course->subject_name} ({$course->module_name})\n";
        }
        
    } catch (Exception $e) {
        echo "❌ enrollment_courses table check failed: " . $e->getMessage() . "\n";
    }

    // 3. CHECK REGISTRATIONS TABLE FOR COURSE DATA
    echo "\n3. REGISTRATIONS TABLE COURSE DATA:\n";
    try {
        $recentRegs = DB::table('registrations')
            ->select('registration_id', 'firstname', 'lastname', 'selected_courses', 'selected_modules', 'enrollment_type')
            ->where('enrollment_type', 'Modular')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($recentRegs as $reg) {
            echo "  - REG {$reg->registration_id}: {$reg->firstname} {$reg->lastname}\n";
            echo "    Selected Courses: " . ($reg->selected_courses ?: 'NULL') . "\n";
            echo "    Selected Modules: " . ($reg->selected_modules ?: 'NULL') . "\n";
            
            // Try to decode JSON
            if ($reg->selected_courses) {
                $courses = json_decode($reg->selected_courses, true);
                if ($courses) {
                    echo "    Parsed Courses: " . print_r($courses, true) . "\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "❌ Registrations check failed: " . $e->getMessage() . "\n";
    }

    // 4. CHECK FILE UPLOAD STORAGE
    echo "\n4. FILE UPLOAD STORAGE:\n";
    $uploadPath = storage_path('app/public/uploads/education_requirements');
    if (file_exists($uploadPath)) {
        $files = scandir($uploadPath);
        $fileCount = count($files) - 2; // exclude . and ..
        echo "✅ Upload directory exists: {$uploadPath}\n";
        echo "✅ Files in directory: {$fileCount}\n";
        
        // Show recent files
        $recentFiles = array_slice(array_diff($files, ['.', '..']), -5);
        foreach ($recentFiles as $file) {
            echo "  - {$file}\n";
        }
    } else {
        echo "❌ Upload directory does not exist: {$uploadPath}\n";
    }

    // Check if files are referenced in registrations
    echo "\n5. FILE REFERENCES IN REGISTRATIONS:\n";
    try {
        $regsWithFiles = DB::table('registrations')
            ->whereNotNull('PSA')
            ->orWhereNotNull('good_moral')
            ->orWhereNotNull('TOR')
            ->orWhereNotNull('Course_Cert')
            ->select('registration_id', 'firstname', 'lastname', 'PSA', 'good_moral', 'TOR', 'Course_Cert')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($regsWithFiles as $reg) {
            echo "  - REG {$reg->registration_id}: {$reg->firstname} {$reg->lastname}\n";
            if ($reg->PSA) echo "    PSA: {$reg->PSA}\n";
            if ($reg->good_moral) echo "    Good Moral: {$reg->good_moral}\n";
            if ($reg->TOR) echo "    TOR: {$reg->TOR}\n";
            if ($reg->Course_Cert) echo "    Course Cert: {$reg->Course_Cert}\n";
        }
    } catch (Exception $e) {
        echo "❌ File references check failed: " . $e->getMessage() . "\n";
    }

    // 6. CHECK BATCH CAPACITY UPDATES
    echo "\n6. BATCH CAPACITY MANAGEMENT:\n";
    try {
        $batches = DB::table('student_batches')
            ->select('batch_id', 'batch_name', 'max_capacity', 'current_capacity', 'batch_status')
            ->where('batch_status', 'available')
            ->limit(5)
            ->get();
            
        foreach ($batches as $batch) {
            echo "  - Batch {$batch->batch_id}: {$batch->batch_name}\n";
            echo "    Capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";
            
            // Check actual enrollments
            $actualCount = DB::table('enrollments')
                ->where('batch_id', $batch->batch_id)
                ->where('enrollment_status', 'approved')
                ->where('payment_status', 'paid')
                ->count();
                
            echo "    Actual Enrolled: {$actualCount}\n";
            
            if ($actualCount != $batch->current_capacity) {
                echo "    ⚠️  Capacity mismatch! Stored: {$batch->current_capacity}, Actual: {$actualCount}\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Batch capacity check failed: " . $e->getMessage() . "\n";
    }

    // 7. CHECK ENROLLMENT FLOW
    echo "\n7. ENROLLMENT FLOW CHECK:\n";
    try {
        // Check recent enrollments with related data
        $enrollments = DB::table('enrollments')
            ->leftJoin('registrations', 'enrollments.registration_id', '=', 'registrations.registration_id')
            ->leftJoin('users', 'enrollments.user_id', '=', 'users.user_id')
            ->select(
                'enrollments.enrollment_id',
                'enrollments.enrollment_status',
                'enrollments.payment_status',
                'enrollments.batch_id',
                'registrations.firstname',
                'registrations.lastname',
                'registrations.selected_courses',
                'users.email'
            )
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($enrollments as $enr) {
            echo "  - Enrollment {$enr->enrollment_id}: {$enr->firstname} {$enr->lastname}\n";
            echo "    Status: {$enr->enrollment_status} / Payment: {$enr->payment_status}\n";
            echo "    Batch: " . ($enr->batch_id ?: 'None') . "\n";
            echo "    Selected Courses: " . ($enr->selected_courses ?: 'None') . "\n";
            
            // Check if enrollment has course records
            $courseCount = DB::table('enrollment_courses')
                ->where('enrollment_id', $enr->enrollment_id)
                ->count();
            echo "    Course Records: {$courseCount}\n";
        }
    } catch (Exception $e) {
        echo "❌ Enrollment flow check failed: " . $e->getMessage() . "\n";
    }

    // 8. CHECK FORM VALIDATION ISSUES
    echo "\n8. FORM VALIDATION ISSUES:\n";
    try {
        // Check if routes exist
        $routes = [
            'enrollment.modular.submit',
            'registration.validateFile',
            'get.module.courses'
        ];
        
        foreach ($routes as $route) {
            try {
                $url = route($route);
                echo "✅ Route '{$route}': {$url}\n";
            } catch (Exception $e) {
                echo "❌ Route '{$route}' not found\n";
            }
        }
        
        // Check packages and modules
        $packageCount = DB::table('packages')->count();
        $moduleCount = DB::table('modules')->count();
        $courseCount = DB::table('courses')->count();
        
        echo "✅ Packages: {$packageCount}, Modules: {$moduleCount}, Courses: {$courseCount}\n";
        
    } catch (Exception $e) {
        echo "❌ Form validation check failed: " . $e->getMessage() . "\n";
    }

    echo "\n=== DEBUG COMPLETE ===\n";
    echo "Check the output above for any ❌ errors that need fixing.\n";

} catch (Exception $e) {
    echo "❌ DEBUG SCRIPT FAILED: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
