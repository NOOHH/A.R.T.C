<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\FormRequirement;

class DynamicEnrollmentForm extends Component
{
    public $programType;
    public $requirements;

    public function __construct($programType = 'both', $requirements = null)
    {
        $this->programType = $programType;
        
        if ($requirements) {
            $this->requirements = $requirements;
        } else {
            // Fallback to fetch requirements if not provided
            $this->requirements = FormRequirement::active()
                ->forProgram($programType)
                ->ordered()
                ->get();
        }
    }

    public function render()
    {
        return view('components.dynamic-enrollment-form');
    }
}
