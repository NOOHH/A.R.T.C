

<?php $__env->startSection('title', 'Student Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<meta name="page-id" content="student-dashboard">
<link rel="stylesheet" href="<?php echo e(asset('css/student/student-dashboard.css')); ?>">
<style>
    <?php
        $courseSettings = \App\Helpers\UiSettingsHelper::getSection('student_portal');
        $dashboardSettings = \App\Helpers\UiSettingsHelper::getSection('dashboard');
    ?>
    
    :root {
        /* Course Card Variables */
        --course-card-bg: <?php echo e($courseSettings['course_card_bg_color'] ?? '#ffffff'); ?>;
        --progress-bar-bg: <?php echo e($courseSettings['progress_bg_color'] ?? '#e9ecef'); ?>;
        --progress-bar-fill: <?php echo e($courseSettings['progress_bar_color'] ?? '#667eea'); ?>;
        --progress-text-color: <?php echo e($courseSettings['progress_text_color'] ?? '#6c757d'); ?>;
        --resume-btn-bg: <?php echo e($courseSettings['resume_button_color'] ?? '#667eea'); ?>;
        --resume-btn-text: <?php echo e($courseSettings['resume_button_text_color'] ?? '#ffffff'); ?>;
        --resume-btn-hover: <?php echo e($courseSettings['resume_button_hover_color'] ?? '#5a67d8'); ?>;
        --premium-badge-bg: <?php echo e($courseSettings['premium_badge_bg'] ?? '#8e44ad'); ?>;
        --type-badge-bg: <?php echo e($courseSettings['type_badge_bg'] ?? '#e67e22'); ?>;
        --badge-text-color: <?php echo e($courseSettings['badge_text_color'] ?? '#ffffff'); ?>;
        --placeholder-color: <?php echo e($courseSettings['course_placeholder_color'] ?? '#ffffff'); ?>;
        --course-title-color: <?php echo e($courseSettings['course_title_color'] ?? '#333333'); ?>;
        --course-card-border-color: <?php echo e($courseSettings['course_card_border_color'] ?? '#dee2e6'); ?>;
        --course-title-font-size: <?php echo e($courseSettings['course_title_font_size'] ?? '1.25rem'); ?>;
        --course-title-font-weight: <?php echo e($courseSettings['course_title_font_weight'] ?? '600'); ?>;
        --course-card-border-radius: <?php echo e($courseSettings['course_card_border_radius'] ?? '15px'); ?>;
        --course-title-font-style: <?php echo e($courseSettings['course_title_font_style'] ?? 'normal'); ?>;
        
        /* Dashboard Variables */
        --dashboard-header-bg: <?php echo e($dashboardSettings['header_bg'] ?? '#0d6efd'); ?>;
        --dashboard-header-text: <?php echo e($dashboardSettings['header_text'] ?? '#ffffff'); ?>;
        --sidebar-bg: <?php echo e($dashboardSettings['sidebar_bg'] ?? '#f8f9fa'); ?>;
        --active-menu-item: <?php echo e($dashboardSettings['active_menu_item'] ?? '#0d6efd'); ?>;
        --course-interface-card-bg: <?php echo e($dashboardSettings['course_card_bg'] ?? '#ffffff'); ?>;
        --course-interface-progress: <?php echo e($dashboardSettings['course_progress_bar'] ?? '#28a745'); ?>;
        --course-interface-title: <?php echo e($dashboardSettings['course_title_color'] ?? '#212529'); ?>;
        --assignment-due-date: <?php echo e($dashboardSettings['assignment_due_date'] ?? '#dc3545'); ?>;
    }
    
    /* Enhanced course cards with modern design */
    .courses-card {
        background: var(--course-card-bg);
        border-radius: var(--course-card-border-radius);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid var(--course-card-border-color);
        backdrop-filter: blur(10px);
    }
    
    .card-header {
        background: var(--dashboard-header-bg);
        color: var(--dashboard-header-text);
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    
    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }
    
    .card-header h2 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .completion-badge {
        background: rgba(255,255,255,0.25);
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
    }
    
    .courses-list {
        padding: 25px;
    }
    
    .course-item {
        display: flex;
        background: var(--course-card-bg);
        border-radius: var(--course-card-border-radius);
        margin-bottom: 25px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid var(--course-card-border-color);
        position: relative;
    }
    
    .course-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .course-item:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }
    
    .course-item:hover::before {
        opacity: 1;
    }
    
    .course-thumbnail {
        width: 140px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .course-thumbnail::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
    }
    
    .course-placeholder {
        font-size: 3rem;
        opacity: 0.8;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .course-details {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .course-details h3 {
        margin: 0 0 12px 0;
        font-size: var(--course-title-font-size);
        font-weight: var(--course-title-font-weight);
        font-style: var(--course-title-font-style);
        color: var(--course-title-color);
        line-height: 1.3;
    }
    
    .course-details p {
        margin: 0 0 18px 0;
        color: #6c757d;
        font-size: 1rem;
        line-height: 1.6;
        flex-grow: 1;
    }
    
    .progress-bar {
        height: 12px;
        background: var(--progress-bar-bg);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        margin: 15px 0;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: var(--progress, 0%);
        background: linear-gradient(90deg, var(--progress-bar-fill), var(--progress-bar-fill));
        border-radius: 8px;
        transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }
    
    .progress-text {
        display: block;
        margin-top: 8px;
        font-size: 0.9rem;
        color: var(--progress-text-color);
        text-align: right;
        font-weight: 500;
    }
    
    .resume-btn {
        background: var(--resume-btn-bg);
        color: var(--resume-btn-text);
        border: none;
        padding: 12px 24px;
        margin: 15px;
        align-self: center;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        min-width: 120px;
        text-align: center;
    }
    
    .resume-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .resume-btn:hover::before {
        left: 100%;
    }
    
    .resume-btn:hover {
        background: var(--resume-btn-hover);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .resume-btn:active {
        transform: translateY(-1px) scale(1.02);
    }
    
    /* Enhanced Button states */
    .resume-btn.pending {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        cursor: not-allowed;
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
    }
    
    .resume-btn.payment-required {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        animation: pulse 2s infinite;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
    }
    
    .resume-btn.rejected {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        cursor: not-allowed;
        box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
    }
    
    .resume-btn.completed {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
    }
    
    @keyframes pulse {
        0% { 
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }
        50% { 
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.6);
        }
        100% { 
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }
    }
    
    /* Course enrollment info badges */
    .course-enrollment-info {
        margin: 10px 0;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .enrollment-badge, .plan-badge, .type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-weight: 500;
        line-height: 1;
    }
    
    .enrollment-badge {
        background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
        color: white;
    }
    
    .plan-badge {
        background: rgba(39, 174, 96, 0.1);
        color: #27ae60;
        border: 1px solid rgba(39, 174, 96, 0.3);
    }
    
    .type-badge {
        background: rgba(230, 126, 34, 0.1);
        color: #e67e22;
        border: 1px solid rgba(230, 126, 34, 0.3);
    }
    
    .no-courses {
        padding: 40px 20px;
        text-align: center;
        color: #7f8c8d;
    }
    
    .no-courses::before {
        content: '📚';
        display: block;
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    /* Certificate card styling */
    .certificate-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .certificate-card .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    
    .certificate-card .card-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .certificate-available .btn {
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }
    
    .certificate-available .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .certificate-pending {
        padding: 20px;
    }
    /*
        font-size: 3rem;
        margin-bottom: 10px;
        opacity: 0.5;
    }
    */
    /* Module icons */
    .course-thumbnail::before {
        content: '📚';
        font-size: 2.5rem;
    }
    
    /* Payment Modal Styles */
    .payment-method-card {
        border: 2px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        user-select: none;
        position: relative;
        z-index: 1;
    }
    
    .payment-method-card:hover {
        border-color: #0d6efd;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }
    
    .payment-method-card:active {
        transform: translateY(0px);
    }
    
    .payment-step {
        min-height: 300px;
    }
    
    .qr-code-container {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        display: inline-block;
    }
    
    .upload-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        
        border: 2px dashed #dee2e6;
    }
    
    .payment-instructions ol {
        background: #fff;
        border-radius: 8px;
        padding: 15px;
    }
    
    .payment-instructions li {
        margin-bottom: 8px;
        line-height: 1.5;
    }
    
    /* Enhanced modal and payment method styling */
    .modal {
        z-index: 1000001 !important;
        position: fixed;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
        background-color: rgba(0, 0, 0, 0.5) !important;
        
        
    }
    

    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-clip: padding-box;
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        outline: 0;
    
    }
    


    .modal-header {
        background: rgb(6, 23, 97);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 20px 25px;
        border-bottom: none;
    }
    
    .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
    }
    
    .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }
    
    .btn-close:hover {
        opacity: 1;
    }
    
    .payment-method-card {
        position: relative;
        z-index: 10;
        pointer-events: auto;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        user-select: none;
    }
    
    .payment-method-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        border-color: #667eea;
    }
    
    .payment-method-card.selected {
        border-color: #667eea;
        background: linear-gradient(145deg, #f8f9ff 0%, #ffffff 100%);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    }
    
    .payment-method-card .card-body {
        padding: 0;
    }
    
    .payment-method-card h5 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .payment-method-card p {
        color: #6c757d;
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    
    /* Ultra Compact Announcement Design */
    .announcement-content {
        padding: 0;
    }
    
    .announcement-item-compact {
        padding: 8px 12px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    
    .announcement-item-compact:last-child {
        border-bottom: none;
    }
    
    .announcement-item-compact:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .announcement-item-compact::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .announcement-item-compact:hover::before {
        opacity: 1;
    }
    
    .announcement-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
        gap: 8px;
    }
    
    .announcement-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.85rem;
        line-height: 1.2;
        flex: 1;
        margin: 0;
    }
    
    .announcement-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    
    .announcement-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 0.7rem;
        color: white;
        transition: all 0.3s ease;
    }
    
    .video-badge {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }
    
    .announcement-time {
        font-size: 0.7rem;
        color: #7f8c8d;
        white-space: nowrap;
    }
    
    .announcement-preview {
        color: #555;
        font-size: 0.75rem;
        line-height: 1.3;
        margin: 0;
        opacity: 0.8;
    }
    
    .empty-announcements {
        text-align: center;
        padding: 30px 20px;
        color: #7f8c8d;
    }
    
    .empty-announcements i {
        font-size: 1.5rem;
        margin-bottom: 8px;
        opacity: 0.5;
    }
    
    .empty-announcements p {
        margin: 0;
        font-size: 0.8rem;
    }
    
    /* Announcement Modal Styles */
    .announcement-modal-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #667eea;
    }
    
    .announcement-modal-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 10px 0;
        line-height: 1.3;
    }
    
    .announcement-modal-time {
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .announcement-modal-time::before {
        content: '🕒';
        font-size: 0.8rem;
    }
    
    .announcement-modal-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #34495e;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    /* Enhanced badges */
    .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    /* Dashboard grid improvements */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-top: 30px;
    }
    
    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .course-item {
            flex-direction: column;
        }
        
        .course-thumbnail {
            width: 100%;
            height: 120px;
        }
        
        /* Mobile announcement improvements */
        .announcement-item-compact {
            padding: 6px 10px;
        }
        
        .announcement-header {
            flex-direction: row;
            align-items: center;
            gap: 6px;
            margin-bottom: 3px;
        }
        
        .announcement-meta {
            align-self: center;
        }
        
        .announcement-title {
            font-size: 0.8rem;
        }
        
        .announcement-preview {
            font-size: 0.7rem;
        }
    }

    /* Enhanced deadlines and announcement cards (normalized) */
