@extends('professor.layout')

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('professor.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name', $professor->first_name) }}" 
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name', $professor->last_name) }}" 
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $professor->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamic Additional Fields -->
                        @if(isset($dynamicFields) && $dynamicFields->count() > 0)
                        <hr>
                        <h6 class="mb-3">Additional Information</h6>
                        
                        @foreach($dynamicFields as $field)
                            <div class="mb-3">
                                <label for="dynamic_{{ $field->field_name }}" class="form-label">
                                    {{ $field->display_name }} 
                                    @if($field->is_required) * @endif
                                </label>
                                
                                @if($field->field_type === 'text')
                                    <input type="text" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->field_type === 'textarea')
                                    <textarea class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                              id="dynamic_{{ $field->field_name }}" 
                                              name="dynamic[{{ $field->field_name }}]" 
                                              rows="3"
                                              {{ $field->is_required ? 'required' : '' }}>{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}</textarea>
                                @elseif($field->field_type === 'select')
                                    <select class="form-select @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                            id="dynamic_{{ $field->field_name }}" 
                                            name="dynamic[{{ $field->field_name }}]"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">Choose...</option>
                                        @if($field->field_options)
                                            @foreach(json_decode($field->field_options, true) as $option)
                                                <option value="{{ $option }}" 
                                                        {{ (old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') == $option) ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                @elseif($field->field_type === 'email')
                                    <input type="email" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->field_type === 'phone')
                                    <input type="tel" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->field_type === 'date')
                                    <input type="date" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @endif
                                
                                @if($field->help_text)
                                    <div class="form-text">{{ $field->help_text }}</div>
                                @endif
                                
                                @error('dynamic.'.$field->field_name)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                        @endif
                        
                        <hr>
                        
                        <h6 class="mb-3">Change Password</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    <div class="form-text">Leave empty to keep current password</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="mt-3 mb-1">{{ $professor->full_name }}</h5>
                        <small class="text-muted">Professor</small>
                    </div>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Email:</strong><br>
                            <small class="text-muted">{{ $professor->email }}</small>
                        </li>
                        <li class="mb-2">
                            <strong>Professor ID:</strong><br>
                            <small class="text-muted">#{{ $professor->professor_id }}</small>
                        </li>
                        <li class="mb-2">
                            <strong>Assigned Programs:</strong><br>
                            <small class="text-muted">{{ $professor->programs->count() }} program(s)</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
