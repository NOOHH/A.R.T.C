@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Student Dashboard')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/student/student-dashboard.css') }}">
<style>
    /* Enhanced course cards with modern design */
    .courses-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
    }
    
    .card-header {
        
        color: white;
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
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        margin-bottom: 25px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
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
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
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
        background: #e9ecef;
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
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 8px;
        transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }
    
    .progress-text {
        display: block;
        margin-top: 8px;
        font-size: 0.9rem;
        color: #6c757d;
        text-align: right;
        font-weight: 500;
    }
    
    .resume-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
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
        border-left: 4px solid #0d6efd;
    }
    
    .payment-instructions li {
        margin-bottom: 8px;
        line-height: 1.5;
    }
    
    /* Enhanced modal and payment method styling */
    .modal {
        z-index: 1055 !important;
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
    
    .modal-backdrop {
        z-index: 1040 !important;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw !important;
        height: 100vh !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
    }
    
    /* Prevent backdrop persistence issues */
    body.modal-open {
        padding-right: 0 !important;
        overflow: hidden !important;
    }
    
    /* Ensure modal content is above backdrop */
    .modal-dialog {
        z-index: 1060 !important;
    }
    
    .modal.show {
        display: block !important;
    }
    
    .modal-dialog {
        position: relative;
        width: auto;
        margin: 1.75rem;
        pointer-events: none;
    }
    
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-color: #fff;
        background-clip: padding-box;
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        outline: 0;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    
    /* Enhanced deadlines and announcements */
    .deadlines-card, .announcements-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .deadline-item, .announcement-item {
        transition: background-color 0.3s ease;
        cursor: pointer;
    }
    
    .deadline-item:hover, .announcement-item:hover {
        background-color: rgba(102, 126, 234, 0.05);
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
    .announcement-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .announcement-modal.show {
        display: flex;
        opacity: 1;
    }
    
    .announcement-modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
    }
    
    .announcement-modal-content {
        position: relative;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow: hidden;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }
    
    .announcement-modal.show .announcement-modal-content {
        transform: scale(1);
    }
    
    .announcement-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .announcement-modal-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .announcement-modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }
    
    .announcement-modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }
    
    .announcement-modal-body {
        padding: 25px;
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
    
    @media (max-width: 768px) {
        .announcement-modal-content {
            width: 95%;
            max-height: 85vh;
        }
        
        .announcement-modal-header {
            padding: 15px 20px;
        }
        
        .announcement-modal-body {
            padding: 20px;
        }
        
        .announcement-modal-title {
            font-size: 1.2rem;
        }
        
        .announcement-modal-text {
            font-size: 0.95rem;
        }
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
        padding: 0 !important;
    }
    
    .deadline-item-modern {
        margin-bottom: 0 !important;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    
    .deadline-item-modern:last-child {
        border-bottom: none;
    }
    
    .deadline-item-modern:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
        padding: 16px 20px;
    }
    
    /* Compact deadline header */
    .deadline-item-modern .d-flex.align-items-start {
        margin-bottom: 12px !important;
    }
    
    .deadline-item-modern .card-title {
        font-size: 0.95rem !important;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px !important;
        line-height: 1.3;
    }
    
    .deadline-item-modern .card-text {
        font-size: 0.8rem;
        color: #7f8c8d;
        margin-bottom: 8px !important;
        line-height: 1.4;
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
    }
    
    .deadline-item-modern .fw-medium {
        font-size: 0.75rem;
        color: #7f8c8d;
    }
    
    /* Compact action indicator */
    .deadline-item-modern .border-top {
        border-top: 1px solid #f0f0f0 !important;
        padding-top: 8px !important;
        margin-top: 8px;
    }
    
    .deadline-item-modern .border-top .fw-medium {
        font-size: 0.75rem;
    }
    
    /* Status badge improvements */
    .deadline-item-modern .badge.bg-success,
    .deadline-item-modern .badge.bg-danger,
    .deadline-item-modern .badge.bg-warning {
        font-size: 0.65rem !important;
        padding: 3px 6px;
    }
    
    /* Feedback section compact */
    .deadline-item-modern .alert {
        padding: 8px 12px;
        margin-bottom: 8px !important;
        border-radius: 8px;
    }
    
    .deadline-item-modern .alert h6 {
        font-size: 0.8rem;
        margin-bottom: 4px !important;
    }
    
    .deadline-item-modern .alert p {
        font-size: 0.75rem;
        margin-bottom: 0;
    }
    
    @media (max-width: 768px) {
        .deadline-item-modern .card-body {
            padding: 12px 16px;
        }
        
        .deadline-item-modern .card-title {
            font-size: 0.9rem !important;
        }
        
        .deadline-item-modern .card-text {
            font-size: 0.75rem;
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
    
    /* Program Modal specific styles for dashboard */
    #programModal {
        z-index: 9999 !important;
    }
    
    #programModal .modal-dialog {
        z-index: 10000 !important;
    }
    
    #programModal .modal-content {
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border: none;
        z-index: 10001 !important;
    }
    
    #programModal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 20px 25px;
        border-bottom: none;
    }
    
    #programModal .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
    }
    
    #programModal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }
    
    #programModal .btn-close:hover {
        opacity: 1;
    }
    
    /* Ensure program modal displays above dashboard elements */
    #programModal.modal.show {
        display: block !important;
        z-index: 9999 !important;
    }
    
    #programModal .modal-backdrop {
        z-index: 9998 !important;
    }
    
    /* Override any competing z-index from layout */
    .modal-backdrop.show {
        z-index: 9998 !important;
    }
    
    .modal.fade.show {
        z-index: 9999 !important;
    }
</style>
@endpush

