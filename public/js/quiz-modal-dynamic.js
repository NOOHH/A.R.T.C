// Add this to your main JS file for the quiz modal
function fetchModulesForProgram(programId) {
    if (!programId) {
        $('#module_id').html('<option value="">Select Module</option>');
        $('#course_id').html('<option value="">Select Course</option>');
        return;
    }
    $.ajax({
        url: '/professor/quiz-generator/get-modules-by-program',
        type: 'POST',
        data: {
            program_id: programId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            var options = '<option value="">Select Module</option>';
            if (res.modules && res.modules.length) {
                res.modules.forEach(function(module) {
                    options += '<option value="' + module.id + '">' + module.name + '</option>';
                });
            }
            $('#module_id').html(options);
            $('#course_id').html('<option value="">Select Course</option>');
        }
    });
}

function fetchCoursesForModule(moduleId) {
    if (!moduleId) {
        $('#course_id').html('<option value="">Select Course</option>');
        return;
    }
    $.ajax({
        url: '/professor/quiz-generator/get-courses-by-module',
        type: 'POST',
        data: {
            module_id: moduleId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            var options = '<option value="">Select Course</option>';
            if (res.courses && res.courses.length) {
                res.courses.forEach(function(course) {
                    // Use subject_id and subject_name from backend response
                    options += '<option value="' + course.subject_id + '">' + course.subject_name + '</option>';
                });
            }
            $('#course_id').html(options);
        }
    });
}

// Defensive null check for quizEmptyState
function renderManualQuestions() {
    var emptyState = document.getElementById('quizEmptyState');
    if (emptyState) {
        // ... your logic ...
        emptyState.style.display = 'block';
    }
}
