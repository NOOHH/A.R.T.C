$student = App\Models\Student::where('student_id', '2025-07-00001')->first();
if($student) {
    echo "Student found: " . $student->firstname . " " . $student->lastname . "\n";
    echo "User ID: " . $student->user_id . "\n";
    echo "Email: " . $student->email . "\n";
    dd($student->toArray());
} else {
    echo "Student not found\n";
    echo "Total students: " . App\Models\Student::count() . "\n";
}
