<?php
// Fix quiz attempt ownership
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Fix Quiz Attempt Ownership</h1>";

// Find student with user_id 15
$student = \App\Models\Student::where('user_id', 15)->first();

if ($student) {
    echo "<p>✅ Student found: ID {$student->student_id}, User ID: {$student->user_id}</p>";
    echo "<p>Student Name: {$student->student_fname} {$student->student_lname}</p>";
    
    // Update quiz attempt 3 to belong to this student
    $attempt = \App\Models\QuizAttempt::find(3);
    
    if ($attempt) {
        $oldStudentId = $attempt->student_id;
        $attempt->update(['student_id' => $student->student_id]);
        
        echo "<p>✅ Updated quiz attempt 3:</p>";
        echo "<p>- Changed from student_id: {$oldStudentId}</p>";
        echo "<p>- Changed to student_id: {$student->student_id}</p>";
        echo "<p>- Status: {$attempt->status}</p>";
        echo "<p>- Quiz ID: {$attempt->quiz_id}</p>";
        
    } else {
        echo "<p>❌ Quiz attempt 3 not found</p>";
    }
    
} else {
    echo "<p>❌ No student found with user_id 15</p>";
    echo "<p>Available students:</p>";
    
    $students = \App\Models\Student::all();
    foreach ($students as $s) {
        echo "<p>- Student ID: {$s->student_id}, User ID: {$s->user_id}, Name: {$s->student_fname} {$s->student_lname}</p>";
    }
}

echo "<h2>Test Links:</h2>";
echo "<a href='set_test_session.php' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Setup Session</a>";
echo "<a href='/student/quiz/take/3' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Take Quiz</a>";
echo "<a href='debug_quiz_session.php' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Debug Session</a>";
?>
