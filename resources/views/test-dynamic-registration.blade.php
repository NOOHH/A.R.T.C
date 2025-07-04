<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dynamic Registration System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Dynamic Registration System Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Active Fields ({{ $activeFields->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($activeFields->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Field Name</th>
                                            <th>Label</th>
                                            <th>Type</th>
                                            <th>Program</th>
                                            <th>Required</th>
                                            <th>Bold</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeFields as $field)
                                            <tr>
                                                <td><code>{{ $field->field_name }}</code></td>
                                                <td class="{{ $field->is_bold ? 'fw-bold' : '' }}">{{ $field->field_label }}</td>
                                                <td>{{ $field->field_type }}</td>
                                                <td>{{ $field->program_type }}</td>
                                                <td>
                                                    @if($field->is_required)
                                                        <span class="badge bg-danger">Required</span>
                                                    @else
                                                        <span class="badge bg-secondary">Optional</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($field->is_bold)
                                                        <i class="fas fa-bold text-primary"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No active fields found.</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5>Inactive Fields ({{ $inactiveFields->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($inactiveFields->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Field Name</th>
                                            <th>Label</th>
                                            <th>Type</th>
                                            <th>Program</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inactiveFields as $field)
                                            <tr class="text-muted">
                                                <td><code>{{ $field->field_name }}</code></td>
                                                <td>{{ $field->field_label }}</td>
                                                <td>{{ $field->field_type }}</td>
                                                <td>{{ $field->program_type }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-success">All fields are active.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>Form Preview - Complete Program</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            @csrf
                            <x-dynamic-enrollment-form program-type="complete" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>Form Preview - Modular Program</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            @csrf
                            <x-dynamic-enrollment-form program-type="modular" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <a href="/admin/settings" class="btn btn-primary">Go to Admin Settings</a>
            <a href="/enrollment/full" class="btn btn-success">Test Full Enrollment</a>
            <a href="/enrollment/modular" class="btn btn-warning">Test Modular Enrollment</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>
