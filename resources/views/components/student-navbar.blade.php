<header class="main-header">
  <div class="header-left">
    <a href="{{ route('home') }}" class="brand-link">
      <img src="{{ asset('images/ARTC_logo.png') }}" alt="Logo">
      <div class="brand-text">
        {{ $navbar['brand_name'] ?? 'Ascendo Review and Training Center' }}
      </div>
    </a>
  </div>

  <div class="header-search">
    @include('components.student-search')
  </div>

  <div class="header-right">
    <span class="notification-icon chat-trigger"
          data-bs-toggle="offcanvas"
          data-bs-target="#chatOffcanvas"
          aria-label="Open chat"
          role="button">
      <i class="bi bi-chat-dots"></i>
    </span>
    <span class="profile-icon">
      @php
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
      @endphp
      
      @if($profilePhoto)
        <img src="{{ asset('storage/profile-photos/' . $profilePhoto) }}" 
             alt="Profile" 
             class="navbar-profile-image">
      @else
        <div class="navbar-profile-placeholder">
          {{ substr(session('user_firstname', 'U'), 0, 1) }}{{ substr(session('user_lastname', 'U'), 0, 1) }}
        </div>
      @endif
    </span>
  </div>
</header>
@include('components.global-chat')
