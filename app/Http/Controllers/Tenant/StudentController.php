<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\UiSetting;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $tenant = $request->route('tenant');
        
        // Get customized student portal settings
        $settings = [
            'sidebar_color' => UiSetting::get('student_portal', 'student_sidebar_color', '#007bff'),
            'sidebar_text' => UiSetting::get('student_portal', 'student_sidebar_text', '#ffffff'),
            'dashboard_title' => UiSetting::get('student_portal', 'student_dashboard_title', 'Student Dashboard'),
            'welcome_message' => UiSetting::get('student_portal', 'student_welcome_message', 'Welcome to your student portal!'),
        ];
        
        // Mock data for demonstration
        $student = (object) [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'student_id' => 'STU001',
        ];
        
        $courses = collect([
            (object) ['id' => 1, 'name' => 'Introduction to Programming', 'progress' => 75, 'instructor' => 'Dr. Smith'],
            (object) ['id' => 2, 'name' => 'Web Development Basics', 'progress' => 60, 'instructor' => 'Prof. Johnson'],
            (object) ['id' => 3, 'name' => 'Database Design', 'progress' => 40, 'instructor' => 'Dr. Williams'],
        ]);
        
        $assignments = collect([
            (object) ['id' => 1, 'title' => 'HTML Structure Assignment', 'due_date' => '2024-01-15', 'status' => 'pending'],
            (object) ['id' => 2, 'title' => 'CSS Styling Project', 'due_date' => '2024-01-20', 'status' => 'submitted'],
            (object) ['id' => 3, 'title' => 'JavaScript Functions', 'due_date' => '2024-01-25', 'status' => 'upcoming'],
        ]);
        
        return view('tenant.student.dashboard', compact('tenant', 'student', 'courses', 'assignments', 'settings'));
    }
    
    public function courses(Request $request)
    {
        $tenant = $request->route('tenant');
        
        $courses = collect([
            (object) ['id' => 1, 'name' => 'Introduction to Programming', 'progress' => 75, 'instructor' => 'Dr. Smith'],
            (object) ['id' => 2, 'name' => 'Web Development Basics', 'progress' => 60, 'instructor' => 'Prof. Johnson'],
            (object) ['id' => 3, 'name' => 'Database Design', 'progress' => 40, 'instructor' => 'Dr. Williams'],
        ]);
        
        return view('tenant.student.courses', compact('tenant', 'courses'));
    }
    
    public function assignments(Request $request)
    {
        $tenant = $request->route('tenant');
        
        $assignments = collect([
            (object) ['id' => 1, 'title' => 'HTML Structure Assignment', 'due_date' => '2024-01-15', 'status' => 'pending'],
            (object) ['id' => 2, 'title' => 'CSS Styling Project', 'due_date' => '2024-01-20', 'status' => 'submitted'],
            (object) ['id' => 3, 'title' => 'JavaScript Functions', 'due_date' => '2024-01-25', 'status' => 'upcoming'],
        ]);
        
        return view('tenant.student.assignments', compact('tenant', 'assignments'));
    }
}
