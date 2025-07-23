<aside class="modern-sidebar col-lg-3 col-xl-2" id="modernSidebar">
  <div class="sidebar-header">
    <button class="sidebar-toggle" id="sidebarToggle">
      <i class="bi bi-arrow-left"></i>
    </button>
    <div class="sidebar-brand">
      <i class="bi bi-mortarboard"></i>
      <span class="brand-title">Student Portal</span>
    </div>
    <button class="sidebar-close" id="sidebarClose">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="sidebar-content">
    <nav class="sidebar-nav">
      <!-- Dashboard -->
      <div class="nav-item">
        <a href="{{ route('student.dashboard') }}"
           class="nav-link @if(Route::currentRouteName()==='student.dashboard') active @endif">
          <i class="bi bi-speedometer2"></i>
          <span>Dashboard</span>
        </a>
      </div>
      <!-- Calendar -->
      <div class="nav-item">
        <a href="{{ route('student.calendar') }}"
           class="nav-link @if(Route::currentRouteName()==='student.calendar') active @endif">
          <i class="bi bi-calendar3"></i>
          <span>Calendar</span>
        </a>
      </div>
      <!-- Meetings -->
      <div class="nav-item">
        <a href="{{ route('student.meetings') }}"
           class="nav-link @if(Route::currentRouteName()==='student.meetings') active @endif">
          <i class="bi bi-camera-video"></i>
          <span>Meetings</span>
        </a>
      </div>
      <!-- My Programs dropdown -->
      <div class="nav-item dropdown-nav @if(str_starts_with(Route::currentRouteName(), 'student.course')) active show @endif">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#programsMenu">
          <i class="bi bi-journal-bookmark"></i>
          <span>My Programs</span>
        </a>
        <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'student.course')) show @endif" id="programsMenu">
          <div class="submenu">
            @forelse($studentPrograms as $program)
              <a href="{{ route('student.course', $program['program_id']) }}"
                 class="submenu-link @if(request()->route('courseId')==$program['program_id']) active @endif">
                <i class="bi bi-book"></i>
                <span class="program-info">
                  <div class="program-name">{{ $program['program_name'] }}</div>
                  <small class="program-details">{{ $program['package_name'] }}</small>
                </span>
              </a>
            @empty
              <div class="submenu-link disabled">
                <i class="bi bi-info-circle"></i>
                <span>No programs available. Contact administrator.</span>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </nav>
  </div>

  <div class="user-profile">
    <div class="user-info">
      <div class="user-avatar">
        {{ strtoupper(substr(optional($user)->name ?? 'S', 0, 1)) }}
      </div>
      <div class="user-details">
        <h6>{{ optional($user)->name ?? 'Student' }}</h6>
        <span>Student</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-item">
        <a href="{{ route('student.settings') }}"
           class="nav-link @if(Route::currentRouteName()==='student.settings') active @endif">
          <i class="bi bi-gear"></i>
          <span>Settings</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </div>
    </nav>
  </div>
</aside>

<!-- Floating arrow button for collapsed sidebar -->
<button class="sidebar-reopen-btn" id="sidebarReopenBtn" style="display:none; position:fixed; top:50%; left:0; transform:translateY(-50%); z-index:2000; background:#fff; border:none; border-radius:0 8px 8px 0; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:8px 10px; cursor:pointer;">
  <i class="bi bi-arrow-right" style="font-size:1.5rem; color:#2d1b69;"></i>
</button>