@section('content')

    <!-- Announcements Section -->
    <div class="dashboard-card announcement-card">
        <div class="card-header">
            <h2>Announcements</h2>
        </div>
        <div class="announcement-content">
            @forelse($announcements as $announcement)
                <div class="announcement-item-compact" onclick="openAnnouncementModal('{{ $announcement->id }}', '{{ addslashes($announcement->title) }}', '{{ addslashes($announcement->content) }}', '{{ $announcement->announcement_type }}', '{{ $announcement->created_at->diffForHumans() }}')" style="cursor: pointer;">
                    <div class="announcement-header">
                        <div class="announcement-title">{{ $announcement->title }}</div>
                        <div class="announcement-meta">
                            @if($announcement->announcement_type === 'video')
                                <span class="announcement-badge video-badge">
                                    <i class="bi bi-camera-video"></i>
                                </span>
                            @endif
                            <span class="announcement-time">{{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="announcement-preview">{{ Str::limit($announcement->content, 80) }}</div>
                </div>
            @empty
                <div class="empty-announcements">
                    <i class="bi bi-megaphone"></i>
                    <p>No announcements at the moment</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="dashboard-grid">
    <!-- My Programs Section -->
    <div class="dashboard-card courses-card">
        <div class="card-header">
            <h2>My Programs</h2>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="completion-badge">{{ count($courses) > 0 ? floor(array_sum(array_column($courses, 'progress')) / count($courses)) : '0' }}% overall progress</span>
                <button onclick="refreshDashboard()" class="btn btn-sm btn-outline-light" style="border-radius: 20px; padding: 4px 12px; font-size: 0.8rem;">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
                <button onclick="testDashboardUpdate()" class="btn btn-sm btn-outline-warning" style="border-radius: 20px; padding: 4px 12px; font-size: 0.8rem;">
                    <i class="bi bi-bug"></i> Test
                </button>
                <button onclick="testProgramModal()" class="btn btn-sm btn-outline-info" style="border-radius: 20px; padding: 4px 12px; font-size: 0.8rem;">
                    <i class="bi bi-collection"></i> Test Program Profile
                </button>
            </div>
        </div>
        <div class="courses-list">
            @forelse($courses as $course)
                <div class="course-item" data-course-id="{{ $course['id'] }}" data-enrollment-id="{{ $course['enrollment_id'] ?? '' }}">
                    <div class="course-thumbnail">
                        <div class="course-placeholder">📚</div>
                    </div>
                    <div class="course-details">
                        <h3>{{ $course['name'] }}</h3>
                        <p>{{ $course['description'] }}</p>
                        
                        <!-- Program Details -->
                        <div class="course-enrollment-info">
                            <span class="enrollment-badge">{{ $course['package_name'] }}</span>
                            @if(isset($course['plan_name']))
                                <span class="plan-badge">{{ $course['plan_name'] }}</span>
                            @endif
                            @if(isset($course['enrollment_type']))
                                <span class="type-badge">{{ $course['enrollment_type'] }}</span>
                            @endif
                        </div>
                        
                        <!-- Status Badge -->
                        @if($course['enrollment_status'] === 'rejected')
                            <span class="badge bg-danger" style="margin-top: 8px;">Rejected</span>
                            @if(isset($course['rejection_reason']) && $course['rejection_reason'])
                                <span class="text-danger" style="display:block; margin-top:4px;">Reason: {{ $course['rejection_reason'] }}</span>
                            @endif
                        @elseif($course['enrollment_status'] === 'pending')
                            <span class="badge bg-warning text-dark" style="margin-top: 8px;">Pending Admin Approval</span>
                        @endif
                        
                        <!-- Batch Information -->
                        @if(isset($course['batch_name']) && $course['batch_name'])
                        <div class="batch-info" style="margin-top: 10px; padding: 8px 12px; background: #e8f5e8; border-radius: 6px; font-size: 0.9rem;">
                            <div style="font-weight: 600; color: #27ae60; margin-bottom: 4px;">
                                <i class="fas fa-users"></i> {{ $course['batch_name'] }}
                            </div>
                            @if(isset($course['batch_dates']) && $course['batch_dates'])
                            <div style="color: #2c3e50; font-size: 0.85rem;">
                                <i class="fas fa-calendar-alt"></i> 
                                Start: {{ $course['batch_dates']['start'] }}
                                @if($course['batch_dates']['end'] !== 'TBA')
                                    | End: {{ $course['batch_dates']['end'] }}
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <div class="progress-bar" style="--progress: {{ $course['progress'] }}%">
                            <span class="progress-text">{{ $course['progress'] }}% complete</span>
                        </div>
                        <div class="course-meta" style="margin-top: 10px; font-size: 0.9rem; color: #7f8c8d;">
                            <span>{{ $course['completed_modules'] ?? 0 }} / {{ $course['total_modules'] ?? 0 }} modules complete</span>
                        </div>
                    </div>
                    @if($course['enrollment_status'] === 'rejected')
                        <button class="{{ $course['button_class'] }}" onclick="showRejectedModal('{{ $course['name'] }}', {{ $course['registration_id'] ?? $course['enrollment_id'] ?? 'null' }})">
                            {{ $course['button_text'] }}
                        </button>
                    @elseif($course['enrollment_status'] === 'resubmitted')
                        <button class="{{ $course['button_class'] }}" onclick="showStatusModal('{{ $course['enrollment_status'] }}', '{{ $course['name'] }}', {{ $course['registration_id'] ?? $course['enrollment_id'] ?? 'null' }})" disabled>
                            {{ $course['button_text'] }}
                        </button>
                    @elseif($course['button_action'] === '#')
                        <button class="{{ $course['button_class'] }}" onclick="showStatusModal('{{ $course['enrollment_status'] }}', '{{ $course['name'] }}', {{ $course['enrollment_id'] ?? 'null' }})" disabled>
                            {{ $course['button_text'] }}
                        </button>
                    @elseif($course['payment_status'] !== 'paid' && $course['enrollment_status'] === 'approved')
                        <button class="{{ $course['button_class'] }}" onclick="showPaymentModal({{ $course['enrollment_id'] ?? 'null' }}, '{{ $course['name'] }}')">
                            {{ $course['button_text'] }}
                        </button>
                    @else
                        <a href="{{ $course['button_action'] }}" class="{{ $course['button_class'] }}">
                            {{ $course['button_text'] }}
                        </a>
                    @endif
                </div>
            @empty
                <div class="no-courses">
                    <p>You are not enrolled in any programs yet.</p>
                    <p>Please contact your administrator to get enrolled in courses.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Deadlines Section -->
    <div class="dashboard-card deadlines-card">
        <div class="card-header">
            <h2><i class="bi bi-calendar-check me-2"></i>Deadlines</h2>
        </div>
        <div class="deadlines-content p-3">
            @forelse($deadlines as $deadline)
                <div class="card mb-3 deadline-item-modern shadow-sm border-0" 
                     onclick="redirectToAssignment('{{ $deadline->reference_id }}', '{{ $deadline->module_id }}', '{{ $deadline->type }}', '{{ $deadline->program_id ?? '' }}')"
                     style="cursor: pointer; transition: all 0.3s ease;">
                    <div class="card-body">
                        <!-- Header with Icon, Title and Status -->
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($deadline->type === 'assignment')
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-file-earmark-text text-white fs-5"></i>
                                        </div>
                                    @elseif($deadline->type === 'quiz')
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-question-circle text-white fs-5"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1 fw-bold">{{ $deadline->title }}</h5>
                                    <p class="card-text text-muted mb-2">{{ $deadline->description }}</p>
                                    
                                    <!-- Program/Course Information -->
                                    @if(!empty($deadline->course_name) || !empty($deadline->program_name))
                                        <div class="mb-2">
                                            <span class="badge bg-light text-dark border me-2">
                                                <i class="bi bi-folder2-open me-1"></i>
                                                {{ $deadline->course_name ?? $deadline->program_name ?? 'Course' }}
                                            </span>
                                            @if(!empty($deadline->module_name))
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-collection me-1"></i>
                                                    {{ $deadline->module_name }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                @if($deadline->status === 'completed')
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle-fill me-1"></i>Completed
                                    </span>
                                @elseif($deadline->status === 'overdue')
                                    <span class="badge bg-danger fs-6">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Overdue
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="bi bi-clock-fill me-1"></i>Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Due Date and Type -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-calendar3 me-2"></i>
                                <span class="fw-medium">Due: {{ \Carbon\Carbon::parse($deadline->due_date)->format('M d, Y g:i A') }}</span>
                            </div>
                            <span class="badge {{ $deadline->type === 'assignment' ? 'bg-primary' : 'bg-success' }} rounded-pill">
                                {{ ucfirst($deadline->type) }}
                            </span>
                        </div>
                        
                        <!-- Feedback Section (if completed with feedback) -->
                        @if($deadline->status === 'completed' && (!empty($deadline->feedback) || !empty($deadline->grade)))
                            <div class="alert alert-light border-start border-4 border-success mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-success">
                                        <i class="bi bi-chat-text-fill me-2"></i>Instructor Feedback
                                    </h6>
                                    @if(!empty($deadline->grade))
                                        <span class="badge bg-success">Grade: {{ $deadline->grade }}%</span>
                                    @endif
                                </div>
                                @if(!empty($deadline->feedback))
                                    <p class="mb-0 small">{{ $deadline->feedback }}</p>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Action Indicator -->
                        <div class="border-top pt-3">
                            @if($deadline->status === 'pending')
                                <div class="d-flex align-items-center text-primary">
                                    <i class="bi bi-arrow-right-circle me-2"></i>
                                    <span class="fw-medium">Click to {{ $deadline->type === 'assignment' ? 'submit assignment' : 'take quiz' }}</span>
                                </div>
                            @elseif($deadline->status === 'completed')
                                <div class="d-flex align-items-center text-success">
                                    <i class="bi bi-eye me-2"></i>
                                    <span class="fw-medium">Click to view details</span>
                                </div>
                            @elseif($deadline->status === 'overdue')
                                <div class="d-flex align-items-center text-danger">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    <span class="fw-medium">Click to submit (overdue)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted">No upcoming deadlines</h4>
                    <p class="text-muted mb-0">You're all caught up! Check back later for new assignments.</p>
                </div>
            @endforelse
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
                <a href="{{ route('student.meetings') }}" class="btn btn-outline-primary btn-sm">
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
    <div class="modal-dialog modal-lg">
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
            <div class="modal-footer" id="paymentModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

<script>
// Debug test function
function testPaymentModal() {
    console.clear();
    console.log('=== PAYMENT MODAL TEST ===');
    console.log('1. Testing modal opening...');
    showPaymentModal(999, 'Test Course DEBUG');
}

// Test function for program profile redirect (accessible from dashboard)
function testProgramModal() {
    console.clear();
    console.log('=== PROGRAM PROFILE TEST ===');
    console.log('1. Testing program profile redirect...');
    
    // Test program ID (Nursing Program)
    const testProgramId = 40;
    
    console.log(`✅ Redirecting to program profile: /profile/program/${testProgramId}`);
    
    // Redirect to the program profile page
    window.location.href = `/profile/program/${testProgramId}`;
}

// Function to manually trigger program redirect from search result
function manualShowProgramModal(programId) {
    console.log('Redirecting to program profile for ID:', programId);
    
    // Redirect to the program profile page
    window.location.href = `/profile/program/${programId}`;
}

// Emergency cleanup function for stuck backdrops
window.emergencyCleanup = function() {
    console.log('🚨 EMERGENCY CLEANUP - Removing all modal elements and backdrops');
    
    // Close all modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
        modal.classList.remove('show');
        
        // Dispose Bootstrap instances
        const instance = bootstrap.Modal.getInstance(modal);
        if (instance) {
            instance.dispose();
        }
    });
    
    // Remove all backdrops
    removeAllBackdrops();
    
    // Reset page state
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.classList.remove('modal-open');
    
    console.log('✅ Emergency cleanup completed');
    alert('Emergency cleanup completed! Page should be interactive again.');
};

// Load meetings data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMeetingsData();
});

