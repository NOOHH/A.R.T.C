<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Models\Registration;
use App\Models\EnrollmentCourse;
use Illuminate\Support\Facades\DB;

class DebugModularEnrollment extends Command
{
    protected $signature = 'debug:modular-enrollment {enrollment-id}';
    protected $description = 'Debug a specific modular enrollment';

    public function handle()
    {
        $enrollmentId = $this->argument('enrollment-id');
        
        $this->info("=== DEBUGGING ENROLLMENT $enrollmentId ===");
        
        // Get enrollment details
        $enrollment = Enrollment::find($enrollmentId);
        
        if (!$enrollment) {
            $this->error("Enrollment not found!");
            return;
        }
        
        $this->info("Enrollment Details:");
        $this->line("- ID: {$enrollment->enrollment_id}");
        $this->line("- User ID: {$enrollment->user_id}");
        $this->line("- Program ID: {$enrollment->program_id}");
        $this->line("- Enrollment Type: {$enrollment->enrollment_type}");
        $this->line("- Status: {$enrollment->enrollment_status}");
        $this->line("");
        
        // Get registration data
        $registration = Registration::where('user_id', $enrollment->user_id)
            ->where('program_id', $enrollment->program_id)
            ->where('enrollment_type', 'Modular')
            ->first();
        
        if (!$registration) {
            $this->warn("No registration found for this enrollment!");
        } else {
            $this->info("Registration Details:");
            $this->line("- Registration ID: {$registration->registration_id}");
            $this->line("- Selected Modules: {$registration->selected_modules}");
            
            if ($registration->selected_modules) {
                $selectedModules = json_decode($registration->selected_modules, true);
                $this->line("- Parsed Modules:");
                foreach ($selectedModules as $index => $module) {
                    $this->line("  [$index]: " . json_encode($module));
                }
            }
        }
        
        $this->line("");
        
        // Get enrollment courses
        $enrollmentCourses = EnrollmentCourse::where('enrollment_id', $enrollmentId)->get();
        
        $this->info("Enrollment Courses:");
        if ($enrollmentCourses->isEmpty()) {
            $this->line("- No enrollment courses found");
        } else {
            foreach ($enrollmentCourses as $ec) {
                $this->line("- Course ID: {$ec->course_id}, Module ID: {$ec->module_id}, Active: " . ($ec->is_active ? 'Yes' : 'No'));
            }
        }
        
        $this->info("=== END DEBUG ===");
    }
}
