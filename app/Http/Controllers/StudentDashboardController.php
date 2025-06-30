<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated students can access these methods
        $this->middleware('student.auth');
    }

    public function dashboard()
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];
        
        // For now, we'll pass some dummy data
        // Later you can fetch real course data from database
        $courses = [
            [
                'id' => 1,
                'name' => 'Fundamentals of Engineering',
                'description' => 'Lorem ipsum dolor sit amet.',
                'progress' => 0,
                'status' => 'in_progress'
            ],
            [
                'id' => 2,
                'name' => 'Advanced Calculus',
                'description' => 'Lorem ipsum dolor sit amet.',
                'progress' => 15,
                'status' => 'in_progress'
            ]
        ];

        return view('student.student-dashboard', compact('user', 'courses'));
    }

    public function calendar()
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        return view('student.student-calendar', compact('user'));
    }

    public function course($courseId)
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        // For now, return a course view with dummy data
        // Later you can fetch real course data from database
        $course = [
            'id' => $courseId,
            'name' => 'Calculus 1',
            'description' => 'Introduction to differential and integral calculus',
            'progress' => 15,
            'modules' => [
                [
                    'id' => 1,
                    'name' => 'Introduction to Limits',
                    'status' => 'completed',
                    'progress' => 100
                ],
                [
                    'id' => 2,
                    'name' => 'Derivatives',
                    'status' => 'in_progress',
                    'progress' => 60
                ],
                [
                    'id' => 3,
                    'name' => 'Integration',
                    'status' => 'locked',
                    'progress' => 0
                ]
            ]
        ];

        return view('student.student-course', compact('user', 'course'));
    }
}