function loadMeetingsData() {
    fetch('{{ route("student.meetings.upcoming") }}')
        .then(response => {
            if (response.status === 401) {
                document.getElementById('upcoming-meetings-list').innerHTML = 
                    '<p style="text-align: center; color: #dc3545; padding: 20px;">Please <a href="/login">log in</a> to view your meetings.</p>';
                throw new Error('HTTP 401: Unauthorized');
            }
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            // Ensure data is an array
            const meetings = Array.isArray(data) ? data : (data.meetings ? data.meetings : []);
            displayMeetings(meetings);
        })
        .catch(error => {
            if (error.message.includes('401')) return; // Already handled
            console.error('Error loading meetings:', error);
            document.getElementById('upcoming-meetings-list').innerHTML = 
                '<p style="text-align: center; color: #6c757d; padding: 20px;">Unable to load meetings</p>';
        });
}

function displayMeetings(meetings) {
    const currentMeetingsSection = document.getElementById('current-meetings-section');
    const currentMeetingsList = document.getElementById('current-meetings-list');
    const upcomingMeetingsList = document.getElementById('upcoming-meetings-list');
    
    // Ensure meetings is an array
    if (!Array.isArray(meetings)) {
        console.error('Meetings data is not an array:', meetings);
        upcomingMeetingsList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">No upcoming meetings</p>';
        if (currentMeetingsSection) currentMeetingsSection.style.display = 'none';
        return;
    }
    
    let currentMeetings = [];
    let upcomingMeetings = [];
    
    // Separate current and upcoming meetings
    meetings.forEach(meeting => {
        const meetingDate = new Date(meeting.meeting_date);
        const now = new Date();
        const diffMinutes = (now - meetingDate) / (1000 * 60);
        const duration = meeting.duration_minutes || 60;
        
        if (diffMinutes >= 0 && diffMinutes <= duration) {
            currentMeetings.push(meeting);
        } else if (meetingDate > now) {
            upcomingMeetings.push(meeting);
        }
    });
    
    // Display current meetings
    if (currentMeetings.length > 0) {
        currentMeetingsSection.style.display = 'block';
        currentMeetingsList.innerHTML = currentMeetings.map(meeting => `
            <div style="background: white; border: 1px solid #ffdddd; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                <div style="font-weight: 600; color: #d63384; margin-bottom: 4px;">
                    ${meeting.title}
                </div>
                <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">
                    ${meeting.program_name} • ${meeting.batch_name}
                </div>
                <a href="${meeting.meeting_url || '#'}" target="_blank" 
                   class="btn btn-danger btn-sm" style="font-size: 0.8rem;">
                    <i class="bi bi-camera-video me-1"></i>Join Now
                </a>
            </div>
        `).join('');
    } else {
        currentMeetingsSection.style.display = 'none';
    }
    
    // Display upcoming meetings
    if (upcomingMeetings.length > 0) {
        upcomingMeetingsList.innerHTML = upcomingMeetings.slice(0, 3).map(meeting => {
            const meetingDate = new Date(meeting.meeting_date);
            const isToday = isDateToday(meetingDate);
            const isTomorrow = isDateTomorrow(meetingDate);
            
            let badge = '';
            if (isToday) {
                badge = '<span style="background: #198754; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem;">Today</span>';
            } else if (isTomorrow) {
                badge = '<span style="background: #ffc107; color: black; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem;">Tomorrow</span>';
            } else {
                badge = `<span style="background: #0d6efd; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem;">${meetingDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}</span>`;
            }
            
            return `
                <div style="background: #f8f9fa; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 4px;">
                        <div style="font-weight: 600; color: #212529; flex: 1;">
                            ${meeting.title}
                        </div>
                        ${badge}
                    </div>
                    <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 4px;">
                        ${meeting.program_name} • ${meeting.batch_name}
                    </div>
                    <div style="font-size: 0.8rem; color: #6c757d;">
                        <i class="bi bi-clock me-1"></i>${meetingDate.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}
                    </div>
                </div>
            `;
        }).join('');
    } else {
        upcomingMeetingsList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">No upcoming meetings</p>';
    }
}

function isDateToday(date) {
    const today = new Date();
    return date.toDateString() === today.toDateString();
}

function isDateTomorrow(date) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return date.toDateString() === tomorrow.toDateString();
}

