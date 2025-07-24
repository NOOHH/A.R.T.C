<header class="main-header">
  <div class="header-left">
    {{-- Sidebar toggle button for all screen sizes --}}
    @if(!isset($hideSidebar) || !$hideSidebar)
      <button class="sidebar-toggle-btn me-2" id="sidebarToggleBtn" title="Toggle Sidebar">
        <i class="bi bi-list"></i>
      </button>
    @endif
    
    <a href="{{ route('home') }}" class="brand-link">
      <img src="{{ asset('images/ARTC_logo.png') }}" alt="Logo">
      <div class="brand-text">
        Ascendo Review<br>and Training Center
      </div>
    </a>
  </div>

  <div class="header-search">
    <div class="search-box">
      <span class="search-icon">🔍</span>
      <input type="text" placeholder="Search courses or topics">
      <button class="search-btn">🔍</button>
    </div>
  </div>

  <div class="header-right">
    <span class="notification-icon chat-trigger"
          data-bs-toggle="offcanvas"
          data-bs-target="#chatOffcanvas"
          aria-label="Open chat"
          role="button">
      <i class="bi bi-chat-dots"></i>
    </span>
    <span class="profile-icon">👤</span>
  </div>
</header>
@include('components.global-chat')
