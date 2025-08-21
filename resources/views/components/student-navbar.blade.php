@php
    // Get brand name from settings if available, otherwise use default
    $brandName = $settings['navbar']['brand_name'] ?? 
                 $navbarBrandName ?? 
                 'Ascendo Review & Training Center';
    
    // Get brand logo from settings if available
    $brandLogo = $settings['navbar']['brand_logo'] ?? null;
    $defaultLogo = asset('images/ARTC_logo.png');
@endphp

<header class="main-header">
  <div class="header-left">
    <a href="{{ route('home') }}" class="brand-link">
      @if($brandLogo)
        <img src="{{ asset($brandLogo) }}" 
             alt="{{ $brandName }}" 
             onerror="this.src='{{ $defaultLogo }}'">
      @else
        <img src="{{ $defaultLogo }}" alt="{{ $brandName }}">
      @endif
      <div class="brand-text">
        {{ $brandName }}
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
        // Check if this is preview mode
        $isPreview = request()->has('preview') || request()->query('preview') === 'true';
        
        if ($isPreview) {
          // Use mock data for preview mode
          $profilePhoto = null;
        } else {
          // Only query database if not in preview mode
          try {
            $student = \App\Models\Student::where('user_id', session('user_id'))->first();
            $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
          } catch (\Exception $e) {
            // If there's an error (e.g., table doesn't exist), use null
            $profilePhoto = null;
          }
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