function showStatusModal(status, courseName, enrollmentId = null) {
    console.log('showStatusModal called with:', status, courseName, enrollmentId);
    
    // Ensure Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not available');
        alert('Modal functionality is not available. Please refresh the page.');
        return;
    }
    
    const statusModalElement = document.getElementById('statusModal');
    if (!statusModalElement) {
        console.error('Status modal element not found');
        return;
    }
    
    const title = document.getElementById('statusModalTitle');
    const body = document.getElementById('statusModalBody');
    
    let modalContent = '';
    
    switch(status) {
        case 'pending':
            title.textContent = 'Pending Verification';
            modalContent = `
                <div class="text-center">
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Enrollment Under Review</h5>
                    <p>Your enrollment for <strong>${courseName}</strong> is currently being reviewed by our administrators.</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Please wait for admin approval. You will be notified once your registration is verified.
                    </div>
                </div>
            `;
            break;
        case 'rejected':
            title.textContent = 'Enrollment Rejected';
            modalContent = `
                <div class="text-center">
                    <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Enrollment Rejected</h5>
                    <p>Unfortunately, your enrollment for <strong>${courseName}</strong> has been rejected.</p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Please contact our support team for more information.
                    </div>
                </div>
            `;
            break;
    }
    
    body.innerHTML = modalContent;
    
    // Force remove any existing modal instances
    const existingInstance = bootstrap.Modal.getInstance(statusModalElement);
    if (existingInstance) {
        existingInstance.dispose();
        console.log('Disposed existing status modal instance');
    }
    
    try {
        // Create new modal instance with proper options for closing
        const statusModalInstance = new bootstrap.Modal(statusModalElement, {
            backdrop: true, // Allow closing with backdrop click
            keyboard: true, // Allow closing with ESC key
            focus: true
        });
        
        // Add comprehensive event listeners
        statusModalElement.addEventListener('shown.bs.modal', function(e) {
            console.log('Status modal fully shown');
            statusModalElement.setAttribute('tabindex', '-1');
            statusModalElement.focus();
        }, { once: true });
        
        statusModalInstance.show();
        console.log('Status modal shown successfully');
        
    } catch (error) {
        console.error('Error showing status modal:', error);
        // Fallback method - avoid creating manual backdrops
        statusModalElement.style.display = 'block';
        statusModalElement.classList.add('show');
        statusModalElement.style.zIndex = '1055';
        
        // Focus the modal for accessibility
        statusModalElement.focus();
        
        // Add simple click-outside-to-close
        const closeModal = () => {
            statusModalElement.style.display = 'none';
            statusModalElement.classList.remove('show');
            removeAllBackdrops();
        };
        
        // Add escape key listener
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
        
        // Add close button functionality
        const closeBtn = statusModalElement.querySelector('.btn-close, [data-bs-dismiss="modal"]');
        if (closeBtn) {
            closeBtn.onclick = closeModal;
        }
    }
}

// Rejected Registration Modal Functions
let currentRejectedEnrollmentId = null;
let rejectedRegistrationData = null;

