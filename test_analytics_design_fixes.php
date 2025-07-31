<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Fixes Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        pre { background-color: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .programs-list { margin: 10px 0; }
        .program-item { background: #e9ecef; padding: 5px; margin: 2px; border-radius: 3px; display: inline-block; }
    </style>
</head>
<body>
    <h1>🔧 ANALYTICS DESIGN FIXES VERIFICATION</h1>
    
    <?php
    require_once 'vendor/autoload.php';
    require_once 'bootstrap/app.php';
    
    use App\Http\Controllers\AdminAnalyticsController;
    use Illuminate\Http\Request;
    
    session(['user_type' => 'admin']);
    
    try {
        $controller = new AdminAnalyticsController();
        
        echo "<div class='section info'>";
        echo "<h2>🎓 TESTING GROUPED RECENTLY COMPLETED</h2>";
        
        // Test Recently Completed (should now be grouped)
        $request = new Request();
        $response = $controller->getData($request);
        $data = json_decode($response->getContent(), true);
        
        if (isset($data['tables']['recentlyCompleted']) && !empty($data['tables']['recentlyCompleted'])) {
            echo "<div class='success'>";
            echo "<h3>✅ Recently Completed Working (Grouped)!</h3>";
            echo "<p>Found " . count($data['tables']['recentlyCompleted']) . " student completion record(s)</p>";
            
            echo "<table>";
            echo "<tr><th>Student</th><th>Email</th><th>Program</th><th>Plan</th><th>Completion Date</th><th>Completed Items</th></tr>";
            foreach ($data['tables']['recentlyCompleted'] as $completion) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($completion['name'] ?? 'N/A') . "<br><small>" . htmlspecialchars($completion['student_id'] ?? '') . "</small></td>";
                echo "<td>" . htmlspecialchars($completion['email'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($completion['program'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($completion['plan'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($completion['completion_date'] ?? 'N/A') . "</td>";
                echo "<td style='max-width: 300px; word-wrap: break-word;'>" . htmlspecialchars($completion['final_score'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h3>❌ Recently Completed Still Not Working</h3>";
            echo "<p>No recently completed data found</p>";
            echo "</div>";
        }
        echo "</div>";
        
        echo "<div class='section info'>";
        echo "<h2>📋 TESTING PROGRAMS API FOR BOARD EXAMS</h2>";
        
        // Test Programs API
        $programsResponse = $controller->getPrograms();
        $programsData = json_decode($programsResponse->getContent(), true);
        
        if (!empty($programsData)) {
            echo "<div class='success'>";
            echo "<h3>✅ Programs API Working!</h3>";
            echo "<p>Found " . count($programsData) . " program(s) for board exam dropdown</p>";
            
            echo "<div class='programs-list'>";
            echo "<strong>Available Programs:</strong><br>";
            foreach ($programsData as $program) {
                echo "<span class='program-item'>";
                echo "ID: " . htmlspecialchars($program['id']) . " - " . htmlspecialchars($program['name']);
                echo "</span>";
            }
            echo "</div>";
            
            echo "<p><strong>Board Exam Options will be:</strong></p>";
            echo "<ul>";
            foreach ($programsData as $program) {
                echo "<li>" . htmlspecialchars($program['name']) . " Board Exam</li>";
            }
            echo "<li>Other</li>";
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h3>❌ Programs API Failed</h3>";
            echo "<p>No programs found or API error</p>";
            echo "</div>";
        }
        echo "</div>";
        
        // Test raw data comparison
        echo "<div class='section info'>";
        echo "<h2>🔍 BEFORE vs AFTER COMPARISON</h2>";
        
        echo "<h3>BEFORE (Separate Rows):</h3>";
        echo "<pre>";
        echo "Vince Michael Dela Vega | vince03handsome11@gmail.com | Nursing | Modular | Jul 31, 2025 | Module: Modules 1\n";
        echo "Vince Michael Dela Vega | vince03handsome11@gmail.com | Nursing | Modular | Jul 31, 2025 | Module: Modules 2\n";
        echo "Vince Michael Dela Vega | vince03handsome11@gmail.com | Nursing | Modular | Jul 31, 2025 | Course: Mechanics\n";
        echo "Vince Michael Dela Vega | vince03handsome11@gmail.com | Nursing | Modular | Jul 31, 2025 | Course: Mechanical Engineering 101\n";
        echo "Vince Michael Dela Vega | vince03handsome11@gmail.com | Nursing | Modular | Jul 31, 2025 | Course: Hospitality";
        echo "</pre>";
        
        echo "<h3>AFTER (Grouped by Student):</h3>";
        if (isset($data['tables']['recentlyCompleted'][0])) {
            $student = $data['tables']['recentlyCompleted'][0];
            echo "<pre>";
            echo htmlspecialchars($student['name']) . " | " . htmlspecialchars($student['email']) . " | " . htmlspecialchars($student['program']) . " | " . htmlspecialchars($student['plan']) . " | " . htmlspecialchars($student['completion_date']) . "\n";
            echo "Completed Items: " . htmlspecialchars($student['final_score']);
            echo "</pre>";
        }
        echo "</div>";
        
        // Show raw API data for debugging
        echo "<div class='section info'>";
        echo "<h2>🔍 RAW API DATA</h2>";
        echo "<h3>Recently Completed Data:</h3>";
        echo "<pre>" . json_encode($data['tables']['recentlyCompleted'] ?? [], JSON_PRETTY_PRINT) . "</pre>";
        echo "<h3>Programs Data:</h3>";
        echo "<pre>" . json_encode($programsData ?? [], JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='section error'>";
        echo "<h3>❌ ERROR OCCURRED</h3>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p>Line: " . $e->getLine() . "</p>";
        echo "</div>";
    }
    ?>
    
    <div class="section info">
        <h2>🎯 EXPECTED RESULTS</h2>
        <ul>
            <li><strong>Recently Completed:</strong> Should show 1 row per student with all their completions listed together</li>
            <li><strong>Board Exam Dropdown:</strong> Should load "Nursing Board Exam" and "Mechanical Engineer Board Exam" from database</li>
            <li><strong>Final Score Format:</strong> "Modules: Module1, Module2 | Courses: Course1, Course2, Course3"</li>
        </ul>
    </div>
</body>
</html>
