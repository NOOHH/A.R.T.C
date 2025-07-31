<?php
// Test users and content functionality
require_once 'vendor/autoload.php';

// Initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>Users in Database:</h2>\n";
$users = App\Models\User::select('id', 'name', 'email', 'role')->get();
foreach ($users as $user) {
    echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n<br>";
}

echo "\n<h2>Content Items:</h2>\n";
$contents = App\Models\ContentItem::select('id', 'title', 'course_id')->limit(10)->get();
foreach ($contents as $content) {
    echo "- ID: {$content->id} - {$content->title} (Course: {$content->course_id})\n<br>";
    echo "  <a href='/test-content/{$content->id}' target='_blank'>Test Content View</a><br><br>";
}

echo "\n<h2>Courses:</h2>\n";
$courses = App\Models\Course::select('subject_id', 'course_name')->limit(10)->get();
foreach ($courses as $course) {
    echo "- ID: {$course->subject_id} - {$course->course_name}\n<br>";
}

echo "\n<h2>Assignment Submissions Table:</h2>\n";
try {
    $submissions = DB::table('assignment_submissions')->select('id', 'user_id', 'content_id')->limit(5)->get();
    foreach ($submissions as $submission) {
        echo "- Submission ID: {$submission->id} - User: {$submission->user_id} - Content: {$submission->content_id}\n<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n<br>";
}

echo "\n<h2>Student Progress Table:</h2>\n";
try {
    $progress = DB::table('student_progress')->select('id', 'student_id', 'content_id', 'is_completed')->limit(5)->get();
    foreach ($progress as $p) {
        echo "- Progress ID: {$p->id} - Student: {$p->student_id} - Content: {$p->content_id} - Completed: " . ($p->is_completed ? 'Yes' : 'No') . "\n<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n<br>";
}
