<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Tenant Pages</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Pages</h1>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form class="row g-2 mb-4" method="post" action="">
        @csrf
        <div class="col-md-3"><input class="form-control" name="title" placeholder="Title" required></div>
        <div class="col-md-3"><input class="form-control" name="slug" placeholder="Slug" required></div>
        <div class="col-md-4"><input class="form-control" name="content" placeholder="Content"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Save</button></div>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Title</th><th>Slug</th><th>Updated</th></tr></thead>
        <tbody>
            @foreach($pages as $p)
                <tr><td>{{ $p->title }}</td><td>{{ $p->slug }}</td><td>{{ $p->updated_at }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>



