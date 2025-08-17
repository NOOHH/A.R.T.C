<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Courses</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $course)
                <tr>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->created_at }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="text-muted">No courses yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>



