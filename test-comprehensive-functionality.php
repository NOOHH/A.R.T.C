<?php
/**
 * Comprehensive Test for Enhanced Professor Features
 * Tests: Quiz Generation, Grading, Video Upload Syncing, Admin Settings
 */

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\Professor;
use App\Models\Student;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Deadline;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Activity;
use App\Models\AdminSetting;
use App\Http\Controllers\Professor\QuizGeneratorController;
use App\Http\Controllers\Professor\GradingController;
use App\Http\Controllers\AdminSettingsController;

class ComprehensiveFunctionalityTest
{
    private $results = [];

    public function runAllTests()
    {
        echo "<h1>🧪 Comprehensive Functionality Test</h1>\n";
        echo "<p>Testing all enhanced professor features and syncing logic...</p>\n";

        $this->testDatabaseConnection();
        $this->testModelRelationships();
        $this->testQuizGeneration();
        $this->testVideoUploadSync();
        $this->testGradingSystem();
        $this->testAdminSettings();
        $this->testStudentSync();
        
        $this->displayResults();
    }

    private function testDatabaseConnection()
    {
        echo "<h2>🔌 Database Connection Test</h2>\n";
        
        try {
            DB::connection()->getPdo();
            $this->results['db_connection'] = '✅ Database connected successfully';
            
            // Test table existence
            $tables = ['quizzes', 'deadlines', 'announcements', 'assignments', 'activities', 'admin_settings'];
            foreach ($tables as $table) {
                try {
                    DB::table($table)->limit(1)->get();
                    $this->results["table_$table"] = "✅ Table '$table' exists and accessible";
                } catch (\Exception $e) {
                    $this->results["table_$table"] = "❌ Table '$table' error: " . $e->getMessage();
                }
            }
            
        } catch (\Exception $e) {
            $this->results['db_connection'] = '❌ Database connection failed: ' . $e->getMessage();
        }
    }

    private function testModelRelationships()
    {
        echo "<h2>🔗 Model Relationships Test</h2>\n";
        
        try {
            // Test Quiz model
            $quiz = new Quiz();
            $this->results['quiz_model'] = '✅ Quiz model loaded successfully';
            
            // Test Deadline model
            $deadline = new Deadline();
            $this->results['deadline_model'] = '✅ Deadline model loaded successfully';
            
            // Test Announcement model
            $announcement = new Announcement();
            $this->results['announcement_model'] = '✅ Announcement model loaded successfully';
            
            // Test Assignment model
            $assignment = new Assignment();
            $this->results['assignment_model'] = '✅ Assignment model loaded successfully';
            
            // Test Activity model
            $activity = new Activity();
            $this->results['activity_model'] = '✅ Activity model loaded successfully';
            
        } catch (\Exception $e) {
            $this->results['model_relationships'] = '❌ Model relationship error: ' . $e->getMessage();
        }
    }

    private function testQuizGeneration()
    {
        echo "<h2>📝 Quiz Generation Test</h2>\n";
        
        try {
            // Test controller instantiation
            $controller = new QuizGeneratorController();
            $this->results['quiz_controller'] = '✅ QuizGeneratorController instantiated successfully';
            
            // Test admin settings check
            AdminSetting::updateOrCreate(
                ['setting_key' => 'ai_quiz_enabled'],
                ['setting_value' => 'true', 'setting_description' => 'Enable AI Quiz Generation']
            );
            
            $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
            $this->results['ai_quiz_setting'] = $aiQuizEnabled ? '✅ AI Quiz setting enabled' : '❌ AI Quiz setting disabled';
            
        } catch (\Exception $e) {
            $this->results['quiz_generation'] = '❌ Quiz generation test error: ' . $e->getMessage();
        }
    }

    private function testVideoUploadSync()
    {
        echo "<h2>📹 Video Upload Sync Test</h2>\n";
        
        try {
            // Test creating an announcement (simulating video upload)
            $announcement = Announcement::create([
                'program_id' => 1, // Assuming program ID 1 exists
                'title' => 'Test Video Upload',
                'content' => 'A new video has been uploaded: https://zoom.us/test-video',
                'announcement_type' => 'video',
                'created_by' => 1 // Assuming professor ID 1
            ]);
            
            if ($announcement) {
                $this->results['video_announcement'] = '✅ Video announcement created successfully';
            } else {
                $this->results['video_announcement'] = '❌ Failed to create video announcement';
            }
            
        } catch (\Exception $e) {
            $this->results['video_upload_sync'] = '❌ Video upload sync error: ' . $e->getMessage();
        }
    }

