<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Tenant Modules</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Modules</h1>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form class="row g-2 mb-4" method="post" action="">
        @csrf
        <div class="col-md-3">
            <select name="course_id" class="form-select" required>
                <option value="">Select Course</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><input class="form-control" name="title" placeholder="Module Title" required></div>
        <div class="col-md-4"><input class="form-control" name="content" placeholder="Content"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Create</button></div>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Course</th><th>Title</th><th>Created</th></tr></thead>
        <tbody>
            @foreach($modules as $m)
                <tr><td>{{ $m->course_title }}</td><td>{{ $m->title }}</td><td>{{ $m->created_at }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>



