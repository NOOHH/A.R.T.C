<!DOCTYPE html>
<html>
<head>
    <title>Admin Quiz Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Admin Quiz Creation Test</h1>
    
    <form id="quizForm">
        <div>
            <label>Title: <input type="text" name="title" value="Test Admin Quiz" required></label>
        </div>
        <div>
            <label>Description: <textarea name="description">Test Description</textarea></label>
        </div>
        <div>
            <label>Program ID: <input type="number" name="program_id" value="41" required></label>
        </div>
        <div>
            <label>Module ID: <input type="number" name="module_id" value="79"></label>
        </div>
        <div>
            <label>Course ID: <input type="number" name="course_id" value="52"></label>
        </div>
        <div>
            <input type="hidden" name="admin_id" value="1">
            <input type="hidden" name="time_limit" value="60">
            <input type="hidden" name="max_attempts" value="1">
            <input type="hidden" name="infinite_retakes" value="false">
            <input type="hidden" name="has_deadline" value="false">
            <input type="hidden" name="is_draft" value="true">
            <input type="hidden" name="status" value="draft">
        </div>
        <button type="submit">Create Quiz</button>
    </form>
    
    <div id="result"></div>

    <script>
        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#quizForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                title: $('input[name="title"]').val(),
                description: $('textarea[name="description"]').val(),
                program_id: $('input[name="program_id"]').val(),
                module_id: $('input[name="module_id"]').val(),
                course_id: $('input[name="course_id"]').val(),
                admin_id: $('input[name="admin_id"]').val(),
                time_limit: $('input[name="time_limit"]').val(),
                max_attempts: $('input[name="max_attempts"]').val(),
                infinite_retakes: $('input[name="infinite_retakes"]').val() === 'true',
                has_deadline: $('input[name="has_deadline"]').val() === 'true',
                is_draft: $('input[name="is_draft"]').val() === 'true',
                status: $('input[name="status"]').val(),
                questions: [
                    {
                        question_text: 'Test Question',
                        question_type: 'multiple_choice',
                        points: 1,
                        options: ['Option A', 'Option B', 'Option C', 'Option D'],
                        correct_answers: [0],
                        order: 1
                    }
                ]
            };
            
            console.log('Sending data:', formData);
            
            $.ajax({
                url: '/admin/quiz-generator/save-quiz',
                method: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    $('#result').html('<div style="color: green;">SUCCESS: ' + JSON.stringify(response) + '</div>');
                    console.log('Success:', response);
                },
                error: function(xhr, status, error) {
                    $('#result').html('<div style="color: red;">ERROR: ' + xhr.status + ' - ' + xhr.responseText + '</div>');
                    console.log('Error:', xhr.responseText);
                    console.log('Status:', xhr.status);
                    console.log('Error:', error);
                }
            });
        });
    </script>
</body>
</html>