function showRejectedModal(courseName, enrollmentId) {
    console.log('showRejectedModal called with:', courseName, enrollmentId);
    currentRejectedEnrollmentId = enrollmentId;
    
    // Ensure Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not available');
        alert('Modal functionality is not available. Please refresh the page.');
        return;
    }
    
    const rejectedModalElement = document.getElementById('rejectedModal');
    if (!rejectedModalElement) {
        console.error('Rejected modal element not found');
        return;
    }
    
    const title = document.getElementById('rejectedModalTitle');
    const body = document.getElementById('rejectedModalBody');
    
    title.textContent = `Registration Rejected - ${courseName}`;
    
    // Show loading state
    body.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading rejection details...</p>
        </div>
    `;
    
    // Show modal
    const rejectedModalInstance = new bootstrap.Modal(rejectedModalElement, {
        backdrop: true,
        keyboard: true,
        focus: true
    });
    
    rejectedModalInstance.show();
    
    // Load rejection details
    loadRejectionDetails(enrollmentId);
}

function loadRejectionDetails(enrollmentId) {
    console.log('Loading rejection details for enrollment:', enrollmentId);
    
    fetch(`/student/enrollment/${enrollmentId}/rejection-details`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Rejection details loaded:', data);
        if (data.success) {
            rejectedRegistrationData = data.data;
            displayRejectionDetails(data.data);
        } else {
            showRejectionError(data.message || 'Failed to load rejection details');
        }
    })
    .catch(error => {
        console.error('Error loading rejection details:', error);
        showRejectionError('Network error occurred while loading rejection details');
    });
}

function displayRejectionDetails(data) {
    const body = document.getElementById('rejectedModalBody');
    const editBtn = document.getElementById('editRegistrationBtn');
    const deleteBtn = document.getElementById('deleteRegistrationBtn');
    
    let rejectedFields = [];
    if (data.rejected_fields) {
        try {
            rejectedFields = typeof data.rejected_fields === 'string' 
                ? JSON.parse(data.rejected_fields) 
                : data.rejected_fields;
        } catch (e) {
            console.error('Error parsing rejected fields:', e);
            rejectedFields = [];
        }
    }
    
    let modalContent = `
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Your registration has been rejected</strong>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h6>Rejection Details:</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Rejected By:</strong> ${data.rejected_by_name || 'Administrator'}</li>
                    <li class="list-group-item"><strong>Rejected On:</strong> ${new Date(data.rejected_at).toLocaleDateString()}</li>
                    <li class="list-group-item"><strong>Reason:</strong> ${data.rejection_reason || 'No specific reason provided'}</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Program Details:</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Program:</strong> ${data.program_name}</li>
                    <li class="list-group-item"><strong>Package:</strong> ${data.package_name}</li>
                    <li class="list-group-item"><strong>Learning Mode:</strong> ${data.learning_mode}</li>
                </ul>
            </div>
        </div>
    `;
    
    if (rejectedFields.length > 0) {
        modalContent += `
            <div class="mt-4">
                <h6 class="text-danger">Fields that need correction:</h6>
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        ${rejectedFields.map(field => `<li><strong>${field.replace(/_/g, ' ').toUpperCase()}</strong>: ${field.includes('_') ? 'Please review and update this field' : field}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }
    
    modalContent += `
        <div class="mt-4">
            <div class="alert alert-info">
                <h6><i class="bi bi-info-circle me-2"></i>What can you do?</h6>
                <p class="mb-2">You have two options:</p>
                <ul class="mb-0">
                    <li><strong>Edit & Resubmit:</strong> Correct the issues and resubmit your registration</li>
                    <li><strong>Delete Registration:</strong> Remove this registration completely (cannot be undone)</li>
                </ul>
            </div>
        </div>
    `;
    
    body.innerHTML = modalContent;
    
    // Show action buttons
    editBtn.style.display = 'inline-block';
    deleteBtn.style.display = 'inline-block';
}

function showRejectionError(message) {
    const body = document.getElementById('rejectedModalBody');
    body.innerHTML = `
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Error:</strong> ${message}
        </div>
        <p>Please contact support if this problem persists.</p>
    `;
}

function editRejectedRegistration() {
    console.log('Opening edit modal for registration:', currentRejectedEnrollmentId);
    
    if (!rejectedRegistrationData) {
        alert('Registration data not loaded. Please try again.');
        return;
    }
    
    // Close rejected modal
    const rejectedModal = bootstrap.Modal.getInstance(document.getElementById('rejectedModal'));
    if (rejectedModal) {
        rejectedModal.hide();
    }
    
    // Load edit form
    loadEditRegistrationForm();
}

function loadEditRegistrationForm() {
    const editModal = document.getElementById('editRegistrationModal');
    const editModalBody = document.getElementById('editRegistrationModalBody');
    const editModalTitle = document.getElementById('editRegistrationModalTitle');
    
    editModalTitle.textContent = `Edit Registration - ${rejectedRegistrationData.program_name}`;
    
    // Show loading state
    editModalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading edit form...</p>
        </div>
    `;
    
    // Show edit modal
    const editModalInstance = new bootstrap.Modal(editModal, {
        backdrop: 'static',
        keyboard: false,
        focus: true
    });
    
    editModalInstance.show();
    
    // Load the edit form
    fetch(`/student/enrollment/${currentRejectedEnrollmentId}/edit-form`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            editModalBody.innerHTML = data.html;
            // Initialize any form components if needed
            initializeEditForm();
        } else {
            editModalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading edit form: ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading edit form:', error);
        editModalBody.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Network error occurred while loading the edit form.
            </div>
        `;
    });
}

function initializeEditForm() {
    // Initialize file uploads, date pickers, etc.
    console.log('Initializing edit form components...');
    
    // Add file change handlers
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            validateFileUpload(this);
        });
    });
    
    // Add form validation
    document.querySelectorAll('input[required], select[required]').forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

function validateFileUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file size (5MB max)
    if (file.size > 5242880) {
        alert('File size must be less than 5MB');
        input.value = '';
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, PNG, and PDF files are allowed');
        input.value = '';
        return;
    }
}

function validateField(field) {
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        field.classList.add('is-invalid');
        return false;
    } else {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        return true;
    }
}

function resubmitRegistration() {
    console.log('Resubmitting registration for enrollment:', currentRejectedEnrollmentId);
    
    const form = document.getElementById('editRegistrationForm');
    if (!form) {
        alert('Form not found. Please try again.');
        return;
    }
    
    // Validate form
    let isValid = true;
    form.querySelectorAll('input[required], select[required]').forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        alert('Please fill in all required fields correctly.');
        return;
    }
    
    // Show loading state
    const resubmitBtn = document.getElementById('resubmitBtn');
    const originalText = resubmitBtn.innerHTML;
    resubmitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Resubmitting...';
    resubmitBtn.disabled = true;
    
    // Create FormData
    const formData = new FormData(form);
    formData.append('_method', 'PUT');
    
    // Submit form
    fetch(`/student/enrollment/${currentRejectedEnrollmentId}/resubmit`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Registration resubmitted successfully! Your registration is now under review again.');
            
            // Close modal and refresh page
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editRegistrationModal'));
            if (editModal) {
                editModal.hide();
            }
            
            // Refresh page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } else {
            alert('Error resubmitting registration: ' + (data.message || 'Unknown error'));
            resubmitBtn.innerHTML = originalText;
            resubmitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error resubmitting registration:', error);
        alert('Network error occurred. Please try again.');
        resubmitBtn.innerHTML = originalText;
        resubmitBtn.disabled = false;
    });
}

function deleteRejectedRegistration() {
    if (!confirm('Are you sure you want to delete this registration? This action cannot be undone and you will need to register again from the beginning.')) {
        return;
    }
    
    console.log('Deleting registration for enrollment:', currentRejectedEnrollmentId);
    
    fetch(`/student/enrollment/${currentRejectedEnrollmentId}/delete`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Registration deleted successfully.');
            
            // Close modal and refresh page
            const rejectedModal = bootstrap.Modal.getInstance(document.getElementById('rejectedModal'));
            if (rejectedModal) {
                rejectedModal.hide();
            }
            
            // Refresh page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } else {
            alert('Error deleting registration: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error deleting registration:', error);
        alert('Network error occurred. Please try again.');
    });
}

// Payment Modal Variables
let currentEnrollmentId = null;
let selectedPaymentMethod = null;
let enrollmentDetails = null;
let paymentModalInstance = null;

// Initialize modal on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing modals...');
    
    // Clean up any existing stuck backdrops first
    removeAllBackdrops();
    
    // Wait for Bootstrap to be fully loaded
    const initModals = () => {
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap is not loaded!');
            setTimeout(initModals, 100);
            return;
        }
        
        console.log('Bootstrap loaded, setting up modals...');
        
        // Initialize Payment Modal
        const paymentModal = document.getElementById('paymentModal');
        if (paymentModal) {
            // Dispose any existing instances
            const existingPaymentInstance = bootstrap.Modal.getInstance(paymentModal);
            if (existingPaymentInstance) {
                existingPaymentInstance.dispose();
            }
            
            // Create new instance
            paymentModalInstance = new bootstrap.Modal(paymentModal, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            
            // Enhanced event handlers
            paymentModal.addEventListener('shown.bs.modal', function() {
                console.log('Payment modal fully shown');
                this.querySelector('.btn-close')?.focus();
            });
            
            paymentModal.addEventListener('hidden.bs.modal', function() {
                console.log('Payment modal hidden');
                resetPaymentModal();
                // Force remove any stuck backdrops
                removeAllBackdrops();
            });
            
            // Ensure close buttons work
            paymentModal.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    console.log('Close button clicked');
                    if (paymentModalInstance) {
                        paymentModalInstance.hide();
                    }
                    // Force remove backdrops after a delay
                    setTimeout(removeAllBackdrops, 100);
                });
            });
            
            console.log('Payment modal initialized');
        }
        
        // Initialize Status Modal
        const statusModal = document.getElementById('statusModal');
        if (statusModal) {
            // Enhanced event handlers for status modal
            statusModal.addEventListener('shown.bs.modal', function() {
                console.log('Status modal fully shown');
                this.querySelector('.btn-close')?.focus();
            });
            
            statusModal.addEventListener('hidden.bs.modal', function() {
                console.log('Status modal hidden');
                // Force remove any stuck backdrops
                removeAllBackdrops();
            });
            
            // Ensure close buttons work
            statusModal.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    console.log('Status modal close button clicked');
                    const modalInstance = bootstrap.Modal.getInstance(statusModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    // Force remove backdrops after a delay
                    setTimeout(removeAllBackdrops, 100);
                });
            });
            
            console.log('Status modal initialized');
        }
        
        // Global backdrop click handler
        document.addEventListener('click', function(e) {
            // Payment modal backdrop
            if (e.target.id === 'paymentModal' && e.target.classList.contains('modal')) {
                console.log('Payment modal backdrop clicked');
                if (paymentModalInstance) {
                    paymentModalInstance.hide();
                }
                setTimeout(removeAllBackdrops, 100);
            }
            
            // Status modal backdrop
            if (e.target.id === 'statusModal' && e.target.classList.contains('modal')) {
                console.log('Status modal backdrop clicked');
                const modalInstance = bootstrap.Modal.getInstance(statusModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
                setTimeout(removeAllBackdrops, 100);
            }
        });
        
        // Enhanced global escape key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                console.log('ESC key pressed - checking for open modals');
                
                // Check for payment modal
                const paymentModal = document.getElementById('paymentModal');
                if (paymentModal && paymentModal.classList.contains('show')) {
                    console.log('Closing payment modal via ESC');
                    const instance = bootstrap.Modal.getInstance(paymentModal);
                    if (instance) {
                        instance.hide();
                    } else if (paymentModalInstance) {
                        paymentModalInstance.hide();
                    } else {
                        // Manual close
                        paymentModal.style.display = 'none';
                        paymentModal.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        removeAllBackdrops();
                    }
                    setTimeout(removeAllBackdrops, 100);
                }
                
                // Check for status modal
                const statusModal = document.getElementById('statusModal');
                if (statusModal && statusModal.classList.contains('show')) {
                    console.log('Closing status modal via ESC');
                    const instance = bootstrap.Modal.getInstance(statusModal);
                    if (instance) {
                        instance.hide();
                    } else {
                        // Manual close
                        statusModal.style.display = 'none';
                        statusModal.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        removeAllBackdrops();
                    }
                    setTimeout(removeAllBackdrops, 100);
                }
            }
        }, true); // Use capture phase to ensure it runs first
        
        console.log('All modal handlers initialized successfully');
    };
    
    // Start initialization
    initModals();
});

// Function to forcefully remove all modal backdrops
function removeAllBackdrops() {
    console.log('Removing all modal backdrops...');
    
    // Remove all modal-backdrop elements
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => {
        console.log('Removing backdrop:', backdrop);
        backdrop.remove();
    });
    
    // Ensure body classes are cleaned up
    document.body.classList.remove('modal-open');
    document.body.style.paddingRight = '';
    document.body.style.overflow = '';
    
    console.log(`Removed ${backdrops.length} backdrop(s)`);
}

function resetPaymentModal() {
    goToStep1();
    currentEnrollmentId = null;
    selectedPaymentMethod = null;
    enrollmentDetails = null;
    
    // Clear form data
    const form = document.getElementById('paymentProofForm');
    if (form) {
        form.reset();
    }
    
    // Force clean modal state
    removeAllBackdrops();
}

function showPaymentModal(enrollmentId, courseName) {
    console.log('showPaymentModal called with:', enrollmentId, courseName);
    currentEnrollmentId = enrollmentId;
    
    // Ensure Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not available');
        alert('Payment modal functionality is not available. Please refresh the page.');
        return;
    }
    
    const paymentModalElement = document.getElementById('paymentModal');
    if (!paymentModalElement) {
        console.error('Payment modal element not found');
        return;
    }
    
    // Reset modal state
    goToStep1();
    const modalLabel = document.getElementById('paymentModalLabel');
    if (modalLabel) {
        modalLabel.textContent = `Complete Payment - ${courseName}`;
    }
    
    // Load payment methods and enrollment details
    loadPaymentMethods();
    loadEnrollmentDetails(enrollmentId);
    
    // Force remove any existing modal instances
    const existingInstance = bootstrap.Modal.getInstance(paymentModalElement);
    if (existingInstance) {
        existingInstance.dispose();
        console.log('Disposed existing modal instance');
    }
    
    // Create new modal instance with proper options for closing
    paymentModalInstance = new bootstrap.Modal(paymentModalElement, {
        backdrop: true, // Allow closing with backdrop click
        keyboard: true, // Allow closing with ESC key
        focus: true
    });
    
    // Add comprehensive event listeners
    paymentModalElement.addEventListener('shown.bs.modal', function(e) {
        console.log('Payment modal fully shown');
        // Ensure modal is focusable and interactive
        paymentModalElement.setAttribute('tabindex', '-1');
        paymentModalElement.focus();
    }, { once: true });
    
    paymentModalElement.addEventListener('hidden.bs.modal', function(e) {
        console.log('Payment modal hidden');
        resetPaymentModal();
    }, { once: true });
    
    // Show the modal
    try {
        paymentModalInstance.show();
        console.log('Payment modal show() called successfully');
    } catch (error) {
        console.error('Error showing payment modal:', error);
        // Fallback manual show - avoid creating persistent backdrops
        paymentModalElement.style.display = 'block';
        paymentModalElement.classList.add('show');
        paymentModalElement.style.zIndex = '1055';
        
        // Focus the modal for accessibility
        paymentModalElement.focus();
        
        // Add simple close functionality
        const closeModal = () => {
            paymentModalElement.style.display = 'none';
            paymentModalElement.classList.remove('show');
            resetPaymentModal();
            removeAllBackdrops();
        };
        
        // Add escape key listener
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
        
        // Add close button functionality
        const closeBtn = paymentModalElement.querySelector('.btn-close, [data-bs-dismiss="modal"]');
        if (closeBtn) {
            closeBtn.onclick = closeModal;
        }
    }
}

async function loadPaymentMethods() {
    console.log('Loading payment methods...');
    try {
        const response = await fetch('/student/payment/methods');
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success && data.data.length > 0) {
            renderPaymentMethods(data.data);
        } else {
            console.log('No payment methods found, showing mock data for testing');
            // Show mock data for testing
            const mockMethods = [
                {
                    payment_method_id: 1,
                    method_name: 'GCash',
                    method_type: 'gcash',
                    qr_code_path: '/test-qr.png',
                    description: 'Pay via GCash mobile wallet'
                },
                {
                    payment_method_id: 2,
                    method_name: 'Maya (PayMaya)',
                    method_type: 'maya',
                    qr_code_path: '/test-qr.png',
                    description: 'Pay via Maya mobile wallet'
                }
            ];
            renderPaymentMethods(mockMethods);
        }
    } catch (error) {
        console.error('Error loading payment methods:', error);
        console.log('Showing mock data for testing due to error');
        // Show mock data for testing
        const mockMethods = [
            {
                payment_method_id: 1,
                method_name: 'GCash',
                method_type: 'gcash',
                qr_code_path: '/test-qr.png',
                description: 'Pay via GCash mobile wallet'
            },
            {
                payment_method_id: 2,
                method_name: 'Maya (PayMaya)',
                method_type: 'maya',
                qr_code_path: '/test-qr.png',
                description: 'Pay via Maya mobile wallet'
            }
        ];
        renderPaymentMethods(mockMethods);
    }
}

function renderPaymentMethods(methods) {
  console.log('Rendering payment methods:', methods);
  const container = document.getElementById('paymentMethodsContainer');

  // Remove any existing event listeners by cloning the container
  const newContainer = container.cloneNode(false);
  container.parentNode.replaceChild(newContainer, container);
  
  // Update reference to new container
  const updatedContainer = document.getElementById('paymentMethodsContainer');

  // build all cards at once
  updatedContainer.innerHTML = methods.map(method => {
    const hasQR = method.qr_code_path?.trim() !== '';
    const iconClass = getPaymentMethodIcon(method.method_type);
    return `
      <div class="col-md-6 mb-3">
        <div
          class="card payment-method-card h-100"
          style="cursor:pointer; transition:all .3s; z-index:10; position:relative;"
          data-method-id="${method.payment_method_id}"
          data-method-name="${method.method_name}"
          data-method-type="${method.method_type}"
          data-qr-path="${method.qr_code_path||''}"
          data-description="${method.description||''}"
        >
          <div class="card-body text-center">
            <i class="${iconClass}" style="font-size:2.5rem; margin-bottom:10px;"></i>
            <h6 class="card-title">${method.method_name}</h6>
            <p class="card-text small text-muted">
              ${method.description || 'Digital payment method'}
            </p>
            ${ hasQR
              ? '<span class="badge bg-success">QR Available</span>'
              : '<span class="badge bg-secondary">Manual Process</span>' }
          </div>
        </div>
      </div>
    `;
  }).join('');
  console.log('Payment methods HTML set');

  // Add single delegated click listener to the updated container
  updatedContainer.addEventListener('click', function(e) {
    const card = e.target.closest('.payment-method-card');
    if (!card) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    console.log('Card clicked!', card.dataset.methodName);
    const { methodId, methodName, methodType, qrPath, description } = card.dataset;
    selectPaymentMethod(methodId, methodName, methodType, qrPath, description);
  });
}

function getPaymentMethodIcon(methodType) {
    const icons = {
        'gcash': 'bi bi-phone',
        'maya': 'bi bi-phone',
        'bank_transfer': 'bi bi-bank',
        'credit_card': 'bi bi-credit-card',
        'cash': 'bi bi-cash-coin',
        'other': 'bi bi-wallet2'
    };
    return icons[methodType] || 'bi bi-wallet2';
}

function selectPaymentMethod(id, name, type, qrPath, description) {
    console.log('Payment method selected:', { id, name, type, qrPath, description });
    
    // Remove previous selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('border-primary', 'border-2');
        card.style.backgroundColor = '';
    });
    
    // Find and highlight selected card
    const selectedCard = document.querySelector(`[data-method-id="${id}"]`);
    if (selectedCard) {
        selectedCard.classList.add('border-primary', 'border-2');
        selectedCard.style.backgroundColor = 'rgba(13, 110, 253, 0.1)';
    }
    
    selectedPaymentMethod = {
        id: id,
        name: name,
        type: type,
        qr_path: qrPath,
        description: description
    };
    
    // Show payment details and continue button
    document.getElementById('paymentDetails').style.display = 'block';
    
    // Add continue button if not exists
    let continueBtn = document.getElementById('continueToQRBtn');
    if (!continueBtn) {
        const container = document.getElementById('paymentStep1');
        const buttonHTML = `
            <div class="text-center mt-4">
                <button type="button" class="btn btn-primary btn-lg" id="continueToQRBtn" onclick="goToStep2()">
                    <i class="bi bi-arrow-right me-2"></i>Continue to Payment
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', buttonHTML);
    } else {
        continueBtn.style.display = 'block';
    }
}

async function loadEnrollmentDetails(enrollmentId) {
    try {
        const response = await fetch(`/student/payment/enrollment/${enrollmentId}/details`);
        
        if (response.status === 403) {
            console.warn('Access denied to enrollment details. Using mock data for testing.');
            // Use mock data for testing when access is denied
            enrollmentDetails = {
                program_name: 'Test Course Program',
                package_name: 'Standard Package',
                amount: '5000.00'
            };
        } else {
            const data = await response.json();
            if (data.success) {
                enrollmentDetails = data.data;
            } else {
                throw new Error(data.message || 'Failed to load enrollment details');
            }
        }
        
        // Display enrollment details (either real or mock)
        if (enrollmentDetails) {
            document.getElementById('enrollmentInfo').innerHTML = `
                <div class="row">
                    <div class="col-sm-4"><strong>Program:</strong></div>
                    <div class="col-sm-8">${enrollmentDetails.program_name}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4"><strong>Package:</strong></div>
                    <div class="col-sm-8">${enrollmentDetails.package_name}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4"><strong>Amount:</strong></div>
                    <div class="col-sm-8"><strong>₱${parseFloat(enrollmentDetails.amount).toLocaleString()}</strong></div>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Error loading enrollment details:', error);
        // Show error message in the UI
        document.getElementById('enrollmentInfo').innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Unable to load enrollment details. Please contact support if this persists.
            </div>
        `;
    }
}

function goToStep1() {
    document.getElementById('paymentStep1').style.display = 'block';
    document.getElementById('paymentStep2').style.display = 'none';
    document.getElementById('paymentStep3').style.display = 'none';
    document.getElementById('paymentModalFooter').style.display = 'block';
}

function goToStep2() {
    if (!selectedPaymentMethod) {
        alert('Please select a payment method first');
        return;
    }
    
    if (!selectedPaymentMethod.qr_path || selectedPaymentMethod.qr_path.trim() === '') {
        alert('This payment method does not support QR code payments. Please contact support for assistance.');
        return;
    }
    
    // Setup QR code step
    document.getElementById('paymentMethodTitle').textContent = `Pay with ${selectedPaymentMethod.name}`;
    document.getElementById('paymentMethodName').textContent = selectedPaymentMethod.name;
    
    if (enrollmentDetails) {
        const amount = parseFloat(enrollmentDetails.amount).toFixed(2);
        document.getElementById('paymentAmount').textContent = parseFloat(amount).toLocaleString();
        document.getElementById('paymentAmountInstruction').textContent = parseFloat(amount).toLocaleString();
    }
    
    // Set QR code image
    const qrImage = document.getElementById('qrCodeImage');
    if (selectedPaymentMethod.qr_path) {
        qrImage.src = `/storage/${selectedPaymentMethod.qr_path}`;
        qrImage.style.display = 'block';
    } else {
        qrImage.style.display = 'none';
    }
    
    // Reset form
    document.getElementById('paymentProof').value = '';
    document.getElementById('referenceNumber').value = '';
    
    // Show step 2
    document.getElementById('paymentStep1').style.display = 'none';
    document.getElementById('paymentStep2').style.display = 'block';
    document.getElementById('paymentStep3').style.display = 'none';
}

async function submitPayment() {
    const fileInput = document.getElementById('paymentProof');
    const referenceInput = document.getElementById('referenceNumber');
    const submitBtn = document.getElementById('submitPaymentBtn');
    
    if (!fileInput.files[0]) {
        alert('Please upload payment proof screenshot');
        return;
    }
    
    if (!selectedPaymentMethod || !currentEnrollmentId || !enrollmentDetails) {
        alert('Missing payment information. Please start over.');
        return;
    }
    
    // Show loading state
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-half me-2"></i>Uploading...';
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('payment_proof', fileInput.files[0]);
        formData.append('reference_number', referenceInput.value || '');
        formData.append('payment_method_id', selectedPaymentMethod.id);
        formData.append('enrollment_id', currentEnrollmentId);
        formData.append('amount', enrollmentDetails.amount);
        
        const response = await fetch('/student/payment/upload-proof', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success step
            document.getElementById('paymentStep2').style.display = 'none';
            document.getElementById('paymentStep3').style.display = 'block';
            document.getElementById('paymentModalFooter').innerHTML = `
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="location.reload()">
                    <i class="bi bi-check-circle me-2"></i>Done
                </button>
            `;
        } else {
            throw new Error(data.error || 'Upload failed');
        }
        
    } catch (error) {
        console.error('Error uploading payment proof:', error);
        alert('Failed to upload payment proof. Please try again.');
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}
</script>
<script>
// Global: Always clean up backdrops when any modal is hidden
if (typeof bootstrap !== 'undefined') {
    document.addEventListener('hidden.bs.modal', function() {
        setTimeout(removeAllBackdrops, 100);
    });
}
// Failsafe: Remove all backdrops on any click if no modal is open
// (prevents UI lockout in edge cases)
document.addEventListener('click', function() {
    const anyModalOpen = document.querySelector('.modal.show');
    if (!anyModalOpen) {
        removeAllBackdrops();
    }
});

// 🔥 REAL-TIME DASHBOARD UPDATE SYSTEM 🔥
// Listen for completion updates from course pages
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing real-time dashboard update system...');
    
    // Method 1: localStorage event listener (works across tabs)
    window.addEventListener('storage', function(e) {
        if (e.key === 'dashboardUpdate') {
            console.log('📡 Received dashboard update via localStorage:', e.newValue);
            try {
                const updateData = JSON.parse(e.newValue);
                updateDashboardUI(updateData);
            } catch (error) {
                console.error('Error parsing dashboard update data:', error);
            }
        }
    });
    
    // Method 2: BroadcastChannel listener (works across tabs in modern browsers)
    if (window.BroadcastChannel) {
        try {
            const channel = new BroadcastChannel('dashboard-updates');
            channel.onmessage = function(event) {
                console.log('📡 Received dashboard update via BroadcastChannel:', event.data);
                updateDashboardUI(event.data);
            };
            console.log('✅ BroadcastChannel listener initialized');
        } catch (error) {
            console.log('❌ BroadcastChannel not available:', error);
        }
    }
    
    // Method 3: Custom event listener (works on same page)
    window.addEventListener('dashboardUpdate', function(event) {
        console.log('📡 Received dashboard update via CustomEvent:', event.detail);
        updateDashboardUI(event.detail);
    });
    
    // Method 4: Polling fallback (checks for updates every 5 seconds)
    setInterval(function() {
        const lastUpdate = localStorage.getItem('dashboardLastUpdate');
        if (lastUpdate) {
            const lastUpdateTime = parseInt(lastUpdate);
            const now = Date.now();
            // If last update was more than 5 seconds ago, check for new updates
            if (now - lastUpdateTime > 5000) {
                const updateData = localStorage.getItem('dashboardUpdate');
                if (updateData) {
                    try {
                        const data = JSON.parse(updateData);
                        const dataTime = data.timestamp || 0;
                        // Only update if the data is newer than our last check
                        if (dataTime > lastUpdateTime) {
                            console.log('📡 Received dashboard update via polling:', data);
                            updateDashboardUI(data);
                            localStorage.setItem('dashboardLastUpdate', now.toString());
                        }
                    } catch (error) {
                        console.error('Error parsing dashboard update data:', error);
                    }
                }
            }
        }
    }, 5000);
    
    console.log('✅ Real-time dashboard update system initialized');
});