.deadlines-card,
.announcement-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
}


    /* Professional Deadline Cards Design */
    .deadlines-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
    }
    
    .deadlines-content {
        display: flex !important;
        flex-direction: column;
        gap: 8px;
        padding: 20px !important;
        padding-top: 32px !important;
        
        /* limit and enable scrolling */
        max-height: 450px;           /* increased height */
        overflow-y: auto;
        width: 100%;
        
        /* Add smooth scrolling */
        scroll-behavior: smooth;
        
        /* Better spacing for content */
        box-sizing: border-box;
    }
    .deadlines-content::-webkit-scrollbar {
        width: 6px;
    }
    .deadlines-content::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.03);
        border-radius: 6px;
        margin: 10px 0;
    }
    .deadlines-content::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, rgba(102,126,234,0.6), rgba(118,75,162,0.6));
        border-radius: 6px;
        transition: background 0.3s ease;
    }
    .deadlines-content::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, rgba(102,126,234,0.8), rgba(118,75,162,0.8));
    }

    @media (max-width: 768px) {
        .deadlines-content {
            max-height: 360px;
            padding: 15px !important;
            padding-top: 2rem !important;
        }
    }
    .deadline-item-modern {
        width: 100%;
        flex: 0 0 auto;
        min-width: 0; /* prevent flex-based overflow */
        margin-bottom: 12px !important;
        margin-top: 0 !important;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        
        /* Add proper spacing */
        padding: 0 !important;
        background: transparent;
        border-radius: 12px;
        
        /* Prevent content cutoff */
        overflow: visible;
    }
    
    /* Ensure first deadline item has proper top spacing */
    .deadlines-content .deadline-item-modern:first-child {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    .deadline-item-modern:last-child {
        border-bottom: none;
        margin-bottom: 8px !important; /* Add bottom margin to last item */
    }
    
    .deadline-item-modern:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
    }
    
    .deadline-item-modern::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .deadline-item-modern:hover::before {
        opacity: 1;
    }
    
    .deadline-item-modern .card {
        border: none !important;
        box-shadow: none !important;
        background: transparent;
        margin: 0;
        border-radius: 0;
    }
    
    .deadline-item-modern .card-body {
        padding: 18px 20px 16px 20px !important;
        
        /* Ensure proper spacing and prevent cutoff */
        min-height: auto;
        box-sizing: border-box;
        margin: 0 !important;
    }
    
    /* Compact deadline header */
    .deadline-item-modern .d-flex.align-items-start {
        margin-bottom: 14px !important;
    }
    
    .deadline-item-modern .card-title {
        font-size: 0.95rem !important;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 6px !important;
        line-height: 1.3;
        word-wrap: break-word;
    }
    
    .deadline-item-modern .card-text {
        font-size: 0.8rem;
        color: #7f8c8d;
        margin-bottom: 10px !important;
        line-height: 1.4;
        word-wrap: break-word;
    }
    
    /* Compact badges */
    .deadline-item-modern .badge {
        font-size: 0.7rem !important;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 500;
    }
    
    /* Compact icon circles */
    .deadline-item-modern .bg-primary.rounded-circle,
    .deadline-item-modern .bg-success.rounded-circle {
        width: 36px !important;
        height: 36px !important;
    }
    
    .deadline-item-modern .bg-primary.rounded-circle i,
    .deadline-item-modern .bg-success.rounded-circle i {
        font-size: 0.9rem !important;
    }
    
    /* Compact due date section */
    .deadline-item-modern .d-flex.align-items-center.justify-content-between:last-of-type {
        margin-bottom: 0 !important;
        padding-top: 4px;
    }
    
    .deadline-item-modern .fw-medium {
        font-size: 0.75rem;
        color: #7f8c8d;
    }
    
    /* Compact action indicator */
    .deadline-item-modern .border-top {
        border-top: 1px solid #f0f0f0 !important;
        padding-top: 10px !important;
        margin-top: 10px !important;
    }
    
    .deadline-item-modern .border-top .fw-medium {
        font-size: 0.75rem;
    }
    
    /* Status badge improvements */
    .deadline-item-modern .badge.bg-success,
    .deadline-item-modern .badge.bg-danger,
    .deadline-item-modern .badge.bg-warning {
        font-size: 0.65rem !important;
        padding: 4px 8px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    /* Feedback section compact */
    .deadline-item-modern .alert {
        padding: 10px 14px;
        margin-bottom: 10px !important;
        border-radius: 10px;
        border: none;
    }
    
    .deadline-item-modern .alert h6 {
        font-size: 0.8rem;
        margin-bottom: 6px !important;
        font-weight: 600;
    }
    
    .deadline-item-modern .alert p {
        font-size: 0.75rem;
        margin-bottom: 0;
        line-height: 1.4;
    }
    
    @media (max-width: 768px) {
        .deadline-item-modern .card-body {
            padding: 14px 18px 12px 18px !important;
        }
        
        .deadline-item-modern .card-title {
            font-size: 0.9rem !important;
        }
        
        .deadline-item-modern .card-text {
            font-size: 0.75rem !important;
        }
        
        .deadline-item-modern .bg-primary.rounded-circle,
        .deadline-item-modern .bg-success.rounded-circle {
            width: 32px !important;
            height: 32px !important;
        }
        
        .deadline-item-modern .bg-primary.rounded-circle i,
        .deadline-item-modern .bg-success.rounded-circle i {
            font-size: 0.8rem !important;
        }
        
        /* Ensure proper spacing on mobile */
        .deadline-item-modern {
            margin-bottom: 10px !important;
        }
        
        .deadline-item-modern:last-child {
            margin-bottom: 6px !important;
        }
    }
    
    /* Spinning animation for refresh button */
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Dashboard update notification styles */
    .dashboard-update-notification {
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }


 

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    <!-- Announcements Section -->
    <div class="dashboard-card announcement-card">
        <div class="card-header">
            <h2>Announcements</h2>
        </div>
        <div class="announcement-content">
            <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="announcement-item-compact" onclick="openAnnouncementModal('<?php echo e($announcement->id); ?>', '<?php echo e(addslashes($announcement->title)); ?>', '<?php echo e(addslashes($announcement->content)); ?>', '<?php echo e($announcement->announcement_type); ?>', '<?php echo e($announcement->created_at->diffForHumans()); ?>')" style="cursor: pointer;">
                    <div class="announcement-header">
                        <div class="announcement-title"><?php echo e($announcement->title); ?></div>
                        <div class="announcement-meta">
                            <?php if($announcement->announcement_type === 'video'): ?>
                                <span class="announcement-badge video-badge">
                                    <i class="bi bi-camera-video"></i>
                                </span>
                            <?php endif; ?>
                            <span class="announcement-time"><?php echo e($announcement->created_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                    <div class="announcement-preview"><?php echo e(Str::limit($announcement->content, 80)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-announcements">
                    <i class="bi bi-megaphone"></i>
                    <p>No announcements at the moment</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-grid">
    <!-- My Programs Section -->
    <div class="dashboard-card courses-card">
        <div class="card-header">
            <h2>My Programs</h2>
        </div>
        <div class="courses-list">
            <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="course-item" data-course-id="<?php echo e($course['id']); ?>" data-enrollment-id="<?php echo e($course['enrollment_id'] ?? ''); ?>">
                    <div class="course-thumbnail">
                        <div class="course-placeholder">📚</div>
                    </div>
                    <div class="course-details">
                        <h3><?php echo e($course['name']); ?></h3>
                        <p><?php echo e($course['description']); ?></p>
                        
                        <!-- Program Details -->
                        <div class="course-enrollment-info">
                            <span class="enrollment-badge"><?php echo e($course['package_name']); ?></span>
                            <?php if(isset($course['plan_name'])): ?>
                                <span class="plan-badge"><?php echo e($course['plan_name']); ?></span>
                            <?php endif; ?>
                            <?php if(isset($course['enrollment_type'])): ?>
                                <span class="type-badge"><?php echo e($course['enrollment_type']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Status Badge -->
                        <?php if($course['enrollment_status'] === 'rejected'): ?>
                            <span class="badge bg-danger" style="margin-top: 8px;">Rejected</span>
                            <?php if(isset($course['rejection_reason']) && $course['rejection_reason']): ?>
                                <span class="text-danger" style="display:block; margin-top:4px;">Reason: <?php echo e($course['rejection_reason']); ?></span>
                            <?php endif; ?>
                        <?php elseif($course['enrollment_status'] === 'pending'): ?>
                            <span class="badge bg-warning text-dark" style="margin-top: 8px;">Pending Admin Approval</span>
                        <?php endif; ?>
                        
                        <!-- Batch Information -->
                        <?php if(isset($course['batch_name']) && $course['batch_name']): ?>
                        <div class="batch-info" style="margin-top: 10px; padding: 8px 12px; background: #e8f5e8; border-radius: 6px; font-size: 0.9rem;">
                            <div style="font-weight: 600; color: #27ae60; margin-bottom: 4px;">
                                <i class="fas fa-users"></i> <?php echo e($course['batch_name']); ?>

                            </div>
                            <?php if(isset($course['batch_dates']) && $course['batch_dates']): ?>
                            <div style="color: #2c3e50; font-size: 0.85rem;">
                                <i class="fas fa-calendar-alt"></i> 
                                Start: <?php echo e($course['batch_dates']['start']); ?>

                                <?php if($course['batch_dates']['end'] !== 'TBA'): ?>
                                    | End: <?php echo e($course['batch_dates']['end']); ?>

                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="progress-bar" style="--progress: <?php echo e($course['progress']); ?>%">
                            <span class="progress-text"><?php echo e($course['progress']); ?>% complete</span>
                        </div>
                        <div class="course-meta" style="margin-top: 10px; font-size: 0.9rem; color: #7f8c8d;">
                            <span><?php echo e($course['completed_modules'] ?? 0); ?> / <?php echo e($course['total_modules'] ?? 0); ?> modules complete</span>
                        </div>
                    </div>
                    <?php if($course['enrollment_status'] === 'rejected'): ?>
                        <button class="<?php echo e($course['button_class']); ?>" onclick="showRejectedModal('<?php echo e($course['name']); ?>', <?php echo e($course['registration_id'] ?? $course['enrollment_id'] ?? 'null'); ?>)">
                            <?php echo e($course['button_text']); ?>

                        </button>
                    <?php elseif($course['enrollment_status'] === 'resubmitted'): ?>
                        <button class="<?php echo e($course['button_class']); ?>" onclick="showStatusModal('<?php echo e($course['enrollment_status']); ?>', '<?php echo e($course['name']); ?>', <?php echo e($course['registration_id'] ?? $course['enrollment_id'] ?? 'null'); ?>)" disabled>
                            <?php echo e($course['button_text']); ?>

                        </button>
                    <?php elseif($course['button_action'] === '#'): ?>
                        <button class="<?php echo e($course['button_class']); ?>" onclick="showStatusModal('<?php echo e($course['enrollment_status']); ?>', '<?php echo e($course['name']); ?>', <?php echo e($course['enrollment_id'] ?? 'null'); ?>)" disabled>
                            <?php echo e($course['button_text']); ?>

                        </button>
                    <?php elseif($course['payment_status'] !== 'paid' && $course['enrollment_status'] === 'approved'): ?>
                        <button class="<?php echo e($course['button_class']); ?>" onclick="showPaymentModal(<?php echo e($course['enrollment_id'] ?? 'null'); ?>, '<?php echo e($course['name']); ?>')">
                            <?php echo e($course['button_text']); ?>

                        </button>
                    <?php else: ?>
                        <a href="<?php echo e($course['button_action']); ?>" class="<?php echo e($course['button_class']); ?>">
                            <?php echo e($course['button_text']); ?>

                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="no-courses">
                    <p>You are not enrolled in any programs yet.</p>
                    <p>Please contact your administrator to get enrolled in courses.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Deadlines Section -->
    <div class="dashboard-card deadlines-card">
        <div class="card-header">
            <h2><i class="bi bi-calendar-check me-2"></i>Deadlines</h2>
        </div>
        <div class="deadlines-content">
            <?php $__empty_1 = true; $__currentLoopData = $deadlines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deadline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
             <div class="card mb-3 deadline-item-modern shadow-sm border-0" 
                 onclick="redirectToAssignment('<?php echo e($deadline->reference_id ?? ''); ?>', '<?php echo e($deadline->module_id ?? ''); ?>', '<?php echo e($deadline->type ?? 'assignment'); ?>', '<?php echo e($deadline->program_id ?? ''); ?>')"
                     style="cursor: pointer; transition: all 0.3s ease;">
                    <div class="card-body">
                        <!-- Header with Icon, Title and Status -->
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <?php if(($deadline->type ?? 'assignment') === 'assignment'): ?>
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-file-earmark-text text-white fs-5"></i>
                                        </div>
                                    <?php elseif(($deadline->type ?? '') === 'quiz'): ?>
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-question-circle text-white fs-5"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1 fw-bold"><?php echo e($deadline->title ?? 'Upcoming Task'); ?></h5>
                                    <p class="card-text text-muted mb-2"><?php echo e($deadline->description ?? ''); ?></p>
                                    
                                    <!-- Program/Course Information -->
                                    <?php if(!empty($deadline->course_name) || !empty($deadline->program_name)): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-light text-dark border me-2">
                                                <i class="bi bi-folder2-open me-1"></i>
                                                <?php echo e($deadline->course_name ?? $deadline->program_name ?? 'Course'); ?>

                                            </span>
                                            <?php if(!empty($deadline->module_name)): ?>
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-collection me-1"></i>
                                                    <?php echo e($deadline->module_name); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <?php if($deadline->status === 'completed'): ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle-fill me-1"></i>Completed
                                    </span>
                                <?php elseif($deadline->status === 'overdue'): ?>
                                    <span class="badge bg-danger fs-6">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Overdue
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="bi bi-clock-fill me-1"></i>Pending
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Due Date and Type -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-calendar3 me-2"></i>
                                <span class="fw-medium">Due: <?php echo e(\Carbon\Carbon::parse($deadline->due_date)->format('M d, Y g:i A')); ?></span>
                            </div>
                            <span class="badge <?php echo e(($deadline->type ?? 'assignment') === 'assignment' ? 'bg-primary' : 'bg-success'); ?> rounded-pill">
                                <?php echo e(ucfirst($deadline->type ?? 'assignment')); ?>

                            </span>
                        </div>
                        
                        <!-- Feedback Section (if completed with feedback) -->
                        <?php if($deadline->status === 'completed' && (!empty($deadline->feedback) || !empty($deadline->grade))): ?>
                            <div class="alert alert-light border-start border-4 border-success mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-success">
                                        <i class="bi bi-chat-text-fill me-2"></i>Instructor Feedback
                                    </h6>
                                    <?php if(!empty($deadline->grade)): ?>
                                        <span class="badge bg-success">Grade: <?php echo e($deadline->grade); ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <?php if(!empty($deadline->feedback)): ?>
                                    <p class="mb-0 small"><?php echo e($deadline->feedback); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Action Indicator -->
                        <div class="border-top pt-3">
                            <?php if($deadline->status === 'pending'): ?>
                                <div class="d-flex align-items-center text-primary">
                                    <i class="bi bi-arrow-right-circle me-2"></i>
                                    <span class="fw-medium">Click to <?php echo e($deadline->type === 'assignment' ? 'submit assignment' : 'take quiz'); ?></span>
                                </div>
                            <?php elseif($deadline->status === 'completed'): ?>
                                <div class="d-flex align-items-center text-success">
                                    <i class="bi bi-eye me-2"></i>
                                    <span class="fw-medium">Click to view details</span>
                                </div>
                            <?php elseif($deadline->status === 'overdue'): ?>
                                <div class="d-flex align-items-center text-danger">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    <span class="fw-medium">Click to submit (overdue)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted">No upcoming deadlines</h4>
                    <p class="text-muted mb-0">You're all caught up! Check back later for new assignments.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- My Meetings Section -->
    <div class="dashboard-card meetings-card">
        <div class="card-header">
            <h2>My Meetings</h2>
        </div>
        <div class="meetings-content" style="padding: 20px;">
            <div id="current-meetings-section" style="display: none;">
                <div style="background: #ffe6e6; border: 1px solid #ffcccc; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                    <h6 style="color: #d63384; margin: 0 0 10px 0; font-weight: 600;">
                        <i class="bi bi-broadcast" style="margin-right: 8px;"></i>Live Now
                    </h6>
                    <div id="current-meetings-list"></div>
                </div>
            </div>
            
            <div id="upcoming-meetings-section">
                <h6 style="color: #6c757d; margin: 0 0 10px 0; font-weight: 600;">
                    <i class="bi bi-calendar-week" style="margin-right: 8px;"></i>Upcoming Meetings
                </h6>
                <div id="upcoming-meetings-list" style="min-height: 60px;">
                    <div class="loading-spinner" style="text-align: center; padding: 20px;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span style="margin-left: 10px; color: #6c757d;">Loading meetings...</span>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="<?php echo e(route('student.meetings')); ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i>See All Meetings
                </a>
            </div>
        </div>
    </div>




</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalTitle" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalTitle">Enrollment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statusModalBody">
                <!-- Content will be filled by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Complete Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentStep1" class="payment-step">
                    <div class="text-center mb-4">
                        <i class="bi bi-credit-card-2-front" style="font-size: 3rem; color: #0d6efd;"></i>
                        <h4 class="mt-3">Choose Payment Method</h4>
                        <p class="text-muted">Select your preferred payment method to proceed</p>
                    </div>
                    
                    <div class="row" id="paymentMethodsContainer">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading payment methods...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading payment methods...</p>
                        </div>
                    </div>
                    
                    <div class="payment-details mt-4" id="paymentDetails" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Payment Details</h6>
                            <div id="enrollmentInfo">
                                <!-- Enrollment details will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="paymentStep2" class="payment-step" style="display: none;">
                    <div id="qrCodeSection" class="text-center mb-4">
                        <h5 id="paymentMethodTitle">Pay with GCash</h5>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Payment Amount: ₱<span id="paymentAmount">0.00</span></strong>
                        </div>
                        
                        <div class="qr-code-container mb-4">
                            <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 250px; border: 2px solid #ddd; border-radius: 8px;">
                        </div>
                        
                        <div class="payment-instructions">
                            <h6>Payment Instructions:</h6>
                            <ol class="text-start">
                                <li>Scan the QR code using your <span id="paymentMethodName">GCash</span> app</li>
                                <li>Enter the exact amount: ₱<span id="paymentAmountInstruction">0.00</span></li>
                                <li>Complete the payment transaction</li>
                                <li>Take a screenshot of the payment confirmation</li>
                                <li>Upload the screenshot below for verification</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="upload-section">
                        <div class="mb-3">
                            <label for="paymentProof" class="form-label">
                                <i class="bi bi-cloud-upload me-2"></i>Upload Payment Screenshot *
                            </label>
                            <input type="file" class="form-control" id="paymentProof" accept="image/*" required>
                            <div class="form-text">Supported formats: JPG, PNG (Max 5MB)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="referenceNumber" class="form-label">
                                <i class="bi bi-hash me-2"></i>Reference Number (Optional)
                            </label>
                            <input type="text" class="form-control" id="referenceNumber" placeholder="Enter transaction reference number">
                            <div class="form-text">Any reference number from your payment confirmation</div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="goToStep1()">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </button>
                        <button type="button" class="btn btn-primary flex-fill" id="submitPaymentBtn" onclick="submitPayment()">
                            <i class="bi bi-cloud-upload me-2"></i>Submit Payment Proof
                        </button>
                    </div>
                </div>
                
                <div id="paymentStep3" class="payment-step" style="display: none;">
                    <div class="text-center">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-success">Payment Proof Submitted!</h4>
                        <p class="text-muted">Your payment proof has been uploaded successfully. We will verify your payment within 24-48 hours and notify you via email.</p>
                        
                        <div class="alert alert-info mt-4">
                            <i class="bi bi-clock me-2"></i>
                            <strong>Next Steps:</strong>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li>• Admin will verify your payment</li>
                                <li>• You'll receive email confirmation</li>
                                <li>• Course access will be granted upon approval</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejected Registration Modal -->
<div class="modal fade" id="rejectedModal" tabindex="-1" aria-labelledby="rejectedModalTitle" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectedModalTitle">Registration Rejected</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="rejectedModalBody">
                <!-- Content will be filled by JavaScript -->
                <div class="text-center">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading rejection details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editRegistrationBtn" onclick="editRejectedRegistration()" style="display: none;">
                    <i class="bi bi-pencil-square me-2"></i>Edit & Resubmit
                </button>
                <button type="button" class="btn btn-danger" id="deleteRegistrationBtn" onclick="deleteRejectedRegistration()" style="display: none;">
                    <i class="bi bi-trash me-2"></i>Delete Registration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Registration Modal -->
<div class="modal fade" id="editRegistrationModal" tabindex="-1" aria-labelledby="editRegistrationModalTitle" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRegistrationModalTitle">Edit Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editRegistrationModalBody">
                <!-- Dynamic edit form will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="resubmitBtn" onclick="resubmitRegistration()">
                    <i class="bi bi-check2-circle me-2"></i>Resubmit Registration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Inline scripts moved to student-dashboard.page.js for modular architecture -->

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/student/student-dashboard.page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h5 class="modal-title" id="announcementModalLabel">Announcement</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h3 class="announcement-modal-title" id="announcementModalTitle"></h3>
                <div class="announcement-modal-time" id="announcementModalTime"></div>
                <div class="announcement-modal-text" id="announcementModalContent"></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.student-dashboard.student-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/student/student-dashboard/student-dashboard.blade.php ENDPATH**/ ?>