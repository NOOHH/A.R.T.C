<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Auth Form Fields</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Authentication Form Fields</h3>
    <a href="/admin/settings" class="btn btn-outline-secondary">Back to Settings</a>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.settings.auth-fields.set-identifier') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-auto">
          <label class="form-label">Login Identifier</label>
          <select name="login_identifier" class="form-select">
            <option value="email" {{ $loginIdentifier==='email' ? 'selected' : '' }}>Email</option>
            <option value="username" {{ $loginIdentifier==='username' ? 'selected' : '' }}>Username</option>
          </select>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Login Fields</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.settings.auth-fields.store') }}" class="row g-2 mb-3">
            @csrf
            <input type="hidden" name="form" value="login">
            <div class="col-6"><input class="form-control" name="field_key" placeholder="field key (e.g., email)" required></div>
            <div class="col-6"><input class="form-control" name="label" placeholder="Label" required></div>
            <div class="col-4">
              <select class="form-select" name="type">
                <option>text</option>
                <option>email</option>
                <option>password</option>
                <option>tel</option>
                <option>checkbox</option>
              </select>
            </div>
            <div class="col-4"><input class="form-control" name="placeholder" placeholder="Placeholder"></div>
            <div class="col-4"><input class="form-control" name="sort_order" type="number" value="0"></div>
            <div class="col-12 d-flex gap-3">
              <div class="form-check"><input class="form-check-input" type="checkbox" name="is_required" id="lreq"><label class="form-check-label" for="lreq">Required</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" name="is_enabled" id="len" checked><label class="form-check-label" for="len">Enabled</label></div>
            </div>
            <div class="col-12"><button class="btn btn-primary">Add/Update</button></div>
          </form>

          <ul class="list-group">
            @foreach($loginFields as $f)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong>{{ $f->label }}</strong> <small class="text-muted">({{ $f->field_key }})</small>
                <div class="text-muted">Type: {{ $f->type }} • Required: {{ $f->is_required ? 'Yes' : 'No' }} • Enabled: {{ $f->is_enabled ? 'Yes' : 'No' }}</div>
              </div>
              <form method="POST" action="{{ route('admin.settings.auth-fields.delete', $f) }}" onsubmit="return confirm('Delete field?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Register Fields</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.settings.auth-fields.store') }}" class="row g-2 mb-3">
            @csrf
            <input type="hidden" name="form" value="register">
            <div class="col-6"><input class="form-control" name="field_key" placeholder="field key (e.g., name)" required></div>
            <div class="col-6"><input class="form-control" name="label" placeholder="Label" required></div>
            <div class="col-4">
              <select class="form-select" name="type">
                <option>text</option>
                <option>email</option>
                <option>password</option>
                <option>tel</option>
                <option>checkbox</option>
              </select>
            </div>
            <div class="col-4"><input class="form-control" name="placeholder" placeholder="Placeholder"></div>
            <div class="col-4"><input class="form-control" name="sort_order" type="number" value="0"></div>
            <div class="col-12 d-flex gap-3">
              <div class="form-check"><input class="form-check-input" type="checkbox" name="is_required" id="rreq"><label class="form-check-label" for="rreq">Required</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" name="is_enabled" id="ren" checked><label class="form-check-label" for="ren">Enabled</label></div>
            </div>
            <div class="col-12"><button class="btn btn-primary">Add/Update</button></div>
          </form>

          <ul class="list-group">
            @foreach($registerFields as $f)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong>{{ $f->label }}</strong> <small class="text-muted">({{ $f->field_key }})</small>
                <div class="text-muted">Type: {{ $f->type }} • Required: {{ $f->is_required ? 'Yes' : 'No' }} • Enabled: {{ $f->is_enabled ? 'Yes' : 'No' }}</div>
              </div>
              <form method="POST" action="{{ route('admin.settings.auth-fields.delete', $f) }}" onsubmit="return confirm('Delete field?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