// Function to update dashboard UI with new completion data
function updateDashboardUI(updateData) {
    console.log('🔄 Updating dashboard UI with:', updateData);
    
    const { type, id, progress, completed_modules, total_modules, course_id, module_id } = updateData;
    
    // Update overall progress badge using the actual progress calculation from backend
    const completionBadge = document.querySelector('.completion-badge');
    if (completionBadge && progress !== undefined) {
        completionBadge.textContent = `${Math.round(progress)}% overall progress`;
        console.log('✅ Updated overall progress badge to', Math.round(progress) + '%');
    }
    
    // Find and update all course cards since we might have multiple courses for the same program
    const courseItems = document.querySelectorAll('.course-item');
    courseItems.forEach(courseItem => {
        const courseName = courseItem.querySelector('h3')?.textContent;
        const courseDataId = courseItem.getAttribute('data-course-id');
        
        // Try to match by course ID or update all cards for the program
        if (courseDataId == course_id || type === 'content' || type === 'course') {
            // Update progress bar with the calculated progress from backend
            const progressBar = courseItem.querySelector('.progress-bar');
            if (progressBar && progress !== undefined) {
                progressBar.style.setProperty('--progress', Math.round(progress) + '%');
                console.log('✅ Updated progress bar to', Math.round(progress) + '%');
            }
            
            // Update progress text
            const progressText = courseItem.querySelector('.progress-text');
            if (progressText && progress !== undefined) {
                progressText.textContent = `${Math.round(progress)}% complete`;
                console.log('✅ Updated progress text to', Math.round(progress) + '% complete');
            }
            
            // Update modules count if available
            if (completed_modules !== undefined && total_modules !== undefined) {
                const courseMeta = courseItem.querySelector('.course-meta span');
                if (courseMeta) {
                    courseMeta.textContent = `${completed_modules} / ${total_modules} modules complete`;
                    console.log('✅ Updated modules count to', completed_modules + '/' + total_modules);
                }
            }
            
            // Update button if course is completed
            if (type === 'course' && progress >= 100) {
                const button = courseItem.querySelector('.resume-btn');
                if (button) {
                    button.textContent = 'Completed';
                    button.classList.remove('btn-success', 'btn-outline-primary');
                    button.classList.add('btn-outline-success', 'completed');
                    button.disabled = true;
                    console.log('✅ Updated course button to Completed');
                }
            }
            
            // Add visual feedback
            courseItem.style.transition = 'all 0.3s ease';
            courseItem.style.transform = 'scale(1.02)';
            courseItem.style.boxShadow = '0 8px 25px rgba(39, 174, 96, 0.3)';
            
            setTimeout(() => {
                courseItem.style.transform = 'scale(1)';
                courseItem.style.boxShadow = '';
            }, 300);
            
            console.log('✅ Updated course card:', courseName || courseId);
        }
    });
    
    // Show success notification
    showDashboardUpdateNotification(type, id);
    
    // Clear the update data to prevent duplicate processing
    localStorage.removeItem('dashboardUpdate');
}

