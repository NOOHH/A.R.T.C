<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseCompletion;
use App\Models\ContentCompletion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CompletionController extends Controller
{
    public function markCourseComplete(Request $request)
    {
        Log::info('DEBUG: markCourseComplete session user_id', ['user_id' => session('user_id')]);
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        Log::info('DEBUG: markCourseComplete student lookup', ['student' => $student]);
        $request->validate([
            'course_id' => 'required|integer',
            'module_id' => 'nullable|integer',
        ]);
        $userId = session('user_id');
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 401);
        }
        $studentId = $student->student_id;
        $courseId = $request->input('course_id');
        $moduleId = $request->input('module_id');

        $completion = CourseCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'module_id' => $moduleId,
            ],
            [
                'completed_at' => Carbon::now(),
            ]
        );
        $completion->completed_at = Carbon::now();
        $completion->save();
        
        Log::info('Course completion saved/updated', [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'module_id' => $moduleId,
            'completion_id' => $completion->id
        ]);

        // Check if module should be auto-completed
        if ($moduleId) {
            $this->checkAndAutoCompleteModule($studentId, $moduleId);
        }

        // Calculate and return progress information for dashboard updates
        $progressData = $this->calculateStudentProgress($studentId);
        
        return response()->json([
            'success' => true,
            'progress_percentage' => $progressData['progress_percentage'],
            'completed_modules' => $progressData['completed_modules'],
            'total_modules' => $progressData['total_modules'],
            'type' => 'course',
            'course_id' => $courseId,
            'module_id' => $moduleId
        ]);
    }

    public function markContentComplete(Request $request)
    {
        Log::info('DEBUG: markContentComplete session user_id', ['user_id' => session('user_id')]);
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        Log::info('DEBUG: markContentComplete student lookup', ['student' => $student]);
        $request->validate([
            'content_id' => 'required|integer',
            'course_id' => 'nullable|integer',
            'module_id' => 'nullable|integer',
        ]);
        $userId = session('user_id');
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 401);
        }
        $studentId = $student->student_id;
        $contentId = $request->input('content_id');
        $courseId = $request->input('course_id');
        $moduleId = $request->input('module_id');

        Log::info('DEBUG: markContentComplete processing', [
            'student_id' => $studentId,
            'content_id' => $contentId,
            'course_id' => $courseId,
            'module_id' => $moduleId
        ]);

        // Use ContentCompletion model for content completions
        $completion = ContentCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'content_id' => $contentId,
            ],
            [
                'course_id' => $courseId,
                'module_id' => $moduleId,
                'completed_at' => Carbon::now(),
            ]
        );
        $completion->completed_at = Carbon::now();
        $completion->save();
        
        Log::info('Content completion saved', [
            'student_id' => $studentId,
            'content_id' => $contentId,
            'course_id' => $courseId,
            'module_id' => $moduleId
        ]);

        // Check if all content in the course is completed for auto-course completion
        if ($courseId && $moduleId) {
            Log::info('DEBUG: Checking auto-course completion', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'module_id' => $moduleId
            ]);
            $this->checkAndAutoCompleteCourse($studentId, $courseId, $moduleId);
        }

        // Calculate and return progress information for dashboard updates
        Log::info('DEBUG: Calculating student progress', ['student_id' => $studentId]);
        $progressData = $this->calculateStudentProgress($studentId);
        
        Log::info('DEBUG: Progress data calculated', [
            'student_id' => $studentId,
            'progress_percentage' => $progressData['progress_percentage'],
            'completed_modules' => $progressData['completed_modules'],
            'total_modules' => $progressData['total_modules']
        ]);
        
        return response()->json([
            'success' => true,
            'progress_percentage' => $progressData['progress_percentage'],
            'completed_modules' => $progressData['completed_modules'],
            'total_modules' => $progressData['total_modules'],
            'type' => 'content',
            'content_id' => $contentId,
            'course_id' => $courseId,
            'module_id' => $moduleId
        ]);
    }

    public function markModuleComplete(Request $request)
    {
        $request->validate([
            'module_id' => 'required|integer',
        ]);
        
        // Fix: Get student properly using session user_id
        $userId = session('user_id');
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 401);
        }
        $studentId = $student->student_id;
        $moduleId = $request->input('module_id');

        // Check if all content items for this module are completed by the student
        // Get all courses in this module first, then get their content items
        $courseIds = \App\Models\Course::where('module_id', $moduleId)->pluck('subject_id')->toArray();
        $allContentIds = \App\Models\ContentItem::whereIn('course_id', $courseIds)->pluck('id')->toArray();
        
        $completedContentIds = ContentCompletion::where('student_id', $studentId)
            ->where('module_id', $moduleId)
            ->pluck('content_id')
            ->toArray();
            
        $allCompleted = empty(array_diff($allContentIds, $completedContentIds));
        if (!$allCompleted && count($allContentIds) > 0) {
            return response()->json(['success' => false, 'message' => 'You must complete all course content before marking this module as complete.']);
        }

        // Fix: Use correct field name 'modules_id' instead of 'module_id'
        $completion = \App\Models\ModuleCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'modules_id' => $moduleId, // Fixed: was 'module_id'
            ],
            [
                'completed_at' => Carbon::now(),
            ]
        );
        $completion->completed_at = Carbon::now();
        $completion->save();
        return response()->json(['success' => true]);
    }

    /**
     * Check if all content in a course is completed and auto-complete the course
     */
    private function checkAndAutoCompleteCourse($studentId, $courseId, $moduleId)
    {
        // Get all content items for this course (courseId is actually subject_id)
        $allContentIds = \App\Models\ContentItem::where('course_id', $courseId)
            ->pluck('id')
            ->toArray();

        // Get completed content for this course
        $completedContentIds = ContentCompletion::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('module_id', $moduleId)
            ->pluck('content_id')
            ->toArray();

        // Check if all content is completed
        $allCompleted = empty(array_diff($allContentIds, $completedContentIds));

        if ($allCompleted && count($allContentIds) > 0) {
            // Get the program_id for this module
            $module = \App\Models\Module::where('modules_id', $moduleId)->first();
            $programId = $module ? $module->program_id : null;

            // Auto-complete the course
            CourseCompletion::firstOrCreate(
                [
                    'student_id' => $studentId,
                    'course_id' => $courseId, // This stores subject_id
                    'module_id' => $moduleId,
                ],
                [
                    'program_id' => $programId,
                    'completed_at' => Carbon::now(),
                ]
            );

            Log::info('Course auto-completed', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'module_id' => $moduleId,
                'program_id' => $programId
            ]);

            // Check if module should be auto-completed
            $this->checkAndAutoCompleteModule($studentId, $moduleId);
        }
    }

    /**
     * Check if all courses in a module are completed and auto-complete the module
     */
    private function checkAndAutoCompleteModule($studentId, $moduleId)
    {
        Log::info('DEBUG: checkAndAutoCompleteModule called', [
            'student_id' => $studentId,
            'module_id' => $moduleId
        ]);

        // Validate that moduleId is actually a valid module ID, not a course ID
        $module = \App\Models\Module::where('modules_id', $moduleId)->first();
        if (!$module) {
            Log::error('Invalid module ID in checkAndAutoCompleteModule', [
                'student_id' => $studentId,
                'invalid_module_id' => $moduleId
            ]);
            return; // Don't proceed if module doesn't exist
        }

        // Get all courses for this module (using subject_id as the course identifier)
        $allCourseIds = \App\Models\Course::where('module_id', $moduleId)
            ->pluck('subject_id') // Use subject_id instead of course_id
            ->toArray();

        // Get completed courses for this module
        $completedCourseIds = CourseCompletion::where('student_id', $studentId)
            ->where('module_id', $moduleId)
            ->pluck('course_id') // This stores subject_id values
            ->toArray();

        Log::info('DEBUG: Module completion check', [
            'student_id' => $studentId,
            'module_id' => $moduleId,
            'all_courses' => $allCourseIds,
            'completed_courses' => $completedCourseIds
        ]);

        // Check if all courses are completed
        $allCompleted = empty(array_diff($allCourseIds, $completedCourseIds));

        if ($allCompleted && count($allCourseIds) > 0) {
            $programId = $module->program_id;

            Log::info('About to auto-complete module', [
                'student_id' => $studentId,
                'module_id' => $moduleId,
                'program_id' => $programId,
                'all_courses' => $allCourseIds,
                'completed_courses' => $completedCourseIds
            ]);

            // Auto-complete the module
            $moduleCompletion = \App\Models\ModuleCompletion::firstOrCreate(
                [
                    'student_id' => $studentId,
                    'modules_id' => $moduleId, // This should be the actual modules_id, not course_id
                ],
                [
                    'program_id' => $programId,
                    'completed_at' => Carbon::now(),
                ]
            );

            Log::info('Module auto-completed', [
                'student_id' => $studentId,
                'module_id' => $moduleId,
                'program_id' => $programId,
                'module_completion_id' => $moduleCompletion->id
            ]);
        } else {
            Log::info('Module not ready for auto-completion', [
                'student_id' => $studentId,
                'module_id' => $moduleId,
                'all_completed' => $allCompleted,
                'course_count' => count($allCourseIds)
            ]);
        }
    }

    /**
     * Check if a module should be completed (endpoint for frontend)
     */
    public function checkModuleCompletion(Request $request, $moduleId)
    {
        // Fix: Get student properly using session user_id
        $userId = session('user_id');
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 401);
        }
        $studentId = $student->student_id;

        // Get all courses for this module (using subject_id as the course identifier)
        $allCourseIds = \App\Models\Course::where('module_id', $moduleId)
            ->pluck('subject_id') // Use subject_id instead of course_id
            ->toArray();

        // Get completed courses for this module
        $completedCourseIds = CourseCompletion::where('student_id', $studentId)
            ->where('module_id', $moduleId)
            ->pluck('course_id') // This stores subject_id values
            ->toArray();

        // Check if all courses are completed
        $allCompleted = empty(array_diff($allCourseIds, $completedCourseIds));
        $shouldComplete = $allCompleted && count($allCourseIds) > 0;

        return response()->json([
            'success' => true,
            'should_complete' => $shouldComplete,
            'total_courses' => count($allCourseIds),
            'completed_courses' => count($completedCourseIds)
        ]);
    }

    /**
     * Calculate student progress for dashboard updates
     * Enhanced to handle modular enrollments based on enrolled courses
     * Made public so other controllers can use this method
     */
    public function calculateStudentProgress($studentId)
    {
        // Get student's enrolled programs
        $student = \App\Models\Student::where('student_id', $studentId)->first();
        if (!$student) {
            return ['progress_percentage' => 0, 'completed_modules' => 0, 'total_modules' => 0];
        }

        // Get student's enrollments to find their programs
        $enrollments = \App\Models\Enrollment::where('student_id', $studentId)
            ->orWhere('user_id', $student->user_id)
            ->with('program')
            ->get();

        if ($enrollments->isEmpty()) {
            return ['progress_percentage' => 0, 'completed_modules' => 0, 'total_modules' => 0];
        }

        $totalContentItems = 0;
        $completedContentItems = 0;
        $totalModules = 0;
        $completedModules = 0;

        foreach ($enrollments as $enrollment) {
            if (!$enrollment->program) continue;

            Log::info('Processing enrollment', [
                'enrollment_id' => $enrollment->enrollment_id,
                'program_id' => $enrollment->program->program_id,
                'enrollment_type' => $enrollment->enrollment_type ?? 'full'
            ]);

            // Check if this is a modular enrollment
            if (isset($enrollment->enrollment_type) && $enrollment->enrollment_type === 'Modular') {
                // For modular enrollments, calculate based on enrolled courses only
                $enrolledCourseIds = $enrollment->enrollmentCourses()
                    ->where('is_active', true)
                    ->pluck('course_id')
                    ->toArray();
                
                Log::info('Modular enrollment - enrolled courses', [
                    'enrollment_id' => $enrollment->enrollment_id,
                    'enrolled_courses' => $enrolledCourseIds
                ]);

                if (!empty($enrolledCourseIds)) {
                    // Get all content items for enrolled courses
                    $courseContentItems = \App\Models\ContentItem::whereIn('course_id', $enrolledCourseIds)->count();
                    $totalContentItems += $courseContentItems;

                    // Get completed content items for enrolled courses
                    $completedCourseContent = \App\Models\ContentCompletion::where('student_id', $studentId)
                        ->whereIn('course_id', $enrolledCourseIds)
                        ->count();
                    $completedContentItems += $completedCourseContent;

                    // For modular, also count module completions based on enrolled courses
                    $moduleIdsWithEnrolledCourses = \App\Models\Course::whereIn('subject_id', $enrolledCourseIds)
                        ->pluck('module_id')
                        ->unique()
                        ->toArray();
                    
                    $totalModules += count($moduleIdsWithEnrolledCourses);
                    
                    $completedModulesForEnrollment = \App\Models\ModuleCompletion::where('student_id', $studentId)
                        ->where('program_id', $enrollment->program->program_id)
                        ->whereIn('modules_id', $moduleIdsWithEnrolledCourses)
                        ->count();
                    $completedModules += $completedModulesForEnrollment;

                    Log::info('Modular progress calculation', [
                        'course_content_items' => $courseContentItems,
                        'completed_content' => $completedCourseContent,
                        'modules_with_courses' => $moduleIdsWithEnrolledCourses,
                        'completed_modules' => $completedModulesForEnrollment
                    ]);
                }
            } else {
                // For full program enrollments, use traditional module-based calculation
                $programModules = \App\Models\Module::where('program_id', $enrollment->program->program_id)->count();
                $totalModules += $programModules;

                $programCompletedModules = \App\Models\ModuleCompletion::where('student_id', $studentId)
                    ->where('program_id', $enrollment->program->program_id)
                    ->count();
                $completedModules += $programCompletedModules;

                // Also get content completion for full programs
                $allProgramCourses = \App\Models\Module::where('program_id', $enrollment->program->program_id)
                    ->with('courses')
                    ->get()
                    ->pluck('courses')
                    ->flatten()
                    ->pluck('subject_id')
                    ->toArray();

                if (!empty($allProgramCourses)) {
                    $programContentItems = \App\Models\ContentItem::whereIn('course_id', $allProgramCourses)->count();
                    $totalContentItems += $programContentItems;

                    $programCompletedContent = \App\Models\ContentCompletion::where('student_id', $studentId)
                        ->whereIn('course_id', $allProgramCourses)
                        ->count();
                    $completedContentItems += $programCompletedContent;
                }

                Log::info('Full program progress calculation', [
                    'program_id' => $enrollment->program->program_id,
                    'total_modules' => $programModules,
                    'completed_modules' => $programCompletedModules,
                    'content_items' => $programContentItems ?? 0,
                    'completed_content' => $programCompletedContent ?? 0
                ]);
            }
        }

        // Calculate progress based on content completion (more granular)
        $contentProgressPercentage = $totalContentItems > 0 ? round(($completedContentItems / $totalContentItems) * 100, 1) : 0;
        
        // Also calculate module-based progress for compatibility
        $moduleProgressPercentage = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 1) : 0;

        // Use content-based progress as primary, fall back to module-based
        $finalProgressPercentage = $totalContentItems > 0 ? $contentProgressPercentage : $moduleProgressPercentage;

        Log::info('Final progress calculation', [
            'student_id' => $studentId,
            'total_content_items' => $totalContentItems,
            'completed_content_items' => $completedContentItems,
            'content_progress_percentage' => $contentProgressPercentage,
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'module_progress_percentage' => $moduleProgressPercentage,
            'final_progress_percentage' => $finalProgressPercentage
        ]);

        return [
            'progress_percentage' => $finalProgressPercentage,
            'completed_modules' => $completedModules,
            'total_modules' => $totalModules,
            'completed_content' => $completedContentItems,
            'total_content' => $totalContentItems
        ];
    }
} 