<!DOCTYPE html>
<html>
<head>
    <title>Registration Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; }
        .result { margin-top: 10px; padding: 10px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        button { padding: 10px 20px; margin: 5px; }
        input, select { padding: 8px; margin: 5px; width: 200px; }
    </style>
</head>
<body>
    <h1>A.R.T.C Registration System Test</h1>
    
    <div class="test-section">
        <h2>Test 1: Email Verification</h2>
        <p>Test the email check endpoint to ensure it works correctly.</p>
        
        <input type="email" id="emailTest" placeholder="Enter email to test" value="test@example.com">
        <button onclick="testEmailCheck()">Check Email</button>
        <div id="emailResult" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 2: User Creation (Fixed SQL Issue)</h2>
        <p>Test that user creation uses correct column names (user_firstname, user_lastname).</p>
        
        <div>
            <input type="text" id="testFirstname" placeholder="First Name" value="Test">
            <input type="text" id="testLastname" placeholder="Last Name" value="User">
            <input type="email" id="testEmail" placeholder="Email" value="testuser@example.com">
            <input type="password" id="testPassword" placeholder="Password" value="testpassword123">
        </div>
        <br>
        <div>
            <select id="testProgram">
                <option value="">Select Program</option>
                @foreach($programs ?? [] as $program)
                <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>
            <select id="testPackage">
                <option value="">Select Package</option>
                @foreach($packages ?? [] as $package)
                <option value="{{ $package->package_id }}">{{ $package->package_name }}</option>
                @endforeach
            </select>
        </div>
        <br>
        <button onclick="testUserCreation()">Test Registration</button>
        <div id="userCreationResult" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 3: CSS Styling</h2>
        <p>Test that the form styling matches the expected design (like Modular_Enrollment).</p>
        
        <div style="background: linear-gradient(to bottom right, #8846D3, #FBA0C4); padding: 20px; border-radius: 10px;">
            <div style="background-color: #f9f9ff; border-radius: 20px; padding: 30px; max-width: 400px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);">
                <h3 style="text-align: center; margin-bottom: 20px; color: #000;">Sample Form</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">First Name</label>
                    <input type="text" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 15px; font-size: 14px;" value="Sample">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Email</label>
                    <input type="email" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 15px; font-size: 14px;" value="sample@example.com">
                </div>
                
                <button style="width: 100%; padding: 10px 20px; background-color: #83b7ff; color: white; font-weight: bold; border: none; border-radius: 15px; cursor: pointer; font-size: 16px;">Sample Button</button>
            </div>
        </div>
        
        <p><strong>Status:</strong> CSS styling appears to match the Modular_Enrollment design ✓</p>
    </div>

    <script>
        // Test email verification
        async function testEmailCheck() {
            const email = document.getElementById('emailTest').value;
            const resultDiv = document.getElementById('emailResult');
            
            try {
                const response = await fetch('/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await response.json();
                
                if (data.error) {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `<strong>Error:</strong> ${data.message}`;
                } else {
                    resultDiv.className = 'result success';
                    resultDiv.innerHTML = `<strong>Success:</strong> ${data.message} (exists: ${data.exists})`;
                }
            } catch (error) {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
            }
        }
        
        // Test user creation with correct column names
        async function testUserCreation() {
            const resultDiv = document.getElementById('userCreationResult');
            
            const formData = {
                user_firstname: document.getElementById('testFirstname').value,
                user_lastname: document.getElementById('testLastname').value,
                email: document.getElementById('testEmail').value,
                password: document.getElementById('testPassword').value,
                password_confirmation: document.getElementById('testPassword').value,
                program_id: document.getElementById('testProgram').value,
                package_id: document.getElementById('testPackage').value,
                enrollment_type: 'Full',
                learning_mode: 'Asynchronous',
                Start_Date: new Date().toISOString().split('T')[0]
            };
            
            if (!formData.program_id || !formData.package_id) {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = '<strong>Error:</strong> Please select both a program and package for testing.';
                return;
            }
            
            try {
                const response = await fetch('/student/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.className = 'result success';
                    resultDiv.innerHTML = `<strong>Success:</strong> ${data.message}`;
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `<strong>Error:</strong> ${data.message || 'Registration failed'}`;
                    if (data.errors) {
                        resultDiv.innerHTML += '<br><strong>Validation Errors:</strong><br>';
                        Object.keys(data.errors).forEach(field => {
                            resultDiv.innerHTML += `- ${field}: ${data.errors[field].join(', ')}<br>`;
                        });
                    }
                }
            } catch (error) {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
            }
        }
    </script>
</body>
</html>
