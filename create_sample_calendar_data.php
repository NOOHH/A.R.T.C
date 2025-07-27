<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== Creating Sample Calendar Data ===\n\n";

// Get user 174's enrolled programs and find a batch
$enrollment = DB::table('enrollments')
    ->where('user_id', 174)
    ->where('enrollment_status', 'approved')
    ->first();

if (!$enrollment) {
    echo "No approved enrollment found for user 174\n";
    exit;
}

$programId = $enrollment->program_id;

// Find a batch for this program
$batch = DB::table('student_batches')
    ->where('program_id', $programId)
    ->first();

if (!$batch) {
    echo "No batch found for program ID: $programId\n";
    exit;
}

$batchId = $batch->batch_id;

echo "Found enrollment - Batch ID: $batchId, Program ID: $programId\n\n";

// Update the enrollment with the batch ID if it's null
if (!$enrollment->batch_id) {
    DB::table('enrollments')
        ->where('enrollment_id', $enrollment->enrollment_id)
        ->update(['batch_id' => $batchId]);
    echo "Updated enrollment with batch ID\n\n";
}

// Get a professor ID
$professor = DB::table('professors')->first();
if (!$professor) {
    echo "No professors found\n";
    exit;
}

$professorId = $professor->professor_id;
echo "Using Professor ID: $professorId\n\n";

// 1. Create sample meetings for the next few weeks
echo "Creating sample meetings...\n";
$meetings = [
    [
        'title' => 'Introduction to Software Engineering',
        'description' => 'Overview of software development lifecycle',
        'meeting_date' => Carbon::now()->addDays(1)->setTime(10, 0, 0),
        'duration_minutes' => 90
    ],
    [
        'title' => 'Database Design Workshop',
        'description' => 'Hands-on database design session',
        'meeting_date' => Carbon::now()->addDays(3)->setTime(14, 0, 0),
        'duration_minutes' => 120
    ],
    [
        'title' => 'Project Review Session',
        'description' => 'Review of ongoing projects and assignments',
        'meeting_date' => Carbon::now()->addDays(7)->setTime(9, 0, 0),
        'duration_minutes' => 60
    ],
    [
        'title' => 'Advanced Programming Concepts',
        'description' => 'Object-oriented programming and design patterns',
        'meeting_date' => Carbon::now()->addDays(10)->setTime(13, 0, 0),
        'duration_minutes' => 90
    ]
];

foreach ($meetings as $meeting) {
    DB::table('class_meetings')->insert([
        'batch_id' => $batchId,
        'professor_id' => $professorId,
        'title' => $meeting['title'],
        'description' => $meeting['description'],
        'meeting_date' => $meeting['meeting_date'],
        'duration_minutes' => $meeting['duration_minutes'],
        'meeting_url' => 'https://zoom.us/j/example',
        'status' => 'scheduled',
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "Created meeting: {$meeting['title']} on {$meeting['meeting_date']->format('M d, Y H:i')}\n";
}

// 2. Create sample assignments
echo "\nCreating sample assignments...\n";
$assignments = [
    [
        'title' => 'Database Schema Design',
        'description' => 'Design a comprehensive database schema for an e-commerce platform',
        'instructions' => 'Create an ER diagram and implement the schema with proper relationships, constraints, and indexes.',
        'due_date' => Carbon::now()->addDays(5)->setTime(23, 59, 59),
        'max_points' => 100
    ],
    [
        'title' => 'Web Application Development',
        'description' => 'Build a full-stack web application using modern frameworks',
        'instructions' => 'Develop a CRUD application with user authentication, responsive design, and RESTful API.',
        'due_date' => Carbon::now()->addDays(14)->setTime(23, 59, 59),
        'max_points' => 150
    ],
    [
        'title' => 'Algorithm Analysis Report',
        'description' => 'Analyze the time and space complexity of various sorting algorithms',
        'instructions' => 'Write a comprehensive report comparing bubble sort, quicksort, and mergesort with benchmark tests.',
        'due_date' => Carbon::now()->addDays(8)->setTime(23, 59, 59),
        'max_points' => 75
    ]
];

foreach ($assignments as $assignment) {
    DB::table('assignments')->insert([
        'professor_id' => $professorId,
        'program_id' => $programId,
        'title' => $assignment['title'],
        'description' => $assignment['description'],
        'instructions' => $assignment['instructions'],
        'due_date' => $assignment['due_date'],
        'max_points' => $assignment['max_points'],
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "Created assignment: {$assignment['title']} due {$assignment['due_date']->format('M d, Y H:i')}\n";
}

// 3. Create sample announcements
echo "\nCreating sample announcements...\n";
$announcements = [
    [
        'title' => 'Welcome to the New Semester!',
        'description' => 'Important information about the upcoming semester',
        'content' => 'Welcome students! This semester we have exciting new courses and projects. Please check your schedules and prepare for an engaging learning experience.',
        'type' => 'general',
        'publish_date' => Carbon::now()->subDays(1),
        'expire_date' => Carbon::now()->addDays(30)
    ],
    [
        'title' => 'Midterm Examination Schedule',
        'description' => 'Examination dates and requirements',
        'content' => 'Midterm examinations will be held from next week. Please review the schedule and prepare accordingly. All exams will be conducted online.',
        'type' => 'urgent',
        'publish_date' => Carbon::now()->addDays(2),
        'expire_date' => Carbon::now()->addDays(15)
    ],
    [
        'title' => 'Guest Lecture: Industry Expert Session',
        'description' => 'Special guest lecture by industry professional',
        'content' => 'Join us for an exclusive session with a senior software architect from a leading tech company. Topics will include career guidance and industry trends.',
        'type' => 'event',
        'video_link' => 'https://youtube.com/watch?v=example',
        'publish_date' => Carbon::now()->addDays(6),
        'expire_date' => Carbon::now()->addDays(20)
    ]
];

foreach ($announcements as $announcement) {
    DB::table('announcements')->insert([
        'professor_id' => $professorId,
        'program_id' => $programId,
        'title' => $announcement['title'],
        'description' => $announcement['description'],
        'content' => $announcement['content'],
        'type' => $announcement['type'],
        'video_link' => $announcement['video_link'] ?? null,
        'target_scope' => 'specific',
        'target_programs' => json_encode([$programId]),
        'publish_date' => $announcement['publish_date'],
        'expire_date' => $announcement['expire_date'],
        'is_published' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "Created announcement: {$announcement['title']} published {$announcement['publish_date']->format('M d, Y H:i')}\n";
}

echo "\n=== Sample Data Creation Complete ===\n";
echo "✅ Created " . count($meetings) . " meetings\n";
echo "✅ Created " . count($assignments) . " assignments\n";
echo "✅ Created " . count($announcements) . " announcements\n";
echo "\nNow you can test the calendar at: http://127.0.0.1:8000/student/calendar\n";
?>
