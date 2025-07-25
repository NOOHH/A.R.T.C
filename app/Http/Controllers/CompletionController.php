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
        return response()->json(['success' => true]);
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
            $this->checkAndAutoCompleteCourse($studentId, $courseId, $moduleId);
        }

        // Calculate and return progress information for dashboard updates
        $progressData = $this->calculateStudentProgress($studentId);
        
        return response()->json([
            'success' => true,
            'progress_percentage' => $progressData['progress_percentage'],
            'completed_modules' => $progressData['completed_modules'],
            'total_modules' => $progressData['total_modules']
        ]);
    }

    public function markModuleComplete(Request $request)
    {
        $request->validate([
            'module_id' => 'required|integer',
        ]);
        $studentId = Auth::id();
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

        // Assuming you have a ModuleCompletion model/table
        $completion = \App\Models\ModuleCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'module_id' => $moduleId,
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
            \App\Models\ModuleCompletion::firstOrCreate(
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
                'program_id' => $programId
            ]);
        }
    }

    /**
     * Check if a module should be completed (endpoint for frontend)
     */
    public function checkModuleCompletion(Request $request, $moduleId)
    {
        $student = session('user_id') ? \App\Models\Student::where('student_id', session('user_id'))->first() : null;
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
     */
    private function calculateStudentProgress($studentId)
    {
        // Get student's enrolled program
        $student = \App\Models\Student::where('student_id', $studentId)->first();
        if (!$student) {
            return ['progress_percentage' => 0, 'completed_modules' => 0, 'total_modules' => 0];
        }

        // Get all modules for student's program
        $totalModules = \App\Models\Module::where('program_id', $student->program_id)->count();
        
        // Get completed modules
        $completedModules = \App\Models\ModuleCompletion::where('student_id', $studentId)
            ->whereHas('module', function($query) use ($student) {
                $query->where('program_id', $student->program_id);
            })
            ->count();

        $progressPercentage = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 1) : 0;

        return [
            'progress_percentage' => $progressPercentage,
            'completed_modules' => $completedModules,
            'total_modules' => $totalModules
        ];
    }
} 