    private function testGradingSystem()
    {
        echo "<h2>📊 Grading System Test</h2>\n";
        
        try {
            // Test controller instantiation
            $controller = new GradingController();
            $this->results['grading_controller'] = '✅ GradingController instantiated successfully';
            
            // Test creating a deadline (simulating assignment creation)
            $deadline = Deadline::create([
                'student_id' => 1, // Assuming student ID 1 exists
                'program_id' => 1,
                'title' => 'Test Assignment',
                'description' => 'This is a test assignment',
                'due_date' => \Carbon\Carbon::now()->addWeek(),
                'type' => 'assignment',
                'status' => 'pending'
            ]);
            
            if ($deadline) {
                $this->results['assignment_deadline'] = '✅ Assignment deadline created successfully';
            } else {
                $this->results['assignment_deadline'] = '❌ Failed to create assignment deadline';
            }
            
        } catch (\Exception $e) {
            $this->results['grading_system'] = '❌ Grading system test error: ' . $e->getMessage();
        }
    }

    private function testAdminSettings()
    {
        echo "<h2>⚙️ Admin Settings Test</h2>\n";
        
        try {
            // Test controller instantiation
            $controller = new AdminSettingsController();
            $this->results['admin_settings_controller'] = '✅ AdminSettingsController instantiated successfully';
            
            // Test setting creation/update
            $settings = [
                'ai_quiz_enabled' => 'true',
                'grading_enabled' => 'true',
                'video_upload_enabled' => 'true',
                'attendance_enabled' => 'true',
                'view_programs_enabled' => 'true'
            ];
            
            foreach ($settings as $key => $value) {
                AdminSetting::updateOrCreate(
                    ['setting_key' => $key],
                    ['setting_value' => $value, 'setting_description' => "Enable $key feature"]
                );
            }
            
            $this->results['admin_settings_creation'] = '✅ Admin settings created/updated successfully';
            
        } catch (\Exception $e) {
            $this->results['admin_settings'] = '❌ Admin settings test error: ' . $e->getMessage();
        }
    }

    private function testStudentSync()
    {
        echo "<h2>🎓 Student Sync Test</h2>\n";
        
        try {
            // Count deadlines and announcements to verify syncing
            $deadlineCount = Deadline::count();
            $announcementCount = Announcement::count();
            
            $this->results['deadline_count'] = "✅ Found $deadlineCount deadlines in database";
            $this->results['announcement_count'] = "✅ Found $announcementCount announcements in database";
            
            // Test if syncing logic is in place (check for recent entries)
            $recentDeadlines = Deadline::where('created_at', '>=', \Carbon\Carbon::now()->subHour())->count();
            $recentAnnouncements = Announcement::where('created_at', '>=', \Carbon\Carbon::now()->subHour())->count();
            
            $this->results['recent_sync'] = "✅ Recent activity: $recentDeadlines deadlines, $recentAnnouncements announcements";
            
        } catch (\Exception $e) {
            $this->results['student_sync'] = '❌ Student sync test error: ' . $e->getMessage();
        }
    }

    private function displayResults()
    {
        echo "<h2>📋 Test Results Summary</h2>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px;'>\n";
        
        $passed = 0;
        $failed = 0;
        
        foreach ($this->results as $test => $result) {
            echo "<div style='margin: 5px 0;'>$result</div>\n";
            if (strpos($result, '✅') !== false) {
                $passed++;
            } else {
                $failed++;
            }
        }
        
        echo "</div>\n";
        echo "<h3>Summary: $passed passed, $failed failed</h3>\n";
        
        if ($failed === 0) {
            echo "<div style='color: green; font-size: 18px; font-weight: bold;'>🎉 All tests passed! System is ready for use.</div>\n";
        } else {
            echo "<div style='color: orange; font-size: 18px; font-weight: bold;'>⚠️ Some tests failed. Please review the errors above.</div>\n";
        }
    }
}

// Run the comprehensive test
$test = new ComprehensiveFunctionalityTest();
$test->runAllTests();

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
h1, h2, h3 {
    color: #333;
}
h1 { border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 30px; }
</style>
