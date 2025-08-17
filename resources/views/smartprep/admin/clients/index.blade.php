<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Client Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Force reload of styles with timestamp: <?php echo time(); ?> -->
    <style>
        :root {
            /* Modern Color Scheme */
            --primary-color: #4361ee;
            --primary-color-dark: #3a56d4;
            --primary-color-light: #4895ef;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            
            /* Bootstrap Color Variables */
            --bs-primary-rgb: 67, 97, 238;
            --bs-secondary-rgb: 63, 55, 201;
            --bs-success-rgb: 46, 204, 113;
            --bs-info-rgb: 52, 152, 219;
            --bs-warning-rgb: 243, 156, 18;
            --bs-danger-rgb: 231, 76, 60;
            
            /* Background Colors */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            
            /* Text Colors */
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-tertiary: #64748b;
            --text-muted: #94a3b8;
            
            /* Grays */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e2e8f0;
            --gray-300: #d1d5db;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            
            /* Borders & Shadows */
            --border-color: #e2e8f0;
            --border-radius-sm: 0.375rem;
            --border-radius: 0.5rem;
            --border-radius-md: 0.75rem;
            --border-radius-lg: 1rem;
            --border-radius-xl: 1.5rem;
            
            /* Box Shadows */
            --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-inner: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);
            
            /* Transitions */
            --transition-fast: all 0.15s ease;
            --transition: all 0.25s ease;
            --transition-slow: all 0.35s ease;
            
            /* Spacing */
            --spacing-1: 0.25rem;
            --spacing-2: 0.5rem;
            --spacing-3: 0.75rem;
            --spacing-4: 1rem;
            --spacing-5: 1.25rem;
            --spacing-6: 1.5rem;
            --spacing-8: 2rem;
            --spacing-10: 2.5rem;
            --spacing-12: 3rem;
            --spacing-16: 4rem;
            
            /* Layout */
            --container-padding: 1.5rem;
            --header-height: 70px;
            --sidebar-width: 260px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: var(--bg-secondary);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        /* Smooth scrolling for whole page */
        html {
            scroll-behavior: smooth;
        }
        
        /* Better Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.5em;
            color: var(--text-primary);
        }
        
        p {
            margin-bottom: 1rem;
        }
        
        a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        a:hover {
            color: var(--primary-color-dark);
        }
        
        /* Top Navigation - Modern Style */
        .top-navbar {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--header-height);
            box-shadow: var(--shadow-sm);
        }
        
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            padding: 0 var(--container-padding);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
            height: 100%;
            padding: 0 var(--spacing-4);
            position: relative;
        }
        
        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            height: 3px;
            width: 100%;
            background: var(--primary-color);
            transform: scaleX(0.6);
            transform-origin: 0 0;
            transition: var(--transition);
        }
        
        .navbar-brand:hover {
            color: var(--primary-color-dark);
        }
        
        .navbar-brand:hover::after {
            transform: scaleX(1);
        }
        
        .navbar-brand i {
            font-size: 1.75rem;
            margin-right: var(--spacing-3);
            color: var(--primary-color);
        }
        
        .navbar-nav {
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        .navbar-nav .nav-item {
            height: 100%;
            position: relative;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-secondary);
            font-weight: 600;
            padding: 0 var(--spacing-5);
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            align-items: center;
            height: 100%;
            position: relative;
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            height: 3px;
            width: 60%;
            background: var(--primary-color);
            transition: var(--transition);
            border-radius: 3px 3px 0 0;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link:hover::after {
            transform: translateX(-50%) scaleX(1);
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        .navbar-nav .nav-link.active::after {
            transform: translateX(-50%) scaleX(1);
        }
        
        .navbar-nav .nav-link i {
            margin-right: var(--spacing-2);
            font-size: 1.25rem;
            opacity: 0.8;
            transition: var(--transition);
        }
        
        .navbar-nav .nav-link:hover i {
            opacity: 1;
            transform: translateY(-1px);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: var(--border-radius);
            margin-top: var(--spacing-2);
            overflow: hidden;
            padding: var(--spacing-2) 0;
            min-width: 200px;
        }
        
        .dropdown-item {
            padding: var(--spacing-3) var(--spacing-4);
            color: var(--text-secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .dropdown-item i {
            margin-right: var(--spacing-3);
            font-size: 1rem;
            width: 20px;
            text-align: center;
            color: var(--text-tertiary);
            transition: var(--transition);
        }
        
        .dropdown-item:hover {
            background-color: rgba(var(--primary-color), 0.05);
            color: var(--primary-color);
        }
        
        .dropdown-item:hover i {
            color: var(--primary-color);
            transform: translateX(2px);
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color-dark), var(--primary-color), var(--primary-color-light));
            color: white;
            padding: 3.5rem 0 3rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            border-bottom-left-radius: var(--border-radius-lg);
            border-bottom-right-radius: var(--border-radius-lg);
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -10%;
            width: 120%;
            height: 200%;
            background: radial-gradient(ellipse at top right, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(-3deg);
            animation: pulse 8s infinite alternate ease-in-out;
        }
        
        .page-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.25;
        }
        
        @keyframes pulse {
            0% {
                opacity: 0.5;
            }
            100% {
                opacity: 0.8;
            }
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            position: relative;
            display: inline-flex;
            align-items: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: -0.01em;
        }
        
        .page-title i {
            font-size: 2.25rem;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.15));
            margin-right: var(--spacing-3);
            background: rgba(255, 255, 255, 0.2);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        
        .page-title:hover i {
            transform: scale(1.05) rotate(5deg);
            background: rgba(255, 255, 255, 0.25);
        }
        
        .page-subtitle {
            font-size: 1.125rem;
            opacity: 0.9;
            margin: 0;
            font-weight: 500;
            max-width: 650px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(3px);
            padding: var(--spacing-2) var(--spacing-4);
            border-radius: var(--border-radius);
            background: rgba(0, 0, 0, 0.1);
            display: inline-block;
            line-height: 1.5;
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2.25rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: var(--transition);
            height: 100%;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--accent-color));
            opacity: 0.7;
            transition: var(--transition);
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: rgba(var(--bs-primary-rgb), 0.3);
        }
        
        .stats-card:hover::before {
            width: 100%;
            opacity: 0.05;
        }
        
        .stats-number {
            font-size: 3.25rem;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            position: relative;
            z-index: 2;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.1);
        }
        
        .stats-label {
            color: var(--gray-700);
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: relative;
            z-index: 2;
            padding: 0.35rem 0.75rem;
            background: var(--gray-100);
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
            display: inline-block;
        }
        
        .stats-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.75rem;
            position: relative;
            transition: var(--transition);
        }
        
        .stats-card:hover .stats-icon {
            transform: scale(1.1);
        }
        
        .stats-card:nth-child(1) .stats-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: var(--primary-color);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25);
        }
        
        .stats-card:nth-child(2) .stats-icon {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: var(--success-color);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.2);
        }
        
        .stats-card:nth-child(3) .stats-icon {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: var(--warning-color);
            box-shadow: 0 8px 20px rgba(234, 179, 8, 0.2);
        }
        
        .stats-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(var(--bs-primary-rgb), 0.05) 0%, rgba(var(--bs-primary-rgb), 0) 70%);
            border-radius: 50%;
            z-index: -1;
            opacity: 0;
            transition: var(--transition);
        }
        
        .stats-card:hover::after {
            opacity: 1;
            transform: scale(1.2);
        }
        
        .stats-card:nth-child(4) .stats-icon {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            color: var(--info-color);
        }
        
        /* Modern Cards */
        .modern-card {
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
            background: white;
            height: 100%;
            position: relative;
        }
        
        .modern-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-3px);
            border-color: var(--primary-color);
        }
        
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: var(--transition);
        }
        
        .modern-card:hover::before {
            opacity: 1;
        }
        
        .modern-card .card-header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1.75rem;
            position: relative;
            z-index: 1;
        }
        
        .modern-card .card-body {
            padding: 1.75rem;
        }
        
        .modern-card .card-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, 
                rgba(var(--bs-primary-rgb), 0.5), 
                rgba(var(--bs-primary-rgb), 0.2),
                transparent 80%);
        }
        
        .card-title {
            font-weight: 700;
            color: var(--gray-800);
            font-size: 1.125rem;
        }
        
        .card-subtitle {
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        
        .header-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.25rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .header-icon.bg-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }
        
        .header-icon.bg-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }
        
        /* Table Styles */
        .table-modern {
            margin: 0 !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100% !important;
            background: white !important;
            border-radius: var(--border-radius) !important;
            overflow: hidden !important;
            box-shadow: var(--shadow-sm) !important;
        }
        
        .table-modern thead th {
            background: linear-gradient(135deg, var(--gray-50), var(--gray-100)) !important;
            border-bottom: 2px solid var(--gray-200) !important;
            color: var(--gray-700) !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 1.25rem 1.5rem !important;
            position: relative !important;
            white-space: nowrap !important;
        }
        
        .table-modern thead th:first-child {
            border-top-left-radius: 8px;
        }
        
        .table-modern thead th:last-child {
            border-top-right-radius: 8px;
        }
        
        .table-modern tbody tr {
            border-bottom: 1px solid var(--gray-100) !important;
            transition: var(--transition) !important;
            position: relative !important;
            background: white !important;
        }
        
        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.03), rgba(var(--bs-primary-rgb), 0.08)) !important;
            transform: translateY(-1px) !important;
            box-shadow: var(--shadow-md) !important;
            z-index: 2 !important;
            border-left: 3px solid var(--primary-color) !important;
        }
        
        .table-modern tbody tr:last-child {
            border-bottom: none;
        }
        
        .table-modern tbody tr.archived-row {
            background-color: rgba(var(--bs-secondary-rgb), 0.05);
        }
        
        .table-modern tbody tr.archived-row:hover {
            background-color: rgba(var(--bs-secondary-rgb), 0.1);
        }
        
        /* Row hover animation for highlighting current row */
        .table-modern tbody tr::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: var(--transition);
        }
        
        .table-modern tbody tr:hover::before {
            opacity: 1;
        }
        
        .table-modern tbody tr.fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .table-modern tbody td {
            padding: 1.5rem 1.5rem !important;
            vertical-align: middle !important;
            transition: var(--transition) !important;
            border-bottom: 1px solid var(--gray-100) !important;
        }
        
        .table-modern tbody td:first-child {
            border-left: 3px solid transparent;
            transition: var(--transition);
        }
        
        .table-modern tbody tr:hover td:first-child {
            border-left-color: var(--primary-color);
        }
        
        .client-table-header {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, var(--gray-50), white);
        }
        
        .client-table-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }
        
        .client-table-subtitle {
            color: var(--gray-600);
            margin-bottom: 0;
        }
        
        .client-tabs {
            background-color: white;
            border-bottom: none;
            padding: 0.5rem 1.5rem 0;
            margin-bottom: 0;
        }
        
        .client-tabs .nav-item {
            margin-right: 0.5rem;
        }
        
        .client-tabs .nav-link {
            padding: 1rem 1.5rem;
            color: var(--gray-700);
            font-weight: 600;
            border: none;
            position: relative;
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            background-color: rgba(var(--bs-primary-rgb), 0.05);
            transition: var(--transition);
            display: flex;
            align-items: center;
            z-index: 1;
        }
        
        .client-tabs .nav-link:hover {
            color: var(--primary-color);
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
        
        .client-tabs .nav-link.active {
            color: var(--primary-color-dark);
            background-color: white;
            border: 1px solid var(--gray-200);
            border-bottom-color: white;
            margin-bottom: -1px;
        }
        
        .client-tabs .nav-link.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .client-tabs .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
            margin-left: 0.5rem;
        }
        
        .client-avatar {
            width: 50px;
            height: 50px;
            border-radius: var(--border-radius-md);
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
            font-size: 1.375rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .client-avatar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(255, 255, 255, 0.1), 
                rgba(255, 255, 255, 0.05), 
                transparent 70%);
        }
        
        .client-avatar.archived {
            background: linear-gradient(135deg, var(--gray-500), var(--gray-600));
            opacity: 0.8;
        }
        
        .client-name {
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
            font-size: 1rem;
            letter-spacing: -0.01em;
            transition: var(--transition);
            line-height: 1.3;
        }
        
        tr:hover .client-name {
            color: var(--primary-color);
            transform: translateX(2px);
        }
        
        tr:hover .client-avatar {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
        }
        
        tr:hover .code-badge {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.12), rgba(var(--bs-primary-rgb), 0.18));
            border-color: rgba(var(--bs-primary-rgb), 0.25);
            transform: translateY(-1px);
        }
        
        tr:hover .badge-secondary {
            background: linear-gradient(135deg, var(--gray-300), var(--gray-400)) !important;
            transform: translateY(-1px);
        }
        
        .client-type {
            color: var(--gray-500);
            font-size: 0.8125rem;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .client-type::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--primary-color);
            margin-right: 0.5rem;
            opacity: 0.7;
        }
        
        .archived-row .client-type::before {
            background-color: var(--gray-500);
        }
        
        .code-badge {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.08), rgba(var(--bs-primary-rgb), 0.12));
            color: var(--primary-color-dark);
            padding: 0.5rem 0.875rem;
            border-radius: var(--border-radius);
            font-size: 0.8125rem;
            font-weight: 700;
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            letter-spacing: -0.025em;
            border: 1px solid rgba(var(--bs-primary-rgb), 0.15);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }
        
        .code-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(255, 255, 255, 0.1), 
                rgba(255, 255, 255, 0.05), 
                transparent 70%);
            opacity: 0;
            transition: var(--transition);
        }
        
        .code-badge:hover::before {
            opacity: 1;
        }
        
        tr:hover .code-badge {
            background: rgba(var(--bs-primary-rgb), 0.1);
            border-color: rgba(var(--bs-primary-rgb), 0.2);
        }
        
        .badge-secondary {
            background: linear-gradient(135deg, var(--gray-200), var(--gray-300)) !important;
            color: var(--gray-700);
            padding: 0.5rem 0.875rem;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 700;
            transition: var(--transition);
            border: 1px solid var(--gray-300);
            box-shadow: var(--shadow-sm);
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        tr:hover .badge-secondary {
            background: var(--gray-300) !important;
            color: var(--gray-800);
        }
        
        .date-text {
            color: var(--gray-600);
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            background: var(--gray-50);
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }
        
        tr:hover .date-text {
            background: var(--gray-100);
            border-color: var(--gray-300);
            color: var(--gray-700);
        }
        
        .date-text::before {
            content: '\f017'; /* fa-clock icon */
            font-family: 'Font Awesome 6 Free';
            font-weight: 400;
            margin-right: 0.375rem;
            font-size: 0.8125rem;
            opacity: 0.7;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.625rem;
            flex-wrap: nowrap;
            align-items: center;
        }
        
        .action-buttons .btn {
            border-radius: var(--border-radius);
            padding: 0.625rem;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            font-weight: 600;
            border-width: 2px;
        }
        
        .action-buttons .btn i {
            transition: transform 0.2s ease;
        }
        
        .action-buttons .btn:hover i {
            transform: scale(1.15);
        }
        
        .action-buttons .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: currentColor;
            border-radius: inherit;
            opacity: 0;
            transform: scale(0.8);
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        
        .action-buttons .btn:hover::before {
            opacity: 0.05;
            transform: scale(1);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(var(--bs-primary-rgb), 0.02);
            border-radius: var(--border-radius);
            animation: fadeIn 0.6s ease-out;
        }
        
        .empty-state-icon {
            width: 90px;
            height: 90px;
            border-radius: var(--border-radius);
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.05), rgba(var(--bs-primary-rgb), 0.1));
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
            font-size: 2.25rem;
            position: relative;
            box-shadow: var(--shadow);
        }
        
        .empty-state-icon::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: inherit;
            border: 2px dashed rgba(var(--bs-primary-rgb), 0.2);
            opacity: 0.8;
        }
        
        .empty-state-title {
            color: var(--gray-800);
            font-weight: 700;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }
        
        .empty-state-text {
            color: var(--gray-600);
            margin-bottom: 1.75rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.5;
            font-weight: 500;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Form Styles */
        .client-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .form-control {
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            background: white;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
            background: white;
        }
        
        .form-text {
            color: var(--gray-500);
            font-size: 0.8125rem;
            margin-top: 0.375rem;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.9375rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(30, 64, 175, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 64, 175, 0.3);
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        }
        
        .btn-light {
            background: white;
            border: 2px solid white;
            color: var(--primary-color);
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
        }
        
        .btn-light:hover {
            background: var(--gray-100);
            color: var(--primary-color);
            transform: translateY(-1px);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 2rem 0;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .modern-card .card-header,
            .modern-card .card-body {
                padding: 1rem;
            }
            
            .table-modern thead th,
            .table-modern tbody td {
                padding: 1rem;
            }
            
            .client-avatar {
                width: 40px;
                height: 40px;
                font-size: 1.125rem;
                margin-right: 0.75rem;
            }
            
            .action-buttons {
                gap: 0.375rem;
            }
            
            .action-buttons .btn {
                min-width: 2.25rem;
                height: 2.25rem;
                padding: 0.5rem;
            }
            
            .code-badge {
                font-size: 0.75rem;
                padding: 0.375rem 0.625rem;
            }
            
            .badge-secondary {
                font-size: 0.6875rem;
                padding: 0.375rem 0.625rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>SmartPrep
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.website-requests') }}">
                            <i class="fas fa-clock me-2"></i>Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('smartprep.admin.clients') }}">
                            <i class="fas fa-users me-2"></i>Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.settings') }}">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>View Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('smartprep.logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="page-title">
                        <i class="fas fa-building"></i>Client Management
                    </h1>
                    <p class="page-subtitle">Manage and monitor your multi-tenant client websites</p>
                </div>
                <div class="col-md-4 d-flex justify-content-md-end mt-4 mt-md-0">
                    <a href="{{ route('smartprep.admin.clients.create') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>New Client
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container pb-5">
        <style>
            .custom-alert {
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-md);
                border: none;
                padding: 1rem 1.25rem;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }
            
            .custom-alert::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                width: 4px;
            }
            
            .custom-alert-success {
                background-color: rgba(var(--bs-success-rgb), 0.1);
                color: var(--success-color);
            }
            
            .custom-alert-success::before {
                background: var(--success-color);
            }
            
            .custom-alert .btn-close {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: transparent;
                opacity: 0.7;
                transition: var(--transition);
            }
            
            .custom-alert .btn-close:hover {
                opacity: 1;
                transform: scale(1.1);
            }
            
            .custom-alert i {
                font-size: 1.25rem;
                margin-right: 0.75rem;
            }
        </style>
        
        @if(session('status'))
            <div class="custom-alert custom-alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <strong>Success!</strong> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number">{{ $clients->count() }}</div>
                    <div class="stats-label">Active Clients</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="stats-number">{{ $archivedClients->count() }}</div>
                    <div class="stats-label">Archived Clients</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stats-number">{{ $clients->count() + $archivedClients->count() }}</div>
                    <div class="stats-label">Total Websites</div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="card modern-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Client Websites</h5>
                                <p class="card-subtitle mb-0">Manage your multi-tenant platforms</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-tabs client-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                                    <i class="fas fa-users me-2"></i>Active Clients
                                    <span class="badge bg-primary ms-2">{{ $clients->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab">
                                    <i class="fas fa-archive me-2"></i>Archived Clients
                                    <span class="badge bg-secondary ms-2">{{ $archivedClients->count() }}</span>
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                        @if($clients->count() > 0)
                            <div class="table-responsive">
                                <div class="client-table-header">
                                    <h3 class="client-table-title"><i class="fas fa-building me-2"></i>Client Websites</h3>
                                    <p class="client-table-subtitle">Manage your multi-tenant platforms</p>
                                </div>
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-tag me-2 text-primary"></i>Name</th>
                                            <th><i class="fas fa-link me-2 text-info"></i>Slug</th>
                                            <th><i class="fas fa-database me-2 text-success"></i>Database</th>
                                            <th><i class="fas fa-calendar me-2 text-warning"></i>Created</th>
                                            <th><i class="fas fa-tools me-2 text-secondary"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clients as $client)
                                        <tr class="table-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="client-avatar">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <div>
                                                        <div class="client-name">{{ $client->name }}</div>
                                                        <small class="client-type">Multi-tenant website</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(isset($client->external_url))
                                                        <span class="badge badge-secondary">External</span>
                                                    @endif
                                                    <code class="code-badge">{{ $client->slug }}</code>
                                                </div>
                                            </td>
                                            <td><code class="code-badge">{{ $client->db_name ?? $client->database }}</code></td>
                                            <td>
                                                <span class="date-text">{{ optional($client->created_at)->format('M j, Y') ?? '—' }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group action-buttons" role="group">
                                                    <a href="{{ isset($client->external_url) ? $client->external_url : '/t/'.$client->slug }}" 
                                                       class="btn btn-outline-primary btn-sm" target="_blank" title="Visit Website">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    <a href="{{ isset($client->external_url) ? $client->external_url : '/t/'.$client->slug }}/admin/dashboard" 
                                                       class="btn btn-primary btn-sm" target="_blank" title="Admin Panel">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                    
                                                    @if(!isset($client->external_url))
                                                        <a href="{{ route('smartprep.admin.clients.edit', $client->id) }}" 
                                                          class="btn btn-outline-info btn-sm" title="Edit Client">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                                                title="Archive Client" 
                                                                onclick="confirmArchive('{{ $client->name }}', '{{ $client->id }}')">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                        

                                                        
                                                        <form id="archive-form-{{ $client->id }}" 
                                                              action="{{ route('smartprep.admin.clients.archive', $client->id) }}" 
                                                              method="POST" style="display: none;">
                                                            @csrf
                                                            @method('PATCH')
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h4 class="empty-state-title">No clients created yet</h4>
                                <p class="empty-state-text">Create your first client website to get started with the multi-tenant platform</p>
                                <button class="btn btn-primary" onclick="document.querySelector('input[name=name]').focus()">
                                    <i class="fas fa-plus me-2"></i>Create First Client
                                </button>
                            </div>
                        @endif
                            </div>
                            
                            <div class="tab-pane fade" id="archived" role="tabpanel" aria-labelledby="archived-tab">
                                @if($archivedClients->count() > 0)
                                <div class="table-responsive">
                                    <div class="client-table-header">
                                        <h3 class="client-table-title"><i class="fas fa-archive me-2"></i>Archived Clients</h3>
                                        <p class="client-table-subtitle">Previously archived client websites</p>
                                    </div>
                                    <table class="table table-modern">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-tag me-2 text-primary"></i>Name</th>
                                                <th><i class="fas fa-link me-2 text-info"></i>Slug</th>
                                                <th><i class="fas fa-database me-2 text-success"></i>Database</th>
                                                <th><i class="fas fa-calendar me-2 text-warning"></i>Created</th>
                                                <th><i class="fas fa-tools me-2 text-secondary"></i>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($archivedClients as $client)
                                            <tr class="table-row archived-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="client-avatar archived">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                        <div>
                                                            <div class="client-name">{{ $client->name }}</div>
                                                            <small class="client-type">Archived website</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge badge-secondary">Archived</span>
                                                        <code class="code-badge">{{ $client->slug }}</code>
                                                    </div>
                                                </td>
                                                <td><code class="code-badge">{{ $client->db_name ?? $client->database }}</code></td>
                                                <td>
                                                    <span class="date-text">{{ optional($client->created_at)->format('M j, Y') ?? '—' }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group action-buttons" role="group">
                                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                                title="Restore Client" 
                                                                onclick="confirmRestore('{{ $client->name }}', '{{ $client->id }}')">
                                                            <i class="fas fa-trash-restore"></i>
                                                        </button>
                                                        
                                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                title="Delete Permanently" 
                                                                onclick="confirmDelete('{{ $client->name }}', '{{ $client->id }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        
                                                        <form id="restore-form-{{ $client->id }}" 
                                                              action="{{ route('smartprep.admin.clients.unarchive', $client->id) }}" 
                                                              method="POST" style="display: none;">
                                                            @csrf
                                                            @method('PATCH')
                                                        </form>
                                                        
                                                        <form id="delete-form-{{ $client->id }}" 
                                                              action="{{ route('smartprep.admin.clients.destroy', $client->id) }}" 
                                                              method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-archive"></i>
                                    </div>
                                    <h4 class="empty-state-title">No archived clients</h4>
                                    <p class="empty-state-text">Any clients you archive will appear here</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-generate slug from name
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.querySelector('input[name="slug"]');
            const dbInput = document.querySelector('input[name="db"]');
            
            nameInput?.addEventListener('input', function() {
                if (!slugInput.value) {
                    const slug = this.value.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim('-');
                    slugInput.value = slug;
                }
                
                if (!dbInput.value) {
                    const dbName = this.value.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '_')
                        .replace(/_+/g, '_')
                        .trim('_') + '_db';
                    dbInput.value = dbName;
                }
            });
            
            // Add animation to stats cards
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in-up');
            });
            
            // Add animation to table rows
            const tableRows = document.querySelectorAll('.table-row');
            tableRows.forEach((row, index) => {
                row.style.animationDelay = `${(index + 4) * 0.05}s`;
                row.classList.add('fade-in-up');
            });
            
            // Enhanced form interactions
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                control.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Add loading states to action buttons
            document.querySelectorAll('.btn[type="submit"]').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.form && this.form.checkValidity()) {
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
                        this.disabled = true;
                    }
                });
            });
        });
        
        // Enhanced confirmation functions using Bootstrap modals
        function confirmArchive(clientName, clientId) {
            // Update the modal content
            const modal = document.getElementById('archiveModal');
            const clientNameEl = modal.querySelector('.client-name-placeholder');
            const confirmBtn = document.getElementById('confirmArchiveBtn');
            
            // Set client name in the modal
            clientNameEl.textContent = clientName;
            
            // Set up the confirm button action
            confirmBtn.onclick = function() {
                document.getElementById(`archive-form-${clientId}`).submit();
            };
            
            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
        
        function confirmDelete(clientName, clientId) {
            // Update the modal content
            const modal = document.getElementById('deleteModal');
            const clientNameEl = modal.querySelector('.client-name-placeholder');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            
            // Set client name in the modal
            clientNameEl.textContent = clientName;
            
            // Set up the confirm button action
            confirmBtn.onclick = function() {
                document.getElementById(`delete-form-${clientId}`).submit();
            };
            
            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
        
        function confirmRestore(clientName, clientId) {
            // Update the modal content
            const modal = document.getElementById('restoreModal');
            const clientNameEl = modal.querySelector('.client-name-placeholder');
            const confirmBtn = document.getElementById('confirmRestoreBtn');
            
            // Set client name in the modal
            clientNameEl.textContent = clientName;
            
            // Set up the confirm button action
            confirmBtn.onclick = function() {
                document.getElementById(`restore-form-${clientId}`).submit();
            };
            
            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    </script>
    
    <style>
        .modal-confirm .modal-header {
            border-bottom-width: 0;
            position: relative;
            padding: 1.75rem 1.75rem 1rem;
        }
        
        .modal-confirm .modal-content {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .modal-confirm .modal-title {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
        }
        
        .modal-confirm .modal-title i {
            font-size: 1.75rem;
        }
        
        .modal-confirm .modal-body {
            padding: 1.25rem 1.75rem 1.5rem;
        }
        
        .modal-confirm .modal-footer {
            border-top-width: 0;
            padding: 0.75rem 1.75rem 1.75rem;
            justify-content: center;
            gap: 1rem;
        }
        
        .modal-confirm .btn {
            padding: 0.65rem 1.5rem;
            font-weight: 500;
            border-radius: var(--border-radius);
            min-width: 120px;
            transition: var(--transition);
        }
        
        .modal-confirm .client-name-placeholder {
            font-weight: 700;
        }
        
        .modal-warning .modal-title {
            color: var(--warning-color);
        }
        
        .modal-danger .modal-title {
            color: var(--danger-color);
        }
        
        .modal-success .modal-title {
            color: var(--success-color);
        }
        
        .modal-warning .icon-circle, 
        .modal-danger .icon-circle,
        .modal-success .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .modal-warning .icon-circle {
            background-color: rgba(var(--bs-warning-rgb), 0.15);
            color: var(--warning-color);
        }
        
        .modal-danger .icon-circle {
            background-color: rgba(var(--bs-danger-rgb), 0.15);
            color: var(--danger-color);
        }
        
        .modal-success .icon-circle {
            background-color: rgba(var(--bs-success-rgb), 0.15);
            color: var(--success-color);
        }
        
        .modal-warning .icon-circle i, 
        .modal-danger .icon-circle i,
        .modal-success .icon-circle i {
            font-size: 1.5rem;
        }
    </style>
    
    <!-- Modal for archive confirmation -->
    <div class="modal fade modal-confirm modal-warning" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveModalLabel">
                        <i class="fas fa-archive"></i>
                        Archive Client
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="icon-circle mx-auto">
                        <i class="fas fa-archive"></i>
                    </div>
                    <p class="mb-1">Are you sure you want to archive <span class="client-name-placeholder fw-bold"></span>?</p>
                    <p class="text-muted">The client will be hidden from the main list but can be restored later.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmArchiveBtn">
                        <i class="fas fa-archive me-2"></i>Archive
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal for delete confirmation -->
    <div class="modal fade modal-confirm modal-danger" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-trash"></i>
                        Delete Client
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="icon-circle mx-auto">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="alert alert-danger d-inline-block">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p class="mb-0">Are you sure you want to permanently delete <span class="client-name-placeholder fw-bold"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i>Delete Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal for restore confirmation -->
    <div class="modal fade modal-confirm modal-success" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreModalLabel">
                        <i class="fas fa-trash-restore"></i>
                        Restore Client
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="icon-circle mx-auto">
                        <i class="fas fa-trash-restore"></i>
                    </div>
                    <p class="mb-1">Do you want to restore <span class="client-name-placeholder fw-bold"></span>?</p>
                    <p class="text-muted">The client will be moved back to the active clients list.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmRestoreBtn">
                        <i class="fas fa-trash-restore me-2"></i>Restore
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Force refresh CSS with timestamp
        console.log("Page loaded with enhanced styling - <?php echo time(); ?>");
        
        document.addEventListener('DOMContentLoaded', function() {
            // Check if styles are loaded
            console.log("DOM fully loaded - design should be visible now");
            
            // Add animation classes to elements
            document.querySelectorAll('.stats-card').forEach(function(card, index) {
                setTimeout(function() {
                    card.classList.add('fade-in-up');
                }, 100 * (index + 1));
            });
        });
    </script>
</body>
</html>