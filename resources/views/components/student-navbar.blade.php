<header class="main-header">
  <div class="header-left">
    <a href="{{ route('home') }}" class="brand-link">
      @php
        // Get brand logo from NavbarComposer data (tenant-specific) or fallback
        $brandLogo = $navbar['brand_logo'] ?? $settings['navbar']['brand_logo'] ?? null;
        $defaultLogo = asset('images/ARTC_logo.png');
      @endphp
      
      @if($brandLogo)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($brandLogo) }}" 
             alt="Brand Logo"
             onerror="this.src='{{ $defaultLogo }}'">
      @else
        <img src="{{ $defaultLogo }}" alt="Logo">
      @endif
      
      <div class="brand-text">
        {{ $navbar['brand_name'] ?? $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center' }}
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
        try {
          $student = \App\Models\Student::where('user_id', session('user_id'))->first();
          $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
        } catch (\Exception $e) {
          // In preview mode or when students table doesn't exist, use session data
          $student = null;
          $profilePhoto = null;
        }
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
