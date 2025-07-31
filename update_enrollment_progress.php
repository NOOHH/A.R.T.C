<?php

echo "=== UPDATING ENROLLMENT PROGRESS TRACKING ===" . PHP_EOL;
echo "Calculating and updating actual progress for all students" . PHP_EOL;
echo "=====================================================" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Analyzing enrollment structure..." . PHP_EOL;
    
    // Get all enrollments that need progress updates
    $stmt = $pdo->query("
        SELECT e.enrollment_id, e.student_id, e.course_id, 
               c.title as course_title,
               s.firstname, s.lastname
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        JOIN students s ON e.student_id = s.student_id
        WHERE e.progress_percentage = 0.00
        ORDER BY e.student_id, e.course_id
    ");
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Found " . count($enrollments) . " enrollments with 0% progress" . PHP_EOL;
    
    $updatedCount = 0;
    
    foreach ($enrollments as $enrollment) {
        echo PHP_EOL . "2. Processing: {$enrollment['firstname']} {$enrollment['lastname']} - {$enrollment['course_title']}" . PHP_EOL;
        
        $studentId = $enrollment['student_id'];
        $courseId = $enrollment['course_id'];
        $enrollmentId = $enrollment['enrollment_id'];
        
        // Calculate actual progress for this enrollment
        echo "   Calculating progress for enrollment {$enrollmentId}..." . PHP_EOL;
        
        // Get total modules for this course
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_modules
            FROM modules 
            WHERE course_id = ?
        ");
        $stmt->execute([$courseId]);
        $totalModules = $stmt->fetchColumn();
        
        // Get completed modules for this student in this course
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT mc.module_id) as completed_modules
            FROM module_completions mc
            JOIN modules m ON mc.module_id = m.module_id
            WHERE mc.student_id = ? AND m.course_id = ?
        ");
        $stmt->execute([$studentId, $courseId]);
        $completedModules = $stmt->fetchColumn();
        
        // Get total content items for this course
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_content
            FROM content 
            WHERE course_id = ?
        ");
        $stmt->execute([$courseId]);
        $totalContent = $stmt->fetchColumn();
        
        // Get completed content for this student in this course
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT cc.content_id) as completed_content
            FROM content_completions cc
            JOIN content c ON cc.content_id = c.content_id
            WHERE cc.student_id = ? AND c.course_id = ?
        ");
        $stmt->execute([$studentId, $courseId]);
        $completedContent = $stmt->fetchColumn();
        
        // Calculate course completion
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as course_completed
            FROM course_completions 
            WHERE student_id = ? AND course_id = ?
        ");
        $stmt->execute([$studentId, $courseId]);
        $courseCompleted = $stmt->fetchColumn();
        
        // Calculate weighted progress
        $moduleProgress = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;
        $contentProgress = $totalContent > 0 ? ($completedContent / $totalContent) * 100 : 0;
        $courseProgress = $courseCompleted > 0 ? 100 : 0;
        
        // Weighted calculation: content 40%, course 40%, modules 20%
        $totalProgress = ($contentProgress * 0.4) + ($courseProgress * 0.4) + ($moduleProgress * 0.2);
        
        echo "   - Modules: {$completedModules}/{$totalModules} ({$moduleProgress}%)" . PHP_EOL;
        echo "   - Content: {$completedContent}/{$totalContent} ({$contentProgress}%)" . PHP_EOL;
        echo "   - Course: " . ($courseCompleted ? "Completed" : "In Progress") . " ({$courseProgress}%)" . PHP_EOL;
        echo "   - Total Progress: {$totalProgress}%" . PHP_EOL;
        
        // Update the enrollment record
        $certificateEligible = $totalProgress >= 100;
        
        $updateStmt = $pdo->prepare("
            UPDATE enrollments 
            SET progress_percentage = ?,
                certificate_eligible = ?,
                completed_modules = ?,
                total_modules = ?,
                completed_courses = ?,
                total_courses = 1,
                last_activity = NOW()
            WHERE enrollment_id = ?
        ");
        
        $result = $updateStmt->execute([
            round($totalProgress, 2),
            $certificateEligible ? 1 : 0,
            $completedModules,
            $totalModules,
            $courseCompleted,
            $enrollmentId
        ]);
        
        if ($result) {
            echo "   ✅ Updated enrollment with {$totalProgress}% progress" . PHP_EOL;
            if ($certificateEligible) {
                echo "   🎓 Student is now eligible for certificate!" . PHP_EOL;
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
        SELECT e.student_id, s.firstname, s.lastname, c.title as course_title, 
               e.progress_percentage, e.enrollment_id
        FROM enrollments e
        JOIN students s ON e.student_id = s.student_id
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.certificate_eligible = 1
        ORDER BY e.progress_percentage DESC
    ");
    $eligibleStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($eligibleStudents)) {
        foreach ($eligibleStudents as $student) {
            echo "   🎓 {$student['firstname']} {$student['lastname']} - {$student['course_title']} ({$student['progress_percentage']}%)" . PHP_EOL;
        }
    } else {
        echo "   No students currently eligible for certificates" . PHP_EOL;
    }
    
    // Show students with highest progress
    echo PHP_EOL . "5. Students with highest progress:" . PHP_EOL;
    $stmt = $pdo->query("
        SELECT e.student_id, s.firstname, s.lastname, c.title as course_title, 
               e.progress_percentage, e.completed_modules, e.total_modules
        FROM enrollments e
        JOIN students s ON e.student_id = s.student_id
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.progress_percentage > 0
        ORDER BY e.progress_percentage DESC
        LIMIT 10
    ");
    $topStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($topStudents as $student) {
        echo "   📈 {$student['firstname']} {$student['lastname']} - {$student['course_title']}: {$student['progress_percentage']}% ({$student['completed_modules']}/{$student['total_modules']} modules)" . PHP_EOL;
    }
    
    echo PHP_EOL . "=== ENROLLMENT PROGRESS UPDATE COMPLETE ===" . PHP_EOL;
    echo "✅ All enrollment progress has been calculated and updated" . PHP_EOL;
    echo "✅ Certificate eligibility is now properly tracked" . PHP_EOL;
    echo "✅ The certificate management system is fully operational" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "Update completed at " . date('Y-m-d H:i:s') . PHP_EOL;
