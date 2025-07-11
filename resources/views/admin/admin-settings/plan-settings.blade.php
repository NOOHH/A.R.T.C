@extends('admin.layouts.admin')

@section('title', 'Plan Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog mr-2"></i>
                        Learning Mode Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <strong>Learning Mode Settings:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>Synchronous Mode:</strong> Start date is automatically set to 2 weeks from registration date</li>
                                    <li><strong>Asynchronous Mode:</strong> User must manually input their preferred start date</li>
                                </ul>
                            </div>
                            
                            <form id="planSettingsForm">
                                @csrf
                                @foreach($plans as $plan)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-graduation-cap mr-2"></i>
                                            {{ $plan->plan_name }}
                                            @if($plan->description)
                                                <small class="text-muted ml-2">{{ $plan->description }}</small>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="sync_{{ $plan->plan_id }}" 
                                                               name="plans[{{ $plan->plan_id }}][enable_synchronous]" 
                                                               value="1"
                                                               {{ $plan->enable_synchronous ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="sync_{{ $plan->plan_id }}">
                                                            <i class="fas fa-video mr-1"></i>
                                                            <strong>Enable Synchronous Mode</strong>
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Live classes with real-time interaction</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="async_{{ $plan->plan_id }}" 
                                                               name="plans[{{ $plan->plan_id }}][enable_asynchronous]" 
                                                               value="1"
                                                               {{ $plan->enable_asynchronous ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="async_{{ $plan->plan_id }}">
                                                            <i class="fas fa-play-circle mr-1"></i>
                                                            <strong>Enable Asynchronous Mode</strong>
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Self-paced learning with recorded content</small>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="plans[{{ $plan->plan_id }}][plan_id]" value="{{ $plan->plan_id }}">
                                    </div>
                                </div>
                                @endforeach
                                
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        How It Works
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h6>Start Date Behavior:</h6>
                                    <ul>
                                        <li><strong>Synchronous:</strong> Start date field is hidden from students, system automatically sets start date to 2 weeks from registration</li>
                                        <li><strong>Asynchronous:</strong> Students must select their preferred start date</li>
                                    </ul>
                                    
                                    <h6 class="mt-3">Configuration Options:</h6>
                                    <ul>
                                        <li>Enable/disable learning modes per plan type</li>
                                        <li>Full Plan vs Modular Plan independent settings</li>
                                        <li>Changes take effect immediately for new registrations</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('planSettingsForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = {};
        data.plans = [];
        
        // Process form data
        @foreach($plans as $plan)
            const planData = {
                plan_id: {{ $plan->plan_id }},
                enable_synchronous: document.getElementById('sync_{{ $plan->plan_id }}').checked,
                enable_asynchronous: document.getElementById('async_{{ $plan->plan_id }}').checked
            };
            data.plans.push(planData);
        @endforeach
        
        // Send AJAX request
        fetch('{{ route('admin.plans.update-learning-modes') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible';
                alertDiv.innerHTML = `
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check mr-2"></i>
                    ${data.message}
                `;
                form.insertBefore(alertDiv, form.firstChild);
                
                // Auto-hide after 3 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            } else {
                throw new Error(data.message || 'Failed to update settings');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible';
            alertDiv.innerHTML = `
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error: ${error.message}
            `;
            form.insertBefore(alertDiv, form.firstChild);
        });
    });
});
</script>
@endsection
