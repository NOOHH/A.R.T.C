<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\Program;
use App\Models\Module;

class AssignmentSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some existing students, programs, and modules
        $students = Student::take(3)->get();
        $programs = Program::take(2)->get();
        $modules = Module::take(3)->get();

        if ($students->count() > 0 && $programs->count() > 0 && $modules->count() > 0) {
            // Create some sample assignment submissions
            $sampleSubmissions = [
                [
                    'student_id' => $students[0]->student_id,
                    'module_id' => $modules[0]->modules_id,
                    'program_id' => $programs[0]->program_id,
                    'files' => json_encode([
                        [
                            'original_name' => 'assignment1.pdf',
                            'path' => 'assignments/sample1.pdf',
                            'size' => 1024000,
                            'type' => 'application/pdf'
                        ]
                    ]),
                    'comments' => 'Here is my completed assignment for this module.',
                    'submitted_at' => now()->subDays(3),
                    'status' => 'pending'
                ],
                [
                    'student_id' => $students[0]->student_id,
                    'module_id' => $modules[1]->modules_id,
                    'program_id' => $programs[0]->program_id,
                    'files' => json_encode([
                        [
                            'original_name' => 'project_report.docx',
                            'path' => 'assignments/sample2.docx',
                            'size' => 2048000,
                            'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ],
                        [
                            'original_name' => 'code_files.zip',
                            'path' => 'assignments/sample2_code.zip',
                            'size' => 512000,
                            'type' => 'application/zip'
                        ]
                    ]),
                    'comments' => 'Project report with source code files attached.',
                    'submitted_at' => now()->subDays(1),
                    'status' => 'graded',
                    'grade' => 85.5,
                    'feedback' => 'Excellent work! Your code structure is clean and well-documented. Consider adding more error handling in the validation functions.',
                    'graded_at' => now()->subHours(2),
                    'graded_by' => 1
                ]
            ];

            if ($students->count() > 1) {
                $sampleSubmissions[] = [
                    'student_id' => $students[1]->student_id,
                    'module_id' => $modules[0]->modules_id,
                    'program_id' => $programs[0]->program_id,
                    'files' => json_encode([
                        [
                            'original_name' => 'essay.pdf',
                            'path' => 'assignments/sample3.pdf',
                            'size' => 756000,
                            'type' => 'application/pdf'
                        ]
                    ]),
                    'comments' => 'My essay on the assigned topic.',
                    'submitted_at' => now()->subDays(2),
                    'status' => 'reviewed',
                    'feedback' => 'Good effort, but your analysis needs more depth. Please revise the second section and resubmit.',
                    'graded_at' => now()->subHours(6),
                    'graded_by' => 1
                ];
            }

            foreach ($sampleSubmissions as $submission) {
                AssignmentSubmission::create($submission);
            }

            $this->command->info('Sample assignment submissions created successfully!');
        } else {
            $this->command->warn('No students, programs, or modules found. Please seed those tables first.');
        }
    }
}
