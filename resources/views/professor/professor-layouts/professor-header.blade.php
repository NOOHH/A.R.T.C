{{-- resources/views/components/professor-header.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
    <div class="container-fluid">
      {{-- Brand --}}
      <a class="navbar-brand d-flex align-items-center" href="{{ route('professor.dashboard') }}">
        <img src="{{ asset('images/ARTC_logo.png') }}"
             alt="A.R.T.C logo"
             height="40"
             class="d-inline-block align-text-top">
        <div class="ms-2 d-none d-lg-block">
          <span class="h6 mb-0">Ascendo Review &amp; Training Center</span><br>
          <small class="text-muted">Professor Portal</small>
        </div>
      </a>
  
      {{-- Mobile toggle --}}
      <button class="navbar-toggler" type="button"
              data-bs-toggle="collapse"
              data-bs-target="#profHeaderNav"
              aria-controls="profHeaderNav"
              aria-expanded="false"
              aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
  
      {{-- Collapsible content --}}
      <div class="collapse navbar-collapse" id="profHeaderNav">
        {{-- Search + Filter --}}
        <form class="d-flex mx-auto" role="search">
          <input class="form-control me-2"
                 type="search"
                 placeholder="Search..."
                 aria-label="Search">
          <button class="btn btn-outline-primary" type="submit" aria-label="Apply filter">
            <i class="bi bi-funnel"></i>
            <span class="visually-hidden">Filter</span>
          </button>
        </form>
  
        {{-- Chat & Profile --}}
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item me-3">
            <button class="btn btn-link p-0 text-primary"
                    id="chatTriggerBtn"
                    aria-label="Open chat">
              <i class="bi bi-chat-dots fs-4"></i>
              <span class="visually-hidden">Chat</span>
            </button>
          </li>
          <li class="nav-item">
            <a href="{{ route('professor.settings') }}"
               class="nav-link p-0 text-secondary"
               aria-label="Profile settings">
              <i class="bi bi-person-circle fs-4"></i>
              <span class="visually-hidden">Profile</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  