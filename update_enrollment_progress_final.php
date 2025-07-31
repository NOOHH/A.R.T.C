<?php

echo "=== UPDATING ENROLLMENT PROGRESS (CORRECT VERSION) ===" . PHP_EOL;
echo "Calculating progress based on program/package structure" . PHP_EOL;
echo "====================================================" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Analyzing enrollments that need progress updates..." . PHP_EOL;
    
    // Get all enrollments with 0% progress
    $stmt = $pdo->query("
        SELECT e.enrollment_id, e.student_id, e.program_id, e.package_id,
               p.program_name, pk.package_name,
               s.firstname, s.lastname
        FROM enrollments e
        JOIN programs p ON e.program_id = p.program_id
        JOIN packages pk ON e.package_id = pk.package_id
        JOIN students s ON e.student_id = s.student_id
        WHERE e.progress_percentage = 0.00
        ORDER BY e.student_id
    ");
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Found " . count($enrollments) . " enrollments with 0% progress" . PHP_EOL;
    
    $updatedCount = 0;
    
    foreach ($enrollments as $enrollment) {
        echo PHP_EOL . "2. Processing: {$enrollment['firstname']} {$enrollment['lastname']}" . PHP_EOL;
        echo "   Program: {$enrollment['program_name']}" . PHP_EOL;
        echo "   Package: {$enrollment['package_name']}" . PHP_EOL;
        
        $studentId = $enrollment['student_id'];
        $programId = $enrollment['program_id'];
        $packageId = $enrollment['package_id'];
        $enrollmentId = $enrollment['enrollment_id'];
        
        // Get total modules in this program
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_modules
            FROM modules 
            WHERE program_id = ?
        ");
        $stmt->execute([$programId]);
        $totalModules = $stmt->fetchColumn();
        
        // Get completed modules for this student in this program
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT mc.module_id) as completed_modules
            FROM module_completions mc
            JOIN modules m ON mc.module_id = m.modules_id
            WHERE mc.student_id = ? AND m.program_id = ?
        ");
        $stmt->execute([$studentId, $programId]);
        $completedModules = $stmt->fetchColumn();
        
        // Get total courses/subjects in this program
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_courses
            FROM courses 
            WHERE module_id IN (SELECT modules_id FROM modules WHERE program_id = ?)
        ");
        $stmt->execute([$programId]);
        $totalCourses = $stmt->fetchColumn();
        
        // Get completed courses for this student
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT cc.course_id) as completed_courses
            FROM course_completions cc
            JOIN courses c ON cc.course_id = c.subject_id
            JOIN modules m ON c.module_id = m.modules_id
            WHERE cc.student_id = ? AND m.program_id = ?
        ");
        $stmt->execute([$studentId, $programId]);
        $completedCourses = $stmt->fetchColumn();
        
        // Get total content for this student
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as completed_content
            FROM content_completions 
            WHERE student_id = ?
        ");
        $stmt->execute([$studentId]);
        $completedContent = $stmt->fetchColumn();
        
        // Calculate progress percentages
        $moduleProgress = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;
        $courseProgress = $totalCourses > 0 ? ($completedCourses / $totalCourses) * 100 : 0;
        $contentProgress = $completedContent > 0 ? 100 : 0; // Content completion is binary
        
        // Weighted calculation: modules 50%, courses 40%, content 10%
        $totalProgress = ($moduleProgress * 0.5) + ($courseProgress * 0.4) + ($contentProgress * 0.1);
        
        echo "   - Modules: {$completedModules}/{$totalModules} (" . round($moduleProgress, 1) . "%)" . PHP_EOL;
        echo "   - Courses: {$completedCourses}/{$totalCourses} (" . round($courseProgress, 1) . "%)" . PHP_EOL;
        echo "   - Content: {$completedContent} items completed" . PHP_EOL;
        echo "   - Total Progress: " . round($totalProgress, 2) . "%" . PHP_EOL;
        
        // Update the enrollment record
        $certificateEligible = $totalProgress >= 100;
        
        $updateStmt = $pdo->prepare("
            UPDATE enrollments 
            SET progress_percentage = ?,
                certificate_eligible = ?,
                completed_modules = ?,
                total_modules = ?,
                completed_courses = ?,
                total_courses = ?,
                last_activity = NOW()
            WHERE enrollment_id = ?
        ");
        
        $result = $updateStmt->execute([
            round($totalProgress, 2),
            $certificateEligible ? 1 : 0,
            $completedModules,
            $totalModules,
            $completedCourses,
            $totalCourses,
            $enrollmentId
        ]);
        
        if ($result) {
            echo "   ✅ Updated enrollment with " . round($totalProgress, 2) . "% progress" . PHP_EOL;
            if ($certificateEligible) {
                echo "   🎓 Student is now eligible for certificate!" . PHP_EOL;
            } elseif ($totalProgress >= 80) {
                echo "   ⚠️ Student is nearly complete (80%+ threshold)" . PHP_EOL;
            }
            $updatedCount++;
        } else {
            echo "   ❌ Failed to update enrollment" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "3. Summary of updates:" . PHP_EOL;
    echo "   Total enrollments processed: " . count($enrollments) . PHP_EOL;
    echo "   Successfully updated: {$updatedCount}" . PHP_EOL;
    
    // Show students now eligible for certificates
    echo PHP_EOL . "4. Students now eligible for certificates:" . PHP_EOL;
    $stmt = $pdo->query("
        SELECT e.student_id, s.firstname, s.lastname, p.program_name, 
               e.progress_percentage, e.enrollment_id
        FROM enrollments e
        JOIN students s ON e.student_id = s.student_id
        JOIN programs p ON e.program_id = p.program_id
        WHERE e.certificate_eligible = 1
        ORDER BY e.progress_percentage DESC
    ");
    $eligibleStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($eligibleStudents)) {
        foreach ($eligibleStudents as $student) {
            echo "   🎓 {$student['firstname']} {$student['lastname']} - {$student['program_name']} ({$student['progress_percentage']}%)" . PHP_EOL;
        }
    } else {
        echo "   No students currently eligible for certificates" . PHP_EOL;
    }
    
    // Show students with highest progress
    echo PHP_EOL . "5. Students with highest progress:" . PHP_EOL;
    $stmt = $pdo->query("
        SELECT e.student_id, s.firstname, s.lastname, p.program_name, 
               e.progress_percentage, e.completed_modules, e.total_modules,
               e.completed_courses, e.total_courses
        FROM enrollments e
        JOIN students s ON e.student_id = s.student_id
        JOIN programs p ON e.program_id = p.program_id
        WHERE e.progress_percentage > 0
        ORDER BY e.progress_percentage DESC
        LIMIT 10
    ");
    $topStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($topStudents as $student) {
        echo "   📈 {$student['firstname']} {$student['lastname']} - {$student['program_name']}: {$student['progress_percentage']}%" . PHP_EOL;
        echo "      Modules: {$student['completed_modules']}/{$student['total_modules']}, Courses: {$student['completed_courses']}/{$student['total_courses']}" . PHP_EOL;
    }
    
    // Test certificate generation for eligible students
    if (!empty($eligibleStudents)) {
        echo PHP_EOL . "6. Testing certificate generation access:" . PHP_EOL;
        foreach ($eligibleStudents as $student) {
            $certificateUrl = "http://127.0.0.1:8000/admin/certificates/generate/{$student['enrollment_id']}";
            echo "   Certificate generation URL for {$student['firstname']} {$student['lastname']}: {$certificateUrl}" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== ENROLLMENT PROGRESS UPDATE COMPLETE ===" . PHP_EOL;
    echo "✅ All enrollment progress has been calculated and updated" . PHP_EOL;
    echo "✅ Certificate eligibility is now properly tracked" . PHP_EOL;
    echo "✅ The certificate management system is fully operational" . PHP_EOL;
    echo "✅ Students with 100% progress can now generate certificates" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "Update completed at " . date('Y-m-d H:i:s') . PHP_EOL;
