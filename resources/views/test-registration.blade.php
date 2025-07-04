<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Test Registration Form</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display any success/error messages -->
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Hidden fields for testing -->
                            <input type="hidden" name="enrollment_type" value="full">
                            <input type="hidden" name="program_id" value="1">
                            <input type="hidden" name="package_id" value="1">
                            <input type="hidden" name="plan_id" value="1">
                            
                            <!-- Required User Fields -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="user_firstname" class="form-label">User First Name</label>
                                    <input type="text" class="form-control" id="user_firstname" name="user_firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="user_lastname" class="form-label">User Last Name</label>
                                    <input type="text" class="form-control" id="user_lastname" name="user_lastname" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                            
                            <!-- Required Student Fields -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name">
                                </div>
                                <div class="col-md-4">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="student_school" class="form-label">School</label>
                                <input type="text" class="form-control" id="student_school" name="student_school" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="street_address" class="form-label">Street Address</label>
                                <input type="text" class="form-control" id="street_address" name="street_address" required>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="state_province" class="form-label">State/Province</label>
                                    <input type="text" class="form-control" id="state_province" name="state_province" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="zipcode" class="form-label">Zipcode</label>
                                    <input type="text" class="form-control" id="zipcode" name="zipcode" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_contact_number" class="form-label">Emergency Contact</label>
                                    <input type="tel" class="form-control" id="emergency_contact_number" name="emergency_contact_number" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="Start_Date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="Start_Date" name="Start_Date" required>
                            </div>
                            
                            <!-- Education Level -->
                            <div class="mb-3">
                                <label for="education" class="form-label">Education Level</label>
                                <select class="form-select" id="education" name="education" required>
                                    <option value="">Select Education Level</option>
                                    <option value="Undergraduate">Undergraduate</option>
                                    <option value="Graduate">Graduate</option>
                                </select>
                            </div>
                            
                            <!-- Dynamic Fields (if any) -->
                            @if(isset($formRequirements) && $formRequirements->count() > 0)
                                <h5>Additional Information</h5>
                                @foreach($formRequirements as $requirement)
                                    @if($requirement->field_type === 'section')
                                        <h6 class="mt-4 mb-3">{{ $requirement->field_label }}</h6>
                                    @else
                                        <div class="mb-3">
                                            <label for="{{ $requirement->field_name }}" class="form-label">
                                                {{ $requirement->field_label }}
                                                @if($requirement->is_required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            
                                            @if($requirement->field_type === 'text')
                                                <input type="text" class="form-control" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" {{ $requirement->is_required ? 'required' : '' }}>
                                            @elseif($requirement->field_type === 'email')
                                                <input type="email" class="form-control" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" {{ $requirement->is_required ? 'required' : '' }}>
                                            @elseif($requirement->field_type === 'tel')
                                                <input type="tel" class="form-control" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" {{ $requirement->is_required ? 'required' : '' }}>
                                            @elseif($requirement->field_type === 'number')
                                                <input type="number" class="form-control" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" {{ $requirement->is_required ? 'required' : '' }}>
                                            @elseif($requirement->field_type === 'date')
                                                <input type="date" class="form-control" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" {{ $requirement->is_required ? 'required' : '' }}>
                                            @elseif($requirement->field_type === 'textarea')
                                                <textarea class="form-control" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" rows="3" {{ $requirement->is_required ? 'required' : '' }}></textarea>
                                            @elseif($requirement->field_type === 'select')
                                                <select class="form-select" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" {{ $requirement->is_required ? 'required' : '' }}>
                                                    <option value="">Choose...</option>
                                                    @if($requirement->field_options)
                                                        @foreach($requirement->field_options as $option)
                                                            <option value="{{ $option }}">{{ $option }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            @elseif($requirement->field_type === 'file')
                                                <input type="file" class="form-control" id="{{ $requirement->field_name }}" name="{{ $requirement->field_name }}" {{ $requirement->is_required ? 'required' : '' }}>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Register</button>
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
