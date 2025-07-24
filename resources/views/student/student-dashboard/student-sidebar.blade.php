{{-- resources/views/student/student-dashboard/student-dashboard-layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  {{-- … head as before … --}}
  <style>
    .sidebar {
      background: #fff;
      border-right: 1px solid #dee2e6;
      height: 100vh;
      position: sticky;
      top: 0;
      overflow-y: auto;
    }
    .sidebar .nav-link {
      color: #495057;
      border-radius: .375rem;
      margin-bottom: .5rem;
    }
    .sidebar .nav-link.active {
      background-color: #e7f1ff;
      color: #0d6efd;
      font-weight: 600;
    }
    /* offcanvas version */
    .offcanvas .sidebar {
      height: calc(100vh - 56px);
    }
  </style>
</head>
<body>
  <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display:none;">@csrf</form>

  <div class="container-fluid p-0">
    <div class="row g-0">

      {{-- Desktop sidebar --}}
      <aside class="col-lg-3 d-none d-lg-block">
        @yield('sidebar')
      </aside>

      {{-- Mobile offcanvas --}}
      <div class="col-12 d-lg-none mb-3">
        <button class="btn btn-primary" 
                data-bs-toggle="offcanvas" 
                data-bs-target="#sidebarCanvas">
          <i class="bi bi-list"></i> Menu
        </button>

        <div class="offcanvas offcanvas-start" id="sidebarCanvas">
          <div class="offcanvas-header">
            <h5>Menu</h5>
            <button class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body p-0">
            @yield('sidebar')
          </div>
        </div>
      </div>

      {{-- Main content --}}
      <main class="col-12 col-lg-9">
        <div class="content-wrapper p-3">
          @yield('content')
        </div>
      </main>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  {{-- … other scripts … --}}
</body>
</html>
