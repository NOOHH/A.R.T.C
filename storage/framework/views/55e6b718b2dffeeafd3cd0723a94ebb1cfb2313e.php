<!-- Professional Student Sidebar -->
<style>
.professional-sidebar.collapsed .sidebar-toggle-btn {
  display: none;
}

 </style>
<aside class="professional-sidebar" id="studentSidebar">
  <!-- Sidebar Header -->


  <!-- User Profile Section -->
  <div class="sidebar-profile">
    <?php
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
    ?>
    
    <div class="profile-avatar">
      <?php if($profilePhoto): ?>
        <img src="<?php echo e(asset('storage/profile-photos/' . $profilePhoto)); ?>" 
             alt="Profile" 
             class="avatar-image">
      <?php else: ?>
        <div class="avatar-placeholder">
          <?php echo e(substr(session('user_firstname', 'S'), 0, 1)); ?><?php echo e(substr(session('user_lastname', 'T'), 0, 1)); ?>

        </div>
      <?php endif; ?>
    </div>
    <div class="profile-info">
      <div class="profile-name"><?php echo e(session('user_firstname')); ?> <?php echo e(session('user_lastname')); ?></div>
      <div class="profile-role">Student</div>
    </div>
       <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>

  <!-- Navigation Menu -->
  <nav class="sidebar-navigation">
    <div class="nav-section">
      <div class="nav-section-title">Main</div>
      
      <!-- Dashboard -->
      <a href="<?php echo e(route('student.dashboard')); ?>" 
         class="nav-item <?php if(Route::currentRouteName()==='student.dashboard'): ?> active <?php endif; ?>">
        <div class="nav-icon">
          <i class="bi bi-speedometer2"></i>
        </div>
        <span class="nav-text">Dashboard</span>
      </a>

      <!-- Calendar -->
      <a href="<?php echo e(route('student.calendar')); ?>" 
         class="nav-item <?php if(Route::currentRouteName()==='student.calendar'): ?> active <?php endif; ?>">
        <div class="nav-icon">
          <i class="bi bi-calendar-week"></i>
        </div>
        <span class="nav-text">Calendar</span>
      </a>

      <!-- Enrolled Courses -->
      <a href="<?php echo e(route('student.enrolled-courses')); ?>" 
         class="nav-item <?php if(Route::currentRouteName()==='student.enrolled-courses'): ?> active <?php endif; ?>">
        <div class="nav-icon">
          <i class="bi bi-journal-bookmark"></i>
        </div>
        <span class="nav-text">My Courses</span>
      </a>

      <!-- Meetings -->
      <a href="<?php echo e(route('student.meetings')); ?>" 
         class="nav-item <?php if(Route::currentRouteName()==='student.meetings'): ?> active <?php endif; ?>">
        <div class="nav-icon">
          <i class="bi bi-camera-video"></i>
        </div>
        <span class="nav-text">Meetings</span>
      </a>
    </div>

    <!-- Programs Section -->
    <?php if(isset($studentPrograms) && !empty($studentPrograms)): ?>
    <div class="nav-section">
      <div class="nav-section-title">My Programs</div>
      
      <?php $__currentLoopData = $studentPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('student.course', $program['program_id'])); ?>" 
           class="nav-item program-item <?php if(request()->route('courseId')==$program['program_id']): ?> active <?php endif; ?>">
          <div class="nav-icon">
            <i class="bi bi-book"></i>
          </div>
          <div class="nav-text">
            <div class="program-name"><?php echo e($program['program_name']); ?></div>
            <small class="program-package"><?php echo e($program['package_name']); ?></small>
          </div>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <!-- Account Section -->
    <div class="nav-section">
      <div class="nav-section-title">Account</div>
      
      <!-- Settings -->
      <a href="<?php echo e(route('student.settings')); ?>" 
         class="nav-item <?php if(Route::currentRouteName()==='student.settings'): ?> active <?php endif; ?>">
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
      <small>ARTC © <?php echo e(date('Y')); ?></small>
    </div>
  </div>
</aside>

<!-- Sidebar Backdrop for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Mobile Toggle Button -->
<button class="mobile-sidebar-toggle" id="mobileSidebarToggle" title="Toggle Sidebar">
  <i class="bi bi-list"></i>
</button>

<script>
// Load and apply custom sidebar colors
document.addEventListener('DOMContentLoaded', function() {
    loadSidebarCustomization();
});

function loadSidebarCustomization() {
    fetch('/smartprep/api/sidebar-settings')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.colors) {
                applySidebarColors(data.colors);
            }
        })
        .catch(error => {
            console.log('No custom sidebar settings found, using defaults');
        });
}

function applySidebarColors(settings) {
    const sidebar = document.getElementById('studentSidebar');
    if (sidebar && settings) {
        // Apply custom CSS properties
        if (settings.primary_color) {
            sidebar.style.setProperty('--sidebar-bg', settings.primary_color);
            document.documentElement.style.setProperty('--sidebar-bg', settings.primary_color);
        }
        if (settings.secondary_color) {
            sidebar.style.setProperty('--sidebar-hover', settings.secondary_color);
            sidebar.style.setProperty('--sidebar-border', settings.secondary_color);
            document.documentElement.style.setProperty('--sidebar-hover', settings.secondary_color);
            document.documentElement.style.setProperty('--sidebar-border', settings.secondary_color);
        }
        if (settings.accent_color) {
            sidebar.style.setProperty('--sidebar-active', settings.accent_color);
            document.documentElement.style.setProperty('--sidebar-active', settings.accent_color);
        }
        if (settings.text_color) {
            sidebar.style.setProperty('--sidebar-text', settings.text_color);
            document.documentElement.style.setProperty('--sidebar-text', settings.text_color);
        }
        if (settings.hover_color) {
            sidebar.style.setProperty('--sidebar-hover', settings.hover_color);
            document.documentElement.style.setProperty('--sidebar-hover', settings.hover_color);
        }
    }
}
</script>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/components/student-sidebar.blade.php ENDPATH**/ ?>