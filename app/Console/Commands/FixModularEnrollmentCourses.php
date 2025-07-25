<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Models\Registration;
use App\Models\EnrollmentCourse;
use App\Models\Module;
use App\Models\Course;

class FixModularEnrollmentCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modular:fix-courses {--enrollment-id= : Specific enrollment ID to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing enrollment course records for modular enrollments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting modular enrollment course fix...');
        
        $enrollmentId = $this->option('enrollment-id');
        
        if ($enrollmentId) {
            $enrollments = Enrollment::where('enrollment_id', $enrollmentId)
                ->where('enrollment_type', 'Modular')
                ->get();
        } else {
            $enrollments = Enrollment::where('enrollment_type', 'Modular')->get();
        }
        
        if ($enrollments->isEmpty()) {
            $this->info('No modular enrollments found.');
            return;
        }
        
        $this->info("Found {$enrollments->count()} modular enrollments to check.");
        
        $fixedCount = 0;
        $skippedCount = 0;
        
        foreach ($enrollments as $enrollment) {
            $this->info("Checking enrollment ID: {$enrollment->enrollment_id}");
            
            // Check if enrollment course records already exist
            $existingCourseCount = $enrollment->enrollmentCourses()->count();
            if ($existingCourseCount > 0) {
                $this->info("  - Already has {$existingCourseCount} course records. Skipping.");
                $skippedCount++;
                continue;
            }
            
            // Get the registration to find selected courses
            $registration = Registration::where('user_id', $enrollment->user_id)
                ->where('program_id', $enrollment->program_id)
                ->where('enrollment_type', 'Modular')
                ->first();
            
            if (!$registration || !$registration->selected_modules) {
                $this->warn("  - No registration data found. Skipping.");
                continue;
            }
            
            $selectedModulesData = json_decode($registration->selected_modules, true);
            if (!is_array($selectedModulesData)) {
                $this->warn("  - Invalid selected modules data. Skipping.");
                continue;
            }
            
            $createdCourses = 0;
            $enrolledCourseIds = []; // Track to prevent duplicates
            
            foreach ($selectedModulesData as $moduleData) {
                $moduleId = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
                
                if (!$moduleId) continue;
                
                $this->info("  - Processing module ID: {$moduleId}");
                
                // If specific courses are selected for this module
                if (isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                    foreach ($moduleData['selected_courses'] as $courseData) {
                        $courseId = is_array($courseData) ? ($courseData['id'] ?? $courseData['course_id'] ?? $courseData) : $courseData;
                        
                        if ($courseId && !in_array($courseId, $enrolledCourseIds)) {
                            try {
                                EnrollmentCourse::create([
                                    'enrollment_id' => $enrollment->enrollment_id,
                                    'course_id' => $courseId,
                                    'module_id' => $moduleId,
                                    'enrollment_type' => 'course',
                                    'course_price' => 0,
                                    'is_active' => true
                                ]);
                                $enrolledCourseIds[] = $courseId;
                                $createdCourses++;
                                $this->info("    - Created course enrollment: Course ID {$courseId}");
                            } catch (\Exception $e) {
                                $this->error("    - Failed to create course enrollment for Course ID {$courseId}: " . $e->getMessage());
                            }
                        } elseif (in_array($courseId, $enrolledCourseIds)) {
                            $this->info("    - Skipping duplicate course: Course ID {$courseId}");
                        }
                    }
                } else {
                    // If no specific courses selected, enroll in all courses of the module
                    $module = Module::find($moduleId);
                    if ($module) {
                        $moduleCourses = Course::where('module_id', $moduleId)
                            ->where('is_active', true)
                            ->get();
                        
                        $this->info("    - No specific courses selected. Enrolling in all {$moduleCourses->count()} courses of module.");
                        
                        foreach ($moduleCourses as $course) {
                            if (!in_array($course->subject_id, $enrolledCourseIds)) {
                                try {
                                    EnrollmentCourse::create([
                                        'enrollment_id' => $enrollment->enrollment_id,
                                        'course_id' => $course->subject_id,
                                        'module_id' => $moduleId,
                                        'enrollment_type' => 'course',
                                        'course_price' => 0,
                                        'is_active' => true
                                    ]);
                                    $enrolledCourseIds[] = $course->subject_id;
                                    $createdCourses++;
                                    $this->info("    - Created course enrollment: Course ID {$course->subject_id} ({$course->subject_name})");
                                } catch (\Exception $e) {
                                    $this->error("    - Failed to create course enrollment for Course ID {$course->subject_id}: " . $e->getMessage());
                                }
                            } else {
                                $this->info("    - Skipping duplicate course: Course ID {$course->subject_id}");
                            }
                        }
                    } else {
                        $this->warn("    - Module not found for ID {$moduleId}");
                    }
                }
            }
            
            if ($createdCourses > 0) {
                $this->info("  - Created {$createdCourses} course enrollment records.");
                $fixedCount++;
            } else {
                $this->warn("  - No course enrollments created.");
            }
        }
        
        $this->info("Fix completed!");
        $this->info("Fixed enrollments: {$fixedCount}");
        $this->info("Skipped enrollments: {$skippedCount}");
    }
}
