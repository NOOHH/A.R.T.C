<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\FormRequirement;

class DynamicEnrollmentForm extends Component
{
    public $programType;
    public $requirements;

    public function __construct($programType = 'both')
    {
        $this->programType = $programType;
        $this->requirements = FormRequirement::active()
            ->forProgram($programType)
            ->ordered()
            ->get();
    }

    public function render()
    {
        return view('components.dynamic-enrollment-form');
    }
}
