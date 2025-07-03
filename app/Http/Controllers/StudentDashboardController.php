<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Module;
use App\Models\ModuleCompletion;

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
        
        // Get the student data
        $student = Student::where('user_id', session('user_id'))->first();
        
        // Prepare courses/programs data
        $courses = [];
        
        if ($student) {
            // Get all enrollments for this student
            $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
                ->with('program')
                ->get();
                
            // If student has enrollments, get their programs
            if ($enrollments->count() > 0) {
                foreach ($enrollments as $enrollment) {
                    // Skip if program is archived or not found
                    if (!$enrollment->program || $enrollment->program->is_archived) {
                        continue;
                    }
                    
                    $program = $enrollment->program;
                    
                    // Get module count for progress calculation
                    $totalModules = Module::where('program_id', $program->program_id)
                                        ->where('is_archived', false)
                                        ->count();
                    
                    // Get completed modules count
                    $completedModules = ModuleCompletion::where('student_id', $student->student_id)
                                            ->where('program_id', $program->program_id)
                                            ->count();
                    
                    // Calculate progress based on completed modules
                    $progressPercentage = 0;
                    if ($totalModules > 0) {
                        $progressPercentage = round(($completedModules / $totalModules) * 100);
                    }
                    
                    // Add this program to the courses array
                    $courses[] = [
                        'id' => $program->program_id,
                        'name' => $program->program_name,
                        'description' => $program->program_description ?? 'No description available.',
                        'progress' => $progressPercentage,
                        'total_modules' => $totalModules,
                        'completed_modules' => $completedModules
                    ];
                }
            }
            // If no enrollments but student has a direct program_id, use that
            elseif ($student->program_id) {
                $program = Program::where('program_id', $student->program_id)
                                ->where('is_archived', false)
                                ->first();
                                
                if ($program) {
                    $totalModules = Module::where('program_id', $program->program_id)
                                        ->where('is_archived', false)
                                        ->count();
                    
                    // Get completed modules count
                    $completedModules = ModuleCompletion::where('student_id', $student->student_id)
                                            ->where('program_id', $program->program_id)
                                            ->count();
                    
                    // Calculate progress based on completed modules
                    $progressPercentage = 0;
                    if ($totalModules > 0) {
                        $progressPercentage = round(($completedModules / $totalModules) * 100);
                    }
                    
                    $courses[] = [
                        'id' => $program->program_id,
                        'name' => $program->program_name,
                        'description' => $program->program_description ?? 'No description available.',
                        'progress' => $progressPercentage,
                        'total_modules' => $totalModules,
                        'completed_modules' => $completedModules
                    ];
                }
            }
        }

        return view('student.student-dashboard.student-dashboard', compact('courses'));
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

        return view('student.student-calendar.student-calendar', compact('user'));
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

        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Student account not found.');
        }
        
        // Fetch course data from database
        $program = Program::find($courseId);
        
        if (!$program) {
            // If program not found, redirect back to dashboard
            return redirect()->route('student.dashboard')->with('error', 'Course not found.');
        }
        
        // Check if student is enrolled in this program (either via Enrollments or direct program_id)
        $isEnrolled = false;
        
        // Check enrollment table
        $enrollment = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('program_id', $courseId)
            ->first();
            
        if ($enrollment || $student->program_id == $courseId) {
            $isEnrolled = true;
        }
        
        if (!$isEnrolled) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You are not enrolled in this course. Please contact your administrator.');
        }
        
        // Get all modules for this program, ordered by module_order, then creation date
        $modules = Module::where('program_id', $courseId)
                        ->where('is_archived', false)
                        ->orderBy('module_order', 'asc')
                        ->orderBy('created_at', 'asc')
                        ->get();
        
        // Get completed modules for this student in this program
        $completedModuleIds = ModuleCompletion::where('student_id', $student->student_id)
                                        ->where('program_id', $courseId)
                                        ->pluck('module_id')
                                        ->toArray();
        
        $completedModules = count($completedModuleIds);
        
        // Calculate progress percentage
        $progressPercentage = 0;
        if ($modules->count() > 0) {
            $progressPercentage = round(($completedModules / $modules->count()) * 100);
        }
        
        // Group modules by type for better organization
        $modulesByType = [
            'module' => [],
            'assignment' => [],
            'quiz' => [],
            'test' => [],
            'link' => []
        ];
        
        // Format modules for the view
        $formattedModules = [];
        foreach ($modules as $index => $module) {
            // Check if this module is completed by the student
            $isCompleted = in_array($module->modules_id, $completedModuleIds);
            
            // Determine if module should be locked
            // Logic: a module is locked if there are more than 2 uncompleted modules before it
            // This allows students to work ahead a little bit, but not skip too much content
            $uncompletedCount = 0;
            for ($i = 0; $i < $index; $i++) {
                if (!in_array($modules[$i]->modules_id, $completedModuleIds)) {
                    $uncompletedCount++;
                }
            }
            $isLocked = $uncompletedCount > 2;
            
            $moduleData = [
                'id' => $module->modules_id,
                'title' => $module->module_name,
                'description' => $module->module_description ?? '',
                'type' => $module->content_type ?? 'module',
                'is_locked' => $isLocked,
                'is_completed' => $isCompleted,
                'order' => $module->module_order ?? ($index + 1),
                'attachment' => $module->attachment,
                'content_data' => $module->content_data
            ];
            
            $formattedModules[] = $moduleData;
            
            // Also add to grouped array
            $type = $module->content_type ?? 'module';
            if (isset($modulesByType[$type])) {
                $modulesByType[$type][] = $moduleData;
            } else {
                $modulesByType['module'][] = $moduleData; // Default fallback
            }
        }

        return view('student.student-courses.student-course', [
            'program' => $program,
            'modules' => $formattedModules,
            'modulesByType' => $modulesByType,
            'progress' => $progressPercentage,
            'completedModules' => $completedModules,
            'totalModules' => count($formattedModules)
        ]);
    }

    public function settings()
    {
        // Get user data from session
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to access settings.');
        }

        // Fetch student data from the database
        $student = Student::where('user_id', $userId)->first();
        
        if (!$student) {
            // Create a default student record if it doesn't exist
            $student = new Student();
            $student->user_id = $userId;
            $student->student_id = 'TEMP_' . $userId;
            $student->firstname = '';
            $student->lastname = '';
            $student->email = session('user_email', '');
        }

        return view('student.settings', compact('student'));
    }

    public function updateSettings(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to update settings.');
        }

        // Validate the request
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'student_school' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:500',
            'state_province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
            'emergency_contact_number' => 'nullable|string|max:20',
            'Start_Date' => 'nullable|date',
            'education' => 'nullable|in:Undergraduate,Graduate',
            // File uploads
            'good_moral' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'PSA' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'Course_Cert' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'TOR' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'Cert_of_Grad' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo_2x2' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ]);

        try {
            // Find or create the student record
            $student = Student::where('user_id', $userId)->first();
            
            if (!$student) {
                $student = new Student();
                $student->user_id = $userId;
                $student->student_id = 'TEMP_' . $userId . '_' . time();
                $student->email = session('user_email', '');
            }

            // Update the student information
            $student->fill($validated);
            
            // Handle education level
            if ($request->has('education_level')) {
                $student->Undergraduate = $request->education_level === 'undergraduate' ? 1 : 0;
                $student->Graduate = $request->education_level === 'graduate' ? 1 : 0;
            }
            
            // Handle file uploads
            $fileFields = ['good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 'photo_2x2'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student_documents', $fileName, 'public');
                    $student->$field = $filePath;
                }
            }
            
            $student->save();

            return redirect()->route('student.settings')->with('success', 'Your information has been updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Student settings update failed: ' . $e->getMessage());
            return redirect()->route('student.settings')
                ->withInput()
                ->with('error', 'Failed to update your information. Please try again.');
        }
    }

    /**
     * Display a specific module's content
     */
    public function module($moduleId)
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Student account not found.');
        }
        
        // Fetch module data
        $module = Module::find($moduleId);
        
        if (!$module) {
            return redirect()->route('student.dashboard')->with('error', 'Module not found.');
        }
        
        // Get the program for this module
        $program = Program::find($module->program_id);
        
        if (!$program) {
            return redirect()->route('student.dashboard')->with('error', 'Program not found.');
        }
        
        // Check if student is enrolled in this program
        $isEnrolled = false;
        
        // Check enrollment table
        $enrollment = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('program_id', $module->program_id)
            ->first();
            
        if ($enrollment || $student->program_id == $module->program_id) {
            $isEnrolled = true;
        }
        
        if (!$isEnrolled) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You are not enrolled in this course. Please contact your administrator.');
        }
        
        // Check if this module has been completed
        $isCompleted = ModuleCompletion::where('student_id', $student->student_id)
                                    ->where('module_id', $moduleId)
                                    ->exists();
        
        // Format the module data for the view
        $moduleData = [
            'id' => $module->modules_id,
            'title' => $module->module_name,
            'description' => $module->module_description ?? '',
            'type' => $module->content_type ?? 'module',
            'content_data' => $module->content_data ?? [],
            'attachment' => $module->attachment,
            'program_id' => $module->program_id,
            'program_name' => $program->program_name,
            'is_completed' => $isCompleted
        ];
        
        return view('student.student-courses.student-module', [
            'module' => $moduleData,
            'program' => $program
        ]);
    }
    
    /**
     * Mark a module as complete via AJAX
     */
    public function markModuleComplete(Request $request, $moduleId)
    {
        // Get student info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student account not found.'], 404);
        }
        
        // Fetch module data
        $module = Module::find($moduleId);
        
        if (!$module) {
            return response()->json(['success' => false, 'message' => 'Module not found.'], 404);
        }
        
        // Check if this module completion already exists
        $existing = ModuleCompletion::where('student_id', $student->student_id)
                                ->where('module_id', $moduleId)
                                ->first();
                                
        if ($existing) {
            return response()->json(['success' => true, 'message' => 'Module already marked as complete.']);
        }
        
        try {
            // Create new module completion record
            $completion = new ModuleCompletion();
            $completion->student_id = $student->student_id;
            $completion->module_id = $moduleId;
            $completion->program_id = $module->program_id;
            $completion->completed_at = now();
            
            // Handle different submission types
            if ($request->has('submission_data')) {
                $completion->submission_data = $request->input('submission_data');
            }
            
            if ($request->has('score')) {
                $completion->score = $request->input('score');
            }
            
            $completion->save();
            
            // Calculate new progress for the program
            $totalModules = Module::where('program_id', $module->program_id)
                                ->where('is_archived', false)
                                ->count();
                                
            $completedModules = ModuleCompletion::where('student_id', $student->student_id)
                                            ->where('program_id', $module->program_id)
                                            ->count();
                                            
            $progressPercentage = 0;
            if ($totalModules > 0) {
                $progressPercentage = round(($completedModules / $totalModules) * 100);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Module marked as complete.',
                'progress' => $progressPercentage
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking module as complete: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while marking the module as complete.'
            ], 500);
        }
    }
}
