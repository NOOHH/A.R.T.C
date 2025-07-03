<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- Global UI Styles --}}
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}
</head>
<body>
    <div class="container mt-5">
        <h1>UI System Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Navbar Settings</h3>
                <div class="card">
                    <div class="card-body">
                        @if($navbarSettings->count() > 0)
                            @foreach($navbarSettings as $key => $value)
                                <div class="mb-2">
                                    <strong>{{ $key }}:</strong> {{ $value }}
                                </div>
                            @endforeach
                        @else
                            <p class="text-warning">No navbar settings found.</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <h3>Form Requirements</h3>
                <div class="card">
                    <div class="card-body">
                        @if($formRequirements->count() > 0)
                            @foreach($formRequirements as $req)
                                <div class="mb-2">
                                    <strong>{{ $req->field_label }}</strong> 
                                    ({{ $req->field_type }})
                                    @if($req->is_required) <span class="text-danger">*</span> @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-warning">No form requirements found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h3>Dynamic Form Test</h3>
                <div class="card">
                    <div class="card-body">
                        <x-dynamic-enrollment-form program-type="complete" />
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h3>Style Preview</h3>
                <div class="card navbar" style="background-color: var(--navbar-header-bg); color: var(--navbar-header-text);">
                    <div class="card-body">
                        <p>This is a preview using the current navbar styles.</p>
                        <div class="sidebar p-3" style="background-color: var(--navbar-sidebar-bg); color: var(--navbar-sidebar-text);">
                            Sidebar preview
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('admin.settings') }}" class="btn btn-primary">Go to Admin Settings</a>
            <a href="{{ route('enrollment.full') }}" class="btn btn-success">Test Full Enrollment</a>
            <a href="{{ route('enrollment.modular') }}" class="btn btn-info">Test Modular Enrollment</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
