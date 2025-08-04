<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Checking Email in Database ===\n";
$email = 'bmjustimbaste2003@gmail.com';

try {
    // Check students table
    $student = \App\Models\Student::where('email', $email)->first();
    if ($student) {
        echo "✅ Found in STUDENTS table:\n";
        echo "   Student ID: {$student->student_id}\n";
        echo "   Name: {$student->firstname} {$student->lastname}\n";
        echo "   Email: {$student->email}\n\n";
    } else {
        echo "❌ NOT found in students table\n";
    }

    // Check admins table
    $admin = \App\Models\Admin::where('email', $email)->first();
    if ($admin) {
        echo "✅ Found in ADMINS table:\n";
        echo "   Admin ID: {$admin->admin_id}\n";
        echo "   Username: {$admin->username}\n";
        echo "   Email: {$admin->email}\n\n";
    } else {
        echo "❌ NOT found in admins table\n";
    }

    // Check professors table
    $professor = \App\Models\Professor::where('professor_email', $email)->first();
    if ($professor) {
        echo "✅ Found in PROFESSORS table:\n";
        echo "   Professor ID: {$professor->professor_id}\n";
        echo "   Name: {$professor->professor_first_name} {$professor->professor_last_name}\n";
        echo "   Email: {$professor->professor_email}\n\n";
    } else {
        echo "❌ NOT found in professors table\n";
    }

    // Check directors table
    $director = \App\Models\Director::where('directors_email', $email)->first();
    if ($director) {
        echo "✅ Found in DIRECTORS table:\n";
        echo "   Director ID: {$director->director_id}\n";
        echo "   Name: {$director->directors_first_name} {$director->directors_last_name}\n";
        echo "   Email: {$director->directors_email}\n\n";
    } else {
        echo "❌ NOT found in directors table\n";
    }

    // Check users table (if it exists)
    try {
        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            echo "✅ Found in USERS table:\n";
            echo "   User ID: {$user->id}\n";
            echo "   Name: {$user->firstname} {$user->lastname}\n";
            echo "   Email: {$user->email}\n\n";
        } else {
            echo "❌ NOT found in users table\n";
        }
    } catch (Exception $e) {
        echo "❌ Users table check failed: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Email Configuration Check ===\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
?>
