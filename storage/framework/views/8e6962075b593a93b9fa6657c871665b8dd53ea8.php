<style>
    :root {
        --primary-color: #1e40af;
        --secondary-color: #3b82f6;
        --accent-color: #60a5fa;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
    }
    
    body {
        background: var(--gray-100);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        color: var(--gray-800);
    }
    
    /* Top Navigation */
    .top-navbar {
        background: white;
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--primary-color);
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    
    .navbar-brand:hover {
        color: var(--secondary-color);
    }
    
    .navbar-nav .nav-link {
        color: var(--gray-600);
        font-weight: 500;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    
    .navbar-nav .nav-link:hover {
        background: var(--gray-100);
        color: var(--primary-color);
    }
    
    .navbar-nav .nav-link.active {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        font-weight: 600;
    }
    
    .dropdown-menu {
        border: 1px solid var(--gray-200);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    
    .dropdown-item {
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background: var(--gray-100);
        color: var(--primary-color);
    }
    
    .nav-link {
        color: var(--gray-600);
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .nav-link:hover {
        background: var(--gray-100);
        color: var(--primary-color);
    }
    
    /* Settings Layout */
    .settings-layout {
        display: flex;
        min-height: calc(100vh - 80px);
        flex-direction: column;
    }
    
    /* Settings Navbar */
    .settings-navbar {
        background: white;
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem 0;
        position: sticky;
        top: 80px;
        z-index: 999;
    }
    
    .settings-nav-brand h4 {
        color: var(--primary-color);
        margin: 0;
    }
    
    .settings-nav-tabs {
        display: flex;
        gap: 0.5rem;
    }
    
    .settings-nav-tab {
        background: none;
        border: 1px solid var(--gray-300);
        color: var(--gray-600);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    
    .settings-nav-tab:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    .settings-nav-tab.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    
    .settings-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    /* Main Layout */
    .settings-main-layout {
        display: flex;
        flex: 1;
    }
    
    /* Settings Sidebar */
    .settings-sidebar {
        width: 400px;
        background: white;
        border-right: 1px solid var(--gray-200);
        overflow-y: auto;
        height: calc(100vh - 160px);
        position: sticky;
        top: 160px;
    }
    
    .sidebar-section {
        padding: 2rem;
        border-bottom: 1px solid var(--gray-200);
        display: none;
    }
    
    .sidebar-section.active {
        display: block;
    }
    
    .section-header {
        margin-bottom: 1.5rem;
    }
    
    .section-header h5 {
        color: var(--gray-800);
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
        outline: none;
    }
    
    .color-picker-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .color-input {
        width: 40px;
        height: 40px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        cursor: pointer;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--secondary-color);
        transform: translateY(-1px);
    }
    
    .btn-outline-primary {
        background: none;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
    }
    
    /* Preview Panel */
    .preview-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--gray-50);
        position: relative;
    }
    
    .preview-header {
        background: white;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .preview-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-800);
    }
    
    .preview-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .preview-btn {
        background: var(--gray-100);
        border: 1px solid var(--gray-300);
        color: var(--gray-600);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .preview-btn:hover {
        background: var(--gray-200);
        border-color: var(--gray-400);
        color: var(--gray-700);
    }
    
    .preview-iframe-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        position: relative;
    }
    
    .preview-iframe {
        width: 100%;
        height: calc(100vh - 160px);
        border: none;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        transition: opacity 0.3s ease;
    }
    
    .preview-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        z-index: 10;
        background: rgba(255, 255, 255, 0.9);
        padding: 2rem;
        border-radius: 8px;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--gray-300);
        border-top: 3px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .settings-sidebar {
            width: 300px;
        }
    }
    
    @media (max-width: 992px) {
        .settings-main-layout {
            flex-direction: column;
        }
        
        .settings-sidebar {
            width: 100%;
            position: relative;
            height: auto;
        }
        
        .preview-panel {
            height: 500px;
        }
    }
    
    /* ====== SIDEBAR CUSTOMIZATION STYLES ====== */
    .sidebar-preview-container {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        min-height: 400px;
    }
    
    .sidebar-preview {
        background: var(--preview-primary, #1a1a1a);
        color: var(--preview-text, #e0e0e0);
        border-radius: 8px;
        padding: 1rem;
        width: 100%;
        min-height: 350px;
        transition: all 0.3s ease;
    }
    
    .preview-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--preview-secondary, #2d2d2d);
        margin-bottom: 1rem;
    }
    
    .preview-avatar-placeholder {
        width: 40px;
        height: 40px;
        background: var(--preview-accent, #3b82f6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        color: white;
    }
    
    .preview-profile-info {
        flex: 1;
    }
    
    .preview-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--preview-text, #e0e0e0);
    }
    
    .preview-role {
        font-size: 0.8rem;
        color: var(--preview-text-muted, #9ca3af);
        opacity: 0.8;
    }
    
    .preview-toggle {
        background: none;
        border: none;
        color: var(--preview-text, #e0e0e0);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .preview-toggle:hover {
        background: var(--preview-hover, #374151);
    }
    
    .preview-nav {
        margin-top: 1rem;
    }
    
    .preview-section-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--preview-text-muted, #9ca3af);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .preview-nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        margin-bottom: 0.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }
    
    .preview-nav-item:hover {
        background: var(--preview-hover, #374151);
    }
    
    .preview-nav-item.active {
        background: var(--preview-accent, #3b82f6);
        color: white;
    }
    
    .preview-nav-item i {
        width: 16px;
        text-align: center;
    }
    
    .color-preview {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 0.25rem;
        border: 1px solid #ccc;
    }
    
    .color-picker-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .color-input {
        width: 50px;
        height: 38px;
        border: 1px solid #ddd;
        border-radius: 6px;
        cursor: pointer;
        padding: 0;
        background: none;
    }
    
    .color-input::-webkit-color-swatch {
        border: none;
        border-radius: 4px;
    }
    
    /* Role selector styles */
    .btn-check:checked + .btn-outline-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    
    /* Preset buttons */
    .btn-sm .color-preview {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    /* Animation for color changes */
    .sidebar-preview * {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    
    /* Mobile responsiveness for sidebar customization */
    @media (max-width: 768px) {
        .color-picker-group {
            flex-direction: column;
            align-items: stretch;
        }
        
        .color-input {
            width: 100%;
            height: 50px;
        }
        
        .sidebar-preview-container {
            margin-top: 1rem;
        }
    }
</style>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/customize-styles.blade.php ENDPATH**/ ?>