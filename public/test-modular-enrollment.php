<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modular Enrollment Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 5px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; font-size: 12px; }
        .success { color: green; font-size: 12px; }
        .debug { background: #f8f9fa; padding: 10px; margin: 10px 0; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <h1>Modular Enrollment Test</h1>
    
    <div class="debug">
        <h3>Debug Information</h3>
        <div id="debugInfo">Loading...</div>
    </div>

    <form id="testForm" action="/enrollment/modular/submit" method="POST">
        @csrf
        
        <h2>Account Information</h2>
        <div class="form-group">
            <label for="user_firstname">First Name:</label>
            <input type="text" id="user_firstname" name="user_firstname" value="John" required>
        </div>
        
        <div class="form-group">
            <label for="user_lastname">Last Name:</label>
            <input type="text" id="user_lastname" name="user_lastname" value="Doe" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="test@example.com" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="password123" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" value="password123" required>
        </div>
        
        <h2>Enrollment Information</h2>
        <div class="form-group">
            <label for="package_id">Package ID:</label>
            <select id="package_id" name="package_id" required>
                <option value="">Select Package</option>
                @foreach(\App\Models\Package::where('package_type', 'modular')->get() as $package)
                <option value="{{ $package->package_id }}" {{ $package->package_id == 18 ? 'selected' : '' }}>
                    {{ $package->package_name }} (ID: {{ $package->package_id }})
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="program_id">Program ID:</label>
            <select id="program_id" name="program_id" required>
                <option value="">Select Program</option>
                @foreach(\App\Models\Program::all() as $program)
                <option value="{{ $program->program_id }}" {{ $program->program_id == 33 ? 'selected' : '' }}>
                    {{ $program->program_name }} (ID: {{ $program->program_id }})
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="learning_mode">Learning Mode:</label>
            <select id="learning_mode" name="learning_mode" required>
                <option value="synchronous" selected>Synchronous</option>
                <option value="asynchronous">Asynchronous</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="education_level">Education Level:</label>
            <select id="education_level" name="education_level" required>
                <option value="Undergraduate">Undergraduate</option>
                <option value="Graduate" selected>Graduate</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="enrollment_type">Enrollment Type:</label>
            <select id="enrollment_type" name="enrollment_type" required>
                <option value="Modular" selected>Modular</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="selected_modules">Selected Modules (JSON):</label>
            <textarea id="selected_modules" name="selected_modules" rows="3" required>[{"id":46,"name":"Module 1 - Creation of Food","selected_courses":["11","14"]}]</textarea>
        </div>
        
        <div class="form-group">
            <label for="Start_Date">Start Date:</label>
            <input type="date" id="Start_Date" name="Start_Date" value="{{ date('Y-m-d') }}" required>
        </div>
        
        <div class="form-group">
            <label for="referral_code">Referral Code (Optional):</label>
            <input type="text" id="referral_code" name="referral_code" value="">
        </div>
        
        <div class="form-group">
            <label for="plan_id">Plan ID:</label>
            <input type="number" id="plan_id" name="plan_id" value="2">
        </div>
        
        <button type="submit">Test Submit</button>
        <button type="button" onclick="validateForm()">Validate Only</button>
    </form>
    
    <div id="results" style="margin-top: 20px;"></div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Load debug information
        window.addEventListener('DOMContentLoaded', function() {
            loadDebugInfo();
            setupFormValidation();
        });
        
        function loadDebugInfo() {
            const debugDiv = document.getElementById('debugInfo');
            
            // Check if required data exists
            fetch('/get-program-modules?program_id=33')
                .then(response => response.json())
                .then(data => {
                    let debugHTML = '<strong>Program Modules for ID 33:</strong> ';
                    if (data.success && data.modules) {
                        debugHTML += `✅ ${data.modules.length} modules found<br>`;
                        data.modules.forEach(module => {
                            debugHTML += `- Module ${module.modules_id}: ${module.module_name}<br>`;
                        });
                    } else {
                        debugHTML += '❌ No modules found<br>';
                    }
                    debugDiv.innerHTML = debugHTML;
                })
                .catch(error => {
                    debugDiv.innerHTML = '❌ Error loading debug info: ' + error.message;
                });
        }
        
        function setupFormValidation() {
            const form = document.getElementById('testForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                console.log('=== FORM VALIDATION TEST ===');
                
                const formData = new FormData(form);
                
                // Log all form data
                console.log('Form data:');
                for (let [key, value] of formData.entries()) {
                    if (key === 'password' || key === 'password_confirmation') {
                        console.log(`${key}: [HIDDEN - ${value.length} chars]`);
                    } else {
                        console.log(`${key}: ${value}`);
                    }
                }
                
                // Submit to server
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    displayResults(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    displayResults({success: false, message: 'Network error: ' + error.message});
                });
            });
        }
        
        function validateForm() {
            console.log('=== CLIENT-SIDE VALIDATION ===');
            
            const requiredFields = [
                'user_firstname', 'user_lastname', 'email', 'password', 'password_confirmation',
                'package_id', 'program_id', 'learning_mode', 'education_level', 
                'enrollment_type', 'selected_modules', 'Start_Date'
            ];
            
            const issues = [];
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (!element) {
                    issues.push(`Missing form element: ${field}`);
                } else if (!element.value || element.value.trim() === '') {
                    issues.push(`Empty required field: ${field}`);
                }
            });
            
            // Password confirmation check
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            if (password !== passwordConfirmation) {
                issues.push('Passwords do not match');
            }
            
            // JSON validation for selected_modules
            try {
                const selectedModules = document.getElementById('selected_modules').value;
                JSON.parse(selectedModules);
            } catch (e) {
                issues.push('Invalid JSON in selected_modules: ' + e.message);
            }
            
            console.log('Validation issues:', issues);
            
            if (issues.length === 0) {
                displayResults({success: true, message: 'Client-side validation passed!'});
            } else {
                displayResults({success: false, errors: {validation: issues}});
            }
        }
        
        function displayResults(data) {
            const resultsDiv = document.getElementById('results');
            
            let html = '<div class="debug">';
            html += '<h3>Results</h3>';
            
            if (data.success) {
                html += '<div class="success">✅ ' + (data.message || 'Success') + '</div>';
            } else {
                html += '<div class="error">❌ Failed</div>';
                
                if (data.message) {
                    html += '<div class="error">Message: ' + data.message + '</div>';
                }
                
                if (data.errors) {
                    html += '<div class="error">Errors:</div>';
                    html += '<ul>';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        if (Array.isArray(messages)) {
                            messages.forEach(message => {
                                html += `<li class="error">${field}: ${message}</li>`;
                            });
                        } else {
                            html += `<li class="error">${field}: ${messages}</li>`;
                        }
                    }
                    html += '</ul>';
                }
            }
            
            html += '</div>';
            resultsDiv.innerHTML = html;
        }
    </script>
</body>
</html>
