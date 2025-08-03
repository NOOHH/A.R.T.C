<!DOCTYPE html>
<html>
<head>
    <title>Test Enrollment Assignment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Enrollment Assignment</h2>
        
        <div id="result" class="alert" style="display: none;"></div>
        
        <form id="testEnrollmentForm">
            <div class="mb-3">
                <label>Student ID</label>
                <input type="text" name="student_id" class="form-control" value="test_student_001" required>
            </div>
            
            <div class="mb-3">
                <label>Program ID</label>
                <input type="text" name="program_id" class="form-control" value="1" required>
            </div>
            
            <div class="mb-3">
                <label>Batch ID</label>
                <input type="text" name="batch_id" class="form-control" value="1" required>
            </div>
            
            <div class="mb-3">
                <label>Enrollment Type</label>
                <select name="enrollment_type" class="form-control" required>
                    <option value="full">Full</option>
                    <option value="modular">Modular</option>
                    <option value="accelerated">Accelerated</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label>Learning Mode</label>
                <select name="learning_mode" class="form-control" required>
                    <option value="online">Online</option>
                    <option value="onsite">On-site</option>
                    <option value="hybrid">Hybrid</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Test Enrollment Assignment</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('testEnrollmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');
            
            fetch('/admin/enrollment/assign', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
                resultDiv.className = 'alert alert-info';
                resultDiv.style.display = 'block';
            })
            .catch(error => {
                resultDiv.innerHTML = 'Error: ' + error.message;
                resultDiv.className = 'alert alert-danger';
                resultDiv.style.display = 'block';
            });
        });
    </script>
</body>
</html>
