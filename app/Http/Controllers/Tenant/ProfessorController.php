<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\UiSetting;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function dashboard(Request $request)
    {
        $tenant = $request->route('tenant');
        
        // Get customized professor panel settings
        $settings = [
            'sidebar_color' => UiSetting::get('professor_panel', 'professor_sidebar_color', '#28a745'),
            'sidebar_text' => UiSetting::get('professor_panel', 'professor_sidebar_text', '#ffffff'),
            'dashboard_title' => UiSetting::get('professor_panel', 'professor_dashboard_title', 'Professor Dashboard'),
            'welcome_message' => UiSetting::get('professor_panel', 'professor_welcome_message', 'Welcome to your professor panel!'),
        ];
        
        // Mock data for demonstration
        $professor = (object) [
            'name' => 'Dr. Jane Smith',
            'email' => 'jane.smith@example.com',
            'employee_id' => 'PROF001',
            'department' => 'Computer Science',
        ];
        
        $courses = collect([
            (object) ['id' => 1, 'name' => 'Introduction to Programming', 'students_count' => 25, 'semester' => 'Spring 2024'],
            (object) ['id' => 2, 'name' => 'Advanced Web Development', 'students_count' => 18, 'semester' => 'Spring 2024'],
            (object) ['id' => 3, 'name' => 'Software Engineering', 'students_count' => 22, 'semester' => 'Spring 2024'],
        ]);
        
        $students = collect([
            (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'grade' => 'A-'],
            (object) ['id' => 2, 'name' => 'Jane Johnson', 'email' => 'jane.johnson@example.com', 'grade' => 'B+'],
            (object) ['id' => 3, 'name' => 'Mike Wilson', 'email' => 'mike.wilson@example.com', 'grade' => 'A'],
        ]);
        
        $assignments = collect([
            (object) ['id' => 1, 'title' => 'Programming Project 1', 'due_date' => '2024-01-15', 'submissions' => 20],
            (object) ['id' => 2, 'title' => 'Web Development Assignment', 'due_date' => '2024-01-20', 'submissions' => 15],
            (object) ['id' => 3, 'title' => 'Final Project', 'due_date' => '2024-02-15', 'submissions' => 0],
        ]);
        
        return view('tenant.professor.dashboard', compact('tenant', 'professor', 'courses', 'students', 'assignments', 'settings'));
    }
    
    public function courses(Request $request)
    {
        $tenant = $request->route('tenant');
        
        $courses = collect([
            (object) ['id' => 1, 'name' => 'Introduction to Programming', 'students_count' => 25, 'semester' => 'Spring 2024'],
            (object) ['id' => 2, 'name' => 'Advanced Web Development', 'students_count' => 18, 'semester' => 'Spring 2024'],
            (object) ['id' => 3, 'name' => 'Software Engineering', 'students_count' => 22, 'semester' => 'Spring 2024'],
        ]);
        
        return view('tenant.professor.courses', compact('tenant', 'courses'));
    }
    
    public function students(Request $request)
    {
        $tenant = $request->route('tenant');
        
        $students = collect([
            (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'grade' => 'A-', 'course' => 'Intro Programming'],
            (object) ['id' => 2, 'name' => 'Jane Johnson', 'email' => 'jane.johnson@example.com', 'grade' => 'B+', 'course' => 'Web Development'],
            (object) ['id' => 3, 'name' => 'Mike Wilson', 'email' => 'mike.wilson@example.com', 'grade' => 'A', 'course' => 'Software Engineering'],
        ]);
        
        return view('tenant.professor.students', compact('tenant', 'students'));
    }
}
