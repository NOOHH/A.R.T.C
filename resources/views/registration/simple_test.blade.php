<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Registration Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Simple Registration Test (Based on profile.php)</h3>
                    </div>
                    <div class="card-body">
                        
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('student.register.simple') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_firstname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="user_firstname" value="{{ old('user_firstname', 'John') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="user_lastname" value="{{ old('user_lastname', 'Doe') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', 'test' . time() . '@example.com') }}" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" value="password123" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation" value="password123" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="program_id" class="form-label">Program</label>
                                        <select name="program_id" class="form-control" required>
                                            <option value="">Select Program</option>
                                            <option value="1" {{ old('program_id') == '1' ? 'selected' : '' }}>Sample Program 1</option>
                                            <option value="2" {{ old('program_id') == '2' ? 'selected' : '' }}>Sample Program 2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="package_id" class="form-label">Package</label>
                                        <select name="package_id" class="form-control" required>
                                            <option value="">Select Package</option>
                                            <option value="1" {{ old('package_id') == '1' ? 'selected' : '' }}>Sample Package 1</option>
                                            <option value="2" {{ old('package_id') == '2' ? 'selected' : '' }}>Sample Package 2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="enrollment_type" class="form-label">Enrollment Type</label>
                                        <select name="enrollment_type" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <option value="Full" {{ old('enrollment_type') == 'Full' ? 'selected' : '' }}>Full</option>
                                            <option value="Modular" {{ old('enrollment_type') == 'Modular' ? 'selected' : '' }}>Modular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="learning_mode" class="form-label">Learning Mode</label>
                                        <select name="learning_mode" class="form-control" required>
                                            <option value="">Select Mode</option>
                                            <option value="synchronous" {{ old('learning_mode') == 'synchronous' ? 'selected' : '' }}>Synchronous</option>
                                            <option value="asynchronous" {{ old('learning_mode') == 'asynchronous' ? 'selected' : '' }}>Asynchronous</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="education_level" class="form-label">Education Level</label>
                                        <select name="education_level" class="form-control" required>
                                            <option value="">Select Level</option>
                                            <option value="Undergraduate" {{ old('education_level') == 'Undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                                            <option value="Graduate" {{ old('education_level') == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="Start_Date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="Start_Date" value="{{ old('Start_Date', date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <h5>File Uploads (Using profile.php approach)</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="valid_id" class="form-label">Valid ID</label>
                                        <input type="file" class="form-control" name="valid_id" accept="image/*,application/pdf">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="birth_certificate" class="form-label">Birth Certificate</label>
                                        <input type="file" class="form-control" name="birth_certificate" accept="image/*,application/pdf">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="diploma" class="form-label">Diploma</label>
                                        <input type="file" class="form-control" name="diploma" accept="image/*,application/pdf">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tor" class="form-label">Transcript of Records</label>
                                        <input type="file" class="form-control" name="tor" accept="image/*,application/pdf">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Register</button>
                                <a href="/enrollment/full" class="btn btn-secondary">Back to Complex Form</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
