<?php
// Test Admin Enrollment System - Browser Test
echo "=== ADMIN ENROLLMENT SYSTEM BROWSER TEST ===\n";
echo "Testing enrollment form functionality\n\n";

echo "1. Open your browser and go to: http://localhost:8000/admin/enrollments\n";
echo "2. Login with admin credentials if required\n";
echo "3. Test the following:\n\n";

echo "CSV EXPORT TEST:\n";
echo "   - Look for CSV export button/form\n";
echo "   - Click export and verify it downloads a file (not redirects to another page)\n";
echo "   - Check the downloaded CSV contains proper enrollment data\n\n";

echo "PROGRAM -> MODULE -> COURSE FLOW TEST:\n";
echo "   - Select a Program from dropdown\n";
echo "   - Verify Module dropdown populates automatically\n";
echo "   - Select a Module\n";
echo "   - Verify Course dropdown populates automatically\n";
echo "   - Test with different programs to ensure it works consistently\n\n";

echo "FORM VALIDATION TEST:\n";
echo "   - Try submitting form without required fields\n";
echo "   - Should NOT see 'An invalid form control with name=module_id is not focusable' error\n";
echo "   - Hidden fields should not be marked as required in HTML\n\n";

echo "ENROLLMENT SUBMISSION TEST:\n";
echo "   - Fill in all required fields:\n";
echo "     * Student ID: 2025-07-00001 (test student)\n";
echo "     * Program: Select any program\n";
echo "     * Module: Select from populated dropdown\n";
echo "     * Course: Select from populated dropdown\n";
echo "     * Learning Mode: Synchronous or Self Paced\n";
echo "     * Enrollment Type: Modular or Full\n";
echo "   - Submit form\n";
echo "   - Verify success message appears\n";
echo "   - Check database that enrollment was created properly\n\n";

echo "API ENDPOINT VERIFICATION:\n";
echo "   - Open browser developer tools (F12)\n";
echo "   - Go to Network tab\n";
echo "   - Select different programs and watch for API calls to:\n";
echo "     * /api/programs/{id}/modules\n";
echo "     * /api/modules/{id}/courses\n";
echo "   - Verify these return JSON data with modules/courses\n\n";

echo "DATABASE VERIFICATION:\n";
echo "   - After successful enrollment, check these tables:\n";
echo "     * enrollments table - should have new enrollment record\n";
echo "     * enrollment_courses table - should have module/course associations\n\n";

echo "EXPECTED RESULTS:\n";
echo "✓ CSV export downloads file directly\n";
echo "✓ No form validation errors about module_id\n";
echo "✓ Dropdowns populate dynamically based on selections\n";
echo "✓ Enrollment creates records in both enrollments and enrollment_courses tables\n";
echo "✓ API endpoints return proper JSON data\n\n";

echo "If any of these tests fail, check:\n";
echo "- Browser console for JavaScript errors\n";
echo "- Laravel logs for backend errors\n";
echo "- Network tab for failed API requests\n";
echo "- Database for missing records\n\n";

echo "=== AUTOMATED DATABASE CHECK ===\n";
?>

<?php
// Quick database verification
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'artc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n";
    
    // Check recent enrollments
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments");
    $enrollmentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✓ Total enrollments in database: $enrollmentCount\n";
    
    // Check enrollment courses
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollment_courses");
    $enrollmentCoursesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✓ Total enrollment courses: $enrollmentCoursesCount\n";
    
    // Check if test student exists
    $stmt = $pdo->prepare("SELECT firstname, lastname FROM students WHERE student_id = ?");
    $stmt->execute(['2025-07-00001']);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo "✓ Test student exists: {$student['firstname']} {$student['lastname']}\n";
    } else {
        echo "⚠ Test student (2025-07-00001) not found\n";
    }
    
    // Check latest enrollment for test student
    $stmt = $pdo->prepare("SELECT enrollment_id, program_id, enrollment_type, learning_mode, created_at FROM enrollments WHERE student_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['2025-07-00001']);
    $latestEnrollment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($latestEnrollment) {
        echo "✓ Latest enrollment for test student:\n";
        echo "   ID: {$latestEnrollment['enrollment_id']}\n";
        echo "   Program: {$latestEnrollment['program_id']}\n";
        echo "   Type: {$latestEnrollment['enrollment_type']}\n";
        echo "   Learning Mode: {$latestEnrollment['learning_mode']}\n";
        echo "   Date: {$latestEnrollment['created_at']}\n";
    } else {
        echo "⚠ No enrollments found for test student\n";
    }
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "Use the browser tests above to verify the admin enrollment system is working correctly.\n";
?>
