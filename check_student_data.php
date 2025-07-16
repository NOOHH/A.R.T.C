<?php
// Check student data script
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;
use App\Models\FormRequirement;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING STUDENT DATA ===\n\n";

// Check student with ID 2025-07-00001
$student = Student::where('student_id', '2025-07-00001')->first();

if ($student) {
    echo "Student found with ID: 2025-07-00001\n";
    echo "User ID: " . ($student->user_id ?? 'NULL') . "\n";
    echo "Name: " . ($student->firstname ?? 'NULL') . " " . ($student->lastname ?? 'NULL') . "\n";
    echo "Email: " . ($student->email ?? 'NULL') . "\n";
    echo "Created at: " . ($student->created_at ?? 'NULL') . "\n";
    echo "Updated at: " . ($student->updated_at ?? 'NULL') . "\n\n";
    
    // Show all student data
    echo "=== ALL STUDENT DATA ===\n";
    foreach ($student->toArray() as $key => $value) {
        echo "$key: " . ($value ?? 'NULL') . "\n";
    }
    echo "\n";
    
    // Check related user record
    if ($student->user_id) {
        $user = User::where('user_id', $student->user_id)->first();
        if ($user) {
            echo "=== RELATED USER DATA ===\n";
            echo "User ID: " . $user->user_id . "\n";
            echo "Email: " . $user->email . "\n";
            echo "First Name: " . ($user->user_firstname ?? 'NULL') . "\n";
            echo "Last Name: " . ($user->user_lastname ?? 'NULL') . "\n";
            echo "\n";
        } else {
            echo "No related user found for user_id: " . $student->user_id . "\n\n";
        }
    }
    
} else {
    echo "No student found with ID: 2025-07-00001\n\n";
    
    // Check if there are any students at all
    $studentCount = Student::count();
    echo "Total students in database: $studentCount\n";
    
    if ($studentCount > 0) {
        echo "\n=== RECENT STUDENTS ===\n";
        $recentStudents = Student::orderBy('created_at', 'desc')->take(5)->get();
        foreach ($recentStudents as $s) {
            echo "ID: " . ($s->student_id ?? 'NULL') . ", Name: " . ($s->firstname ?? 'NULL') . " " . ($s->lastname ?? 'NULL') . ", User ID: " . ($s->user_id ?? 'NULL') . "\n";
        }
    }
}

// Check form requirements columns in students table
echo "\n=== STUDENTS TABLE COLUMNS ===\n";
$columns = Schema::getColumnListing('students');
foreach ($columns as $column) {
    echo "- $column\n";
}

// Check form requirements
echo "\n=== FORM REQUIREMENTS ===\n";
$formRequirements = FormRequirement::all();
foreach ($formRequirements as $req) {
    $columnExists = Schema::hasColumn('students', $req->field_name);
    echo "Field: " . $req->field_name . " (Type: " . $req->field_type . ") - Column exists: " . ($columnExists ? 'YES' : 'NO') . "\n";
}

echo "\n=== SCRIPT COMPLETE ===\n";
?>
