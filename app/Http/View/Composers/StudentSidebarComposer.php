<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Http\Traits\StudentProgramsTrait;

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
        // Only provide student programs if user is authenticated as student
        if (session('user_role') === 'student' && session('user_id')) {
            $studentPrograms = $this->getStudentPrograms();
            $view->with('studentPrograms', $studentPrograms);
        }
    }
}