// Function to show a success notification
function showDashboardUpdateNotification(type, id) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'dashboard-update-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        font-weight: 600;
        max-width: 300px;
    `;
    
    let message = '';
    if (type === 'content') {
        message = '📚 Content marked as complete!';
    } else if (type === 'course') {
        message = '🎉 Course completed successfully!';
    } else if (type === 'module') {
        message = '🏆 Module completed! Great job!';
    } else {
        message = '✅ Progress updated!';
    }
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill" style="font-size: 1.2rem;"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Function to manually refresh the dashboard
function refreshDashboard() {
    console.log('🔄 Manually refreshing dashboard...');
    
    // Show loading state
    const refreshBtn = document.querySelector('[onclick="refreshDashboard()"]');
    if (refreshBtn) {
        const originalHTML = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Refreshing...';
        refreshBtn.disabled = true;
        
        // Reload the page after a short delay
        setTimeout(() => {
            window.location.reload();
        }, 500);
    } else {
        window.location.reload();
    }
}

// Test function for dashboard updates (remove in production)
function testDashboardUpdate() {
    console.log('🧪 Testing dashboard update from dashboard...');
    const testData = {
        type: 'course',
        id: '33',
        timestamp: Date.now(),
        progress: 75,
        completed_modules: 2,
        total_modules: 3,
        course_id: '33',
        module_id: '67'
    };
    updateDashboardUI(testData);
}

// Redirect to assignment/quiz function
function redirectToAssignment(referenceId, moduleId, type, programId) {
    console.log('Redirecting to:', { referenceId, moduleId, type, programId });
    
    if (type === 'assignment') {
        if (programId) {
            // Directly redirect to student course page using the program ID
            window.location.href = `/student/course/${programId}`;
        } else if (moduleId) {
            // Fallback: Get the program ID for this module
            fetch(`/api/module/${moduleId}/program`)
                .then(response => response.json())
                .then(data => {
                    if (data.program_id) {
                        window.location.href = `/student/course/${data.program_id}`;
                    } else {
                        console.warn('No program ID found for module', moduleId);
                        window.location.href = '/student/dashboard';
                    }
                })
                .catch(error => {
                    console.error('Error getting program ID:', error);
                    window.location.href = '/student/dashboard';
                });
        } else {
            console.warn('No program ID or module ID provided for assignment', referenceId);
            window.location.href = '/student/dashboard';
        }
    } else if (type === 'quiz') {
        if (referenceId) {
            // Redirect to quiz start page using the module ID (referenceId for quizzes)
            window.location.href = `/student/quiz/${referenceId}/start`;
        } else {
            console.warn('No reference ID provided for quiz');
            window.location.href = '/student/dashboard';
        }
    } else {
        console.warn('Unknown deadline type:', type);
        window.location.href = '/student/dashboard';
    }
}

// Announcement Modal Functions
function openAnnouncementModal(id, title, content, type, time) {
    const modal = document.getElementById('announcementModal');
    const modalTitle = document.getElementById('announcementModalTitle');
    const modalContent = document.getElementById('announcementModalContent');
    const modalType = document.getElementById('announcementModalType');
    const modalTime = document.getElementById('announcementModalTime');
    
    modalTitle.textContent = title;
    modalContent.textContent = content;
    modalType.textContent = type === 'video' ? 'Video Announcement' : 'Text Announcement';
    modalTime.textContent = time;
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeAnnouncementModal() {
    const modal = document.getElementById('announcementModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('announcementModal');
    const modalBackdrop = document.getElementById('announcementModalBackdrop');
    
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', closeAnnouncementModal);
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAnnouncementModal();
        }
    });
});
</script>

<!-- Announcement Modal -->
<div class="announcement-modal" id="announcementModal">
    <div class="announcement-modal-backdrop" id="announcementModalBackdrop"></div>
    <div class="announcement-modal-content">
        <div class="announcement-modal-header">
            <div class="announcement-modal-badge" id="announcementModalType">
                <i class="bi bi-megaphone"></i>
            </div>
            <button class="announcement-modal-close" onclick="closeAnnouncementModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="announcement-modal-body">
            <h3 class="announcement-modal-title" id="announcementModalTitle"></h3>
            <div class="announcement-modal-time" id="announcementModalTime"></div>
            <div class="announcement-modal-text" id="announcementModalContent"></div>
        </div>
    </div>
</div>
@endsection
