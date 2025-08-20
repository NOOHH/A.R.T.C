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

    /* Settings Navigation */
    .settings-navbar {
        background: white;
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem 0;
        position: sticky;
        top: 73px;
        z-index: 999;
    }
    
    .settings-nav-tabs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .settings-nav-tab {
        background: transparent;
        border: 1px solid var(--gray-300);
        color: var(--gray-600);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        text-decoration: none;
    }
    
    .settings-nav-tab:hover {
        background: var(--gray-100);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .settings-nav-tab.active {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-color: var(--primary-color);
    }
    
    /* Main Layout */
    .settings-main-layout {
        display: flex;
        min-height: calc(100vh - 146px);
    }
    
    .settings-sidebar {
        width: 38%; /* narrower customization */
        background: white;
        border-right: 1px solid var(--gray-200);
        overflow-y: auto;
        max-height: calc(100vh - 146px);
    }
    
    .preview-panel {
        width: 62%; /* larger preview */
        background: var(--gray-100);
        position: relative;
    }
    
    .sidebar-section {
        display: none;
        padding: 1.25rem; /* tighter spacing */
    }
    
    .sidebar-section.active {
        display: block;
    }
    
    .section-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-200);
    }
    
    .section-header h5 {
        margin: 0;
        color: var(--gray-800);
        font-weight: 600;
    }
    
    /* Form Styling */
    .form-group {
        margin-bottom: 1rem; /* tighter spacing */
    }
    
    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Match compact color picker row like admin */
    .color-picker-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .color-picker-group input[type="text"] {
        flex: 1;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .btn-outline-primary {
        background: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-success {
        background: var(--success-color);
        color: white;
    }
    
    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
    }
    
    /* Color Input Styling */
    input[type="color"] {
        width: 60px;
        height: 40px;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        cursor: pointer;
        background: none;
    }
    
    input[type="color"]::-webkit-color-swatch-wrapper {
        padding: 0;
        border-radius: 6px;
    }
    
    input[type="color"]::-webkit-color-swatch {
        border: none;
        border-radius: 6px;
    }
    
    /* Preview Panel */
    .preview-sticky {
        position: sticky;
        top: 146px;
        height: calc(100vh - 146px);
        display: flex;
        flex-direction: column;
    }
    
    .preview-header {
        background: white;
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .preview-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .preview-iframe {
        flex: 1;
        border: none;
        background: white;
    }
    
    .settings-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
        .settings-main-layout {
            flex-direction: column;
        }
        
        .settings-sidebar {
            width: 100%;
        }
        
        .preview-panel {
            width: 100%;
            min-height: 500px;
        }
        
        .preview-sticky {
            position: relative;
            top: auto;
            height: auto;
        }
    }
    
    @media (max-width: 768px) {
        .settings-nav-tabs {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .settings-nav-tabs::-webkit-scrollbar {
            display: none;
        }
        
        .settings-nav-tab {
            flex-shrink: 0;
        }
        
        .sidebar-section {
            padding: 1rem;
        }
    }
    
    /* Loading States */
    .btn.loading {
        opacity: 0.7;
        pointer-events: none;
    }
    
    .btn.loading::after {
        content: '';
        width: 16px;
        height: 16px;
        margin-left: 8px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Custom Scrollbar */
    .settings-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .settings-sidebar::-webkit-scrollbar-track {
        background: var(--gray-100);
    }
    
    .settings-sidebar::-webkit-scrollbar-thumb {
        background: var(--gray-400);
        border-radius: 3px;
    }
    
    .settings-sidebar::-webkit-scrollbar-thumb:hover {
        background: var(--gray-500);
    }
</style>
