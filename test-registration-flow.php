<!DOCTYPE html>
<html>
<head>
    <title>Test Registration Success</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; }
        .form-container { max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Test Registration Process</h1>
    
    <div class="success">
        <h2>✓ Registration Changes Applied</h2>
        <p>The following changes have been made to fix the registration process:</p>
        <ul>
            <li>Changed StudentRegistrationController to redirect to success page instead of returning JSON</li>
            <li>Fixed validation errors to redirect back with proper error messages</li>
            <li>Fixed StudentDashboardController syntax error that prevented pending enrollments from showing</li>
            <li>Added debugging to dashboard controller to track enrollment data</li>
        </ul>
    </div>
    
    <h2>Current Test Status</h2>
    
    <?php
    // Start Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    use App\Models\Enrollment;
    use App\Models\User;
    use App\Models\Program;
    
    echo "<h3>Database Status:</h3>";
    
    // Check pending enrollments
    $pendingCount = Enrollment::where('enrollment_status', 'pending')->count();
    echo "<p>• Pending enrollments in database: <strong>$pendingCount</strong></p>";
    
    // Check approved enrollments
    $approvedCount = Enrollment::where('enrollment_status', 'approved')->count();
    echo "<p>• Approved enrollments in database: <strong>$approvedCount</strong></p>";
    
    // Check users
    $userCount = User::count();
    echo "<p>• Total users in database: <strong>$userCount</strong></p>";
    
    // Check programs
    $programCount = Program::count();
    echo "<p>• Total programs in database: <strong>$programCount</strong></p>";
    
    echo "<h3>Sample Pending Enrollments:</h3>";
    $pending = Enrollment::where('enrollment_status', 'pending')->with('program')->take(3)->get();
    
    if ($pending->count() > 0) {
        foreach ($pending as $enrollment) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px 0;'>";
            echo "<strong>User ID:</strong> {$enrollment->user_id}<br>";
            echo "<strong>Program:</strong> " . ($enrollment->program ? $enrollment->program->program_name : 'No program') . "<br>";
            echo "<strong>Status:</strong> {$enrollment->enrollment_status}<br>";
            echo "<strong>Created:</strong> {$enrollment->created_at}<br>";
            echo "</div>";
        }
    } else {
        echo "<p>No pending enrollments found.</p>";
    }
    ?>
    
    <h2>Test Registration Form</h2>
    <div class="form-container">
        <p><strong>Note:</strong> Use this form to test the registration flow. After submission, it should redirect to the success page instead of showing JSON.</p>
        
        <form action="<?php echo url('/student/register'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label>Package ID:</label>
                <select name="package_id" required>
                    <option value="">Select Package</option>
                    <?php
                    $packages = \App\Models\Package::all();
                    foreach ($packages as $package) {
                        echo "<option value='{$package->package_id}'>{$package->package_name}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Program ID:</label>
                <select name="program_id" required>
                    <option value="">Select Program</option>
                    <?php
                    $programs = \App\Models\Program::all();
                    foreach ($programs as $program) {
                        echo "<option value='{$program->program_id}'>{$program->program_name}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Learning Mode:</label>
                <select name="learning_mode" required>
                    <option value="">Select Mode</option>
                    <option value="synchronous">Synchronous</option>
                    <option value="asynchronous">Asynchronous</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="firstname" required>
            </div>
            
            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="lastname" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Contact Number:</label>
                <input type="text" name="contact_number" required>
            </div>
            
            <input type="hidden" name="enrollment_type" value="full">
            
            <button type="submit">Test Registration</button>
        </form>
    </div>
    
    <h2>What Should Happen:</h2>
    <ol>
        <li>Fill out the form above and click "Test Registration"</li>
        <li>You should be redirected to <code>/registration/success</code> page</li>
        <li>A new pending enrollment should be created in the database</li>
        <li>When you log in as that user, the pending enrollment should show on the dashboard</li>
    </ol>
</body>
</html>
