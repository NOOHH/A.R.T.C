<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\Program;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\ClassMeeting;
use App\Models\StudentBatch;
use App\Models\Professor;
use Carbon\Carbon;

echo "🚀 Adding calendar test data...\n";

// Get first student and program for testing
$student = Student::first();
$program = Program::first();

if (!$student || !$program) {
    echo "❌ No student or program found in database\n";
    exit(1);
}

echo "📝 Using student: {$student->student_id} and program: {$program->program_name}\n";

// Get or use first available batch
$batch = StudentBatch::where('program_id', $program->program_id)->first();
if (!$batch) {
    // Try to get any existing batch
    $batch = StudentBatch::first();
    if (!$batch) {
        echo "❌ No batches found. Please create a batch manually first.\n";
        exit(1);
    }
}
echo "📝 Using batch: {$batch->batch_name}\n";

// Get or create a professor
$professor = Professor::first();
if (!$professor) {
    // Create a test professor
    $professor = Professor::create([
        'user_id' => 1, // Assuming admin user exists
        'name' => 'Dr. Test Professor',
        'specialization' => 'Computer Science',
        'bio' => 'Test professor for calendar events',
        'is_active' => true
    ]);
    echo "✅ Created test professor: {$professor->name}\n";
}

// Create test announcements for this month
$today = Carbon::now();
for ($i = 0; $i < 5; $i++) {
    $date = $today->copy()->addDays($i - 2); // Some past, some future
    
    Announcement::create([
        'professor_id' => $professor->professor_id,
        'program_id' => $program->program_id,
        'title' => "Important Update " . ($i + 1),
        'content' => "This is test announcement content for demonstration purposes. Event #{$i}",
        'type' => ['general', 'urgent', 'event'][$i % 3],
        'is_active' => true,
        'created_at' => $date,
        'updated_at' => $date
    ]);
}
echo "✅ Created 5 test announcements\n";

// Create test assignments
for ($i = 0; $i < 3; $i++) {
    $dueDate = $today->copy()->addDays($i * 7 + 3); // Weekly assignments
    
    Assignment::create([
        'professor_id' => $professor->professor_id,
        'program_id' => $program->program_id,
        'title' => "Assignment " . ($i + 1),
        'description' => "Test assignment description for assignment #{$i}",
        'instructions' => "Complete the tasks as described in the course materials.",
        'max_points' => 100,
        'due_date' => $dueDate,
        'is_active' => true
    ]);
}
echo "✅ Created 3 test assignments\n";

// Create test class meetings
for ($i = 0; $i < 4; $i++) {
    $meetingDate = $today->copy()->addDays($i * 2)->setHour(10)->setMinute(0); // Every other day
    
    ClassMeeting::create([
        'batch_id' => $batch->batch_id,
        'professor_id' => $professor->professor_id,
        'title' => "Class Session " . ($i + 1),
        'description' => "Regular class session for the program",
        'meeting_date' => $meetingDate,
        'duration_minutes' => 90,
        'meeting_url' => 'https://zoom.us/j/test' . $i,
        'status' => 'scheduled',
        'created_by' => 1 // Use admin ID 1
    ]);
}
echo "✅ Created 4 test class meetings\n";

// Make sure the student is enrolled in the program and batch
$enrollment = \App\Models\Enrollment::where('student_id', $student->student_id)
    ->where('program_id', $program->program_id)
    ->first();

if (!$enrollment) {
    \App\Models\Enrollment::create([
        'student_id' => $student->student_id,
        'user_id' => $student->user_id,
        'program_id' => $program->program_id,
        'batch_id' => $batch->batch_id,
        'enrollment_type' => 'Full',
        'enrollment_status' => 'approved',
        'payment_status' => 'paid'
    ]);
    echo "✅ Created student enrollment\n";
} else {
    echo "✅ Student already enrolled\n";
}

echo "🎉 Test data created successfully!\n";
echo "📅 Calendar should now show real events for student: {$student->student_id}\n";
echo "🔗 Test URLs:\n";
echo "   - Events API: http://127.0.0.1:8000/student/calendar/events?year=" . $today->year . "&month=" . $today->month . "\n";
echo "   - Today API: http://127.0.0.1:8000/student/calendar/today\n";
echo "\n💡 To test with session, log in as student first.\n";
