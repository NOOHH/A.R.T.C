<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Tenant Menus</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Menus</h1>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form class="row g-2 mb-4" method="post" action="">
        @csrf
        <div class="col-md-3"><input class="form-control" name="name" placeholder="Menu Name" required></div>
        <div class="col-md-7"><input class="form-control" name="items[0][label]" placeholder="Item 1 Label"> <input class="form-control mt-1" name="items[0][url]" placeholder="Item 1 URL"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Save</button></div>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Name</th><th>Items</th><th>Updated</th></tr></thead>
        <tbody>
            @foreach($menus as $m)
                <tr><td>{{ $m->name }}</td><td><pre class="m-0">{{ $m->items }}</pre></td><td>{{ $m->updated_at }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>



