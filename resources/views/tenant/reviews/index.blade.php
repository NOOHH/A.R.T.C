<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Reviews</h1>
    <div class="list-group">
        @forelse($reviews as $r)
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <strong>{{ $r->author_name }}</strong>
                    <span class="badge bg-primary">{{ $r->rating }}/5</span>
                </div>
                <div class="text-muted">{{ $r->comment }}</div>
            </div>
        @empty
            <div class="text-muted">No reviews yet.</div>
        @endforelse
    </div>
    </div>
</body>
</html>



