<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 40px;
            background-color: #f8f9fa;
        }
        .error-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .error-icon {
            color: #dc3545;
            font-size: 3em;
            margin-bottom: 20px;
        }
        .btn-return {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container text-center">
            <div class="error-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            
            <h1>{{ $title ?? 'Error' }}</h1>
            
            <p class="lead">{{ $message ?? 'An unexpected error occurred.' }}</p>
            
            @if(isset($details))
            <div class="alert alert-light border mt-4">
                <small class="text-muted">Technical details:</small>
                <p class="mb-0">{{ $details }}</p>
            </div>
            @endif
            
            <div class="btn-return">
                <a href="{{ $returnUrl ?? url('/') }}" class="btn btn-primary">
                    Return to Previous Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
