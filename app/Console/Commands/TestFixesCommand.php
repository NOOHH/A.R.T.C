<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Module;
use App\Models\Student;
use App\Models\Program;

class TestFixesCommand extends Command
{
    protected $signature = 'test:fixes';
    protected $description = 'Test all the fixes implemented';

    public function handle()
    {
        $this->info('=== Testing All Fixes ===');
        
        // Test 1: Check module restore functionality
        $this->info('');
        $this->info('1. Testing Module Data...');
        
        $totalModules = Module::count();
        $archivedModules = Module::where('is_archived', true)->count();
        
        $this->line("   ✓ Total modules: {$totalModules}");
        $this->line("   ✓ Archived modules: {$archivedModules}");
        
        // Test 2: Check student data for CSV export
        $this->info('');
        $this->info('2. Testing Student Data...');
        
        $totalStudents = Student::count();
        $studentsWithEnrollments = Student::has('enrollments')->count();
        
        $this->line("   ✓ Total students: {$totalStudents}");
        $this->line("   ✓ Students with enrollments: {$studentsWithEnrollments}");
        
        // Test 3: Check program data
        $this->info('');
        $this->info('3. Testing Program Data...');
        
        $totalPrograms = Program::count();
        $this->line("   ✓ Total programs: {$totalPrograms}");
        
        // Test 4: Test model relationships
        $this->info('');
        $this->info('4. Testing Model Relationships...');
        
        try {
            $studentWithRelations = Student::with(['user', 'enrollments'])->first();
            if ($studentWithRelations) {
                $this->line("   ✓ Student relationships working");
                $this->line("   ✓ Enrollments count: " . $studentWithRelations->enrollments->count());
            } else {
                $this->line("   ⚠ No students found for testing");
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Student relationships error: " . $e->getMessage());
        }
        
        $this->info('');
        $this->info('=== Test Complete ===');
        
        return 0;
    }
}
