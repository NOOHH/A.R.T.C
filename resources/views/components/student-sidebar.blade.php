<!-- Professional Student Sidebar -->
<aside class="professional-sidebar" id="studentSidebar">
  <!-- Sidebar Header -->
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <div class="brand-icon">
        <i class="bi bi-mortarboard-fill"></i>
      </div>
      <div class="brand-content">
        <div class="brand-title">ARTC</div>
        <div class="brand-subtitle">Student Portal</div>
      </div>
    </div>
    <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>

  <!-- User Profile Section -->
  <div class="sidebar-profile">
    @php
      $student = \App\Models\Student::where('user_id', session('user_id'))->first();
      $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
    @endphp
    
    <div class="profile-avatar">
      @if($profilePhoto)
        <img src="{{ asset('storage/profile-photos/' . $profilePhoto) }}" 
             alt="Profile" 
             class="avatar-image">
      @else
        <div class="avatar-placeholder">
          {{ substr(session('user_firstname', 'S'), 0, 1) }}{{ substr(session('user_lastname', 'T'), 0, 1) }}
        </div>
      @endif
    </div>
    <div class="profile-info">
      <div class="profile-name">{{ session('user_firstname') }} {{ session('user_lastname') }}</div>
      <div class="profile-role">Student</div>
    </div>
  </div>

  <!-- Navigation Menu -->
  <nav class="sidebar-navigation">
    <div class="nav-section">
      <div class="nav-section-title">Main</div>
      
      <!-- Dashboard -->
      <a href="{{ route('student.dashboard') }}" 
         class="nav-item @if(Route::currentRouteName()==='student.dashboard') active @endif">
        <div class="nav-icon">
          <i class="bi bi-speedometer2"></i>
        </div>
        <span class="nav-text">Dashboard</span>
      </a>

      <!-- Calendar -->
      <a href="{{ route('student.calendar') }}" 
         class="nav-item @if(Route::currentRouteName()==='student.calendar') active @endif">
        <div class="nav-icon">
          <i class="bi bi-calendar-week"></i>
        </div>
        <span class="nav-text">Calendar</span>
      </a>

      <!-- Enrolled Courses -->
      <a href="{{ route('student.enrolled-courses') }}" 
         class="nav-item @if(Route::currentRouteName()==='student.enrolled-courses') active @endif">
        <div class="nav-icon">
          <i class="bi bi-journal-bookmark"></i>
        </div>
        <span class="nav-text">My Courses</span>
      </a>

      <!-- Meetings -->
      <a href="{{ route('student.meetings') }}" 
         class="nav-item @if(Route::currentRouteName()==='student.meetings') active @endif">
        <div class="nav-icon">
          <i class="bi bi-camera-video"></i>
        </div>
        <span class="nav-text">Meetings</span>
      </a>
    </div>

    <!-- Programs Section -->
    @if(isset($studentPrograms) && !empty($studentPrograms))
    <div class="nav-section">
      <div class="nav-section-title">My Programs</div>
      
      @foreach($studentPrograms as $program)
        <a href="{{ route('student.course', $program['program_id']) }}" 
           class="nav-item program-item @if(request()->route('courseId')==$program['program_id']) active @endif">
          <div class="nav-icon">
            <i class="bi bi-book"></i>
          </div>
          <div class="nav-text">
            <div class="program-name">{{ $program['program_name'] }}</div>
            <small class="program-package">{{ $program['package_name'] }}</small>
          </div>
        </a>
      @endforeach
    </div>
    @endif

    <!-- Account Section -->
    <div class="nav-section">
      <div class="nav-section-title">Account</div>
      
      <!-- Settings -->
      <a href="{{ route('student.settings') }}" 
         class="nav-item @if(Route::currentRouteName()==='student.settings') active @endif">
        <div class="nav-icon">
          <i class="bi bi-gear"></i>
        </div>
        <span class="nav-text">Settings</span>
      </a>

      <!-- Logout -->
      <a href="#" class="nav-item logout-item" onclick="document.getElementById('logout-form').submit();">
        <div class="nav-icon">
          <i class="bi bi-box-arrow-right"></i>
        </div>
        <span class="nav-text">Logout</span>
      </a>
    </div>
  </nav>

  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    <div class="footer-text">
      <small>ARTC © {{ date('Y') }}</small>
    </div>
  </div>
</aside>

<!-- Sidebar Backdrop for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Mobile Toggle Button -->
<button class="mobile-sidebar-toggle" id="mobileSidebarToggle" title="Toggle Sidebar">
  <i class="bi bi-list"></i>
</button>
