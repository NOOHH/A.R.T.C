<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Test</title>
</head>
<body>
    <h1>Dashboard Debug Test</h1>
    
    <?php
    // Start Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    use App\Models\Enrollment;
    use App\Models\User;
    use App\Models\Student;
    
    // Get a test user with pending enrollment
    $enrollment = Enrollment::where('enrollment_status', 'pending')->with('program')->first();
    
    if (!$enrollment) {
        echo "<p>No pending enrollments found.</p>";
        exit;
    }
    
    echo "<h2>Testing with User ID: {$enrollment->user_id}</h2>";
    echo "<p>Program: " . ($enrollment->program ? $enrollment->program->program_name : 'No program') . "</p>";
    echo "<p>Status: {$enrollment->enrollment_status}</p>";
    
    // Simulate session
    session_start();
    $_SESSION['user_id'] = $enrollment->user_id;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_role'] = 'Student';
    
    // Test the controller logic
    $student = Student::where('user_id', $enrollment->user_id)->first();
    
    echo "<h3>Student Data:</h3>";
    if ($student) {
        echo "<p>Student ID: {$student->student_id}</p>";
        echo "<p>Name: {$student->firstname} {$student->lastname}</p>";
    } else {
        echo "<p>No student record found</p>";
    }
    
    // Get enrollments
    $enrollments = collect();
    
    if ($enrollment->user_id) {
        $userEnrollments = Enrollment::where('user_id', $enrollment->user_id)
            ->with(['program', 'package', 'batch'])
            ->get();
        $enrollments = $enrollments->merge($userEnrollments);
    }
    
    if ($student) {
        $studentEnrollments = Enrollment::where('student_id', $student->student_id)
            ->with(['program', 'package', 'batch'])
            ->get();
        $enrollments = $enrollments->merge($studentEnrollments);
    }
    
    $enrollments = $enrollments->unique('enrollment_id');
    
    echo "<h3>Enrollments Found: " . $enrollments->count() . "</h3>";
    
    $courses = [];
    foreach ($enrollments as $enrollment) {
        if ($enrollment->program) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
            echo "<h4>" . $enrollment->program->program_name . "</h4>";
            echo "<p>Status: {$enrollment->enrollment_status}</p>";
            echo "<p>Payment: {$enrollment->payment_status}</p>";
            echo "<p>Learning Mode: " . ($enrollment->learning_mode ?? 'Not specified') . "</p>";
            echo "</div>";
            
            $courses[] = [
                'name' => $enrollment->program->program_name,
                'enrollment_status' => $enrollment->enrollment_status,
                'payment_status' => $enrollment->payment_status
            ];
        }
    }
    
    echo "<h3>Courses Array:</h3>";
    echo "<pre>" . print_r($courses, true) . "</pre>";
    
    if (empty($courses)) {
        echo "<p style='color: red;'><strong>This is why the dashboard shows 'not enrolled'!</strong></p>";
    } else {
        echo "<p style='color: green;'><strong>Dashboard should show these courses!</strong></p>";
    }
    ?>
</body>
</html>
