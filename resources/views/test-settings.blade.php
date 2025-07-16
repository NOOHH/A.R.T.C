<!DOCTYPE html>
<html>
<head>
    <title>Test Student Settings Update</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .success { color: green; margin: 10px 0; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test Student Settings Update</h1>
    
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
    
    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('student.settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="user_firstname">First Name *</label>
            <input type="text" id="user_firstname" name="user_firstname" 
                   value="{{ old('user_firstname', $student->firstname ?? 'Test First Name') }}" required>
        </div>
        
        <div class="form-group">
            <label for="user_lastname">Last Name *</label>
            <input type="text" id="user_lastname" name="user_lastname" 
                   value="{{ old('user_lastname', $student->lastname ?? 'Test Last Name') }}" required>
        </div>
        
        <div class="form-group">
            <label for="middlename">Middle Name</label>
            <input type="text" id="middlename" name="middlename" 
                   value="{{ old('middlename', $student->middlename ?? 'Test Middle') }}">
        </div>
        
        <div class="form-group">
            <label for="street_address">Street Address</label>
            <input type="text" id="street_address" name="street_address" 
                   value="{{ old('street_address', $student->street_address ?? 'Test Address 123') }}">
        </div>
        
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" 
                   value="{{ old('city', $student->city ?? 'Test City') }}">
        </div>
        
        <div class="form-group">
            <label for="state_province">State/Province</label>
            <input type="text" id="state_province" name="state_province" 
                   value="{{ old('state_province', $student->state_province ?? 'Test State') }}">
        </div>
        
        <div class="form-group">
            <label for="zipcode">Zip Code</label>
            <input type="text" id="zipcode" name="zipcode" 
                   value="{{ old('zipcode', $student->zipcode ?? '12345') }}">
        </div>
        
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" 
                   value="{{ old('contact_number', $student->contact_number ?? '123-456-7890') }}">
        </div>
        
        <h3>Dynamic Fields (Form Requirements)</h3>
        @if(isset($formRequirements) && $formRequirements->count() > 0)
            @foreach($formRequirements as $requirement)
                @if($requirement->field_type !== 'section')
                    <div class="form-group">
                        <label for="{{ $requirement->field_name }}">
                            {{ $requirement->field_label }}
                            @if($requirement->is_required) * @endif
                        </label>
                        
                        @if($requirement->field_type == 'text')
                            <input type="text" 
                                   id="{{ $requirement->field_name }}" 
                                   name="{{ $requirement->field_name }}" 
                                   value="{{ old($requirement->field_name, $student->{$requirement->field_name} ?? 'Test Dynamic Value') }}"
                                   @if($requirement->is_required) required @endif>
                        
                        @elseif($requirement->field_type == 'email')
                            <input type="email" 
                                   id="{{ $requirement->field_name }}" 
                                   name="{{ $requirement->field_name }}" 
                                   value="{{ old($requirement->field_name, $student->{$requirement->field_name} ?? 'test@dynamic.com') }}"
                                   @if($requirement->is_required) required @endif>
                        
                        @elseif($requirement->field_type == 'select')
                            <select id="{{ $requirement->field_name }}" 
                                    name="{{ $requirement->field_name }}"
                                    @if($requirement->is_required) required @endif>
                                <option value="">Select {{ $requirement->field_label }}</option>
                                <option value="Option 1" @if(old($requirement->field_name, $student->{$requirement->field_name} ?? '') == 'Option 1') selected @endif>Option 1</option>
                                <option value="Option 2" @if(old($requirement->field_name, $student->{$requirement->field_name} ?? '') == 'Option 2') selected @endif>Option 2</option>
                            </select>
                        
                        @else
                            <input type="{{ $requirement->field_type }}" 
                                   id="{{ $requirement->field_name }}" 
                                   name="{{ $requirement->field_name }}" 
                                   value="{{ old($requirement->field_name, $student->{$requirement->field_name} ?? 'test value') }}"
                                   @if($requirement->is_required) required @endif>
                        @endif
                    </div>
                @endif
            @endforeach
        @else
            <p>No dynamic form requirements found.</p>
        @endif
        
        <button type="submit">Test Update Settings</button>
    </form>
    
    <h3>Current Student Data</h3>
    <pre>{{ json_encode($student ?? 'No student data', JSON_PRETTY_PRINT) }}</pre>
    
    <h3>Form Requirements</h3>
    <pre>{{ json_encode($formRequirements->toArray() ?? 'No form requirements', JSON_PRETTY_PRINT) }}</pre>
</body>
</html>
