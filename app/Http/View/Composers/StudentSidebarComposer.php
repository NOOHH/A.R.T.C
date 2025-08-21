<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Http\Traits\StudentProgramsTrait;
use Illuminate\Support\Facades\Log;

class StudentSidebarComposer
{
    use StudentProgramsTrait;

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Check if this is preview mode
        $isPreview = request()->has('preview') || request()->query('preview') === 'true';
        
        if ($isPreview) {
            // Provide mock data for preview mode
            $studentPrograms = [
                [
                    'program_id' => 1,
                    'program_name' => 'Nursing Board Review',
                    'package_name' => 'Premium Package',
                    'enrollment_status' => 'approved',
                    'payment_status' => 'paid',
                    'can_access' => true,
                    'batch_access_granted' => true
                ],
                [
                    'program_id' => 2,
                    'program_name' => 'Medical Technology Review',
                    'package_name' => 'Standard Package',
                    'enrollment_status' => 'approved',
                    'payment_status' => 'paid',
                    'can_access' => true,
                    'batch_access_granted' => true
                ]
            ];
            $view->with('studentPrograms', collect($studentPrograms));
        } else {
            // Only provide student programs if user is authenticated as student
            if (session('user_role') === 'student' && session('user_id')) {
                try {
                    $studentPrograms = $this->getStudentPrograms();
                    $view->with('studentPrograms', $studentPrograms);
                } catch (\Exception $e) {
                    // If there's an error (e.g., table doesn't exist), provide empty array
                    Log::warning('Error getting student programs: ' . $e->getMessage());
                    $view->with('studentPrograms', collect([]));
                }
            }
        }
    }
}
