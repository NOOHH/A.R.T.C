@extends('admin.admin-dashboard-layout')

@section('title', 'Programs')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-programs.css') }}">
<style>
  /* Main wrapper */
  .main-content-wrapper {
    align-items: flex-start !important;
  }

  /* Container */
  .programs-container {
    background: #fff;
    padding: 40px 20px 60px;
    margin: 40px 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  /* Header */
  .programs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 0 10px;
  }
  .programs-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  .header-buttons {
    display: flex;
    gap: 15px;
    align-items: center;
  }



  .archive-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .archive-btn:hover {
    background: #5a6268;
    transform: scale(1.05);
  }

  .archive-btn.unarchive {
    background: #28a745;
  }

  .archive-btn.unarchive:hover {
    background: #218838;
  }

  .view-archived-btn {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }

  .view-archived-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    color: white;
    text-decoration: none;
  }

  /* Programs grid */
  .programs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
  }

  /* Main Dashboard Grid */
  .main-dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 450px;
    gap: 30px;
    margin-top: 30px;
  }

  .left-column {
    min-height: 600px;
  }

  .right-column {
    display: flex;
    flex-direction: column;
    gap: 25px;
  }

  /* Analytics Cards */
  .analytics-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
  }

  .analytics-card {
    padding: 25px;
    border-radius: 15px;
    color: white;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .analytics-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
  }

  .analytics-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
  }

  .card-icon {
    font-size: 2.5rem;
    z-index: 1;
  }

  .card-content {
    flex: 1;
    z-index: 1;
  }

  .card-number {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 5px;
  }

  .card-label {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 8px;
  }

  .card-trend {
    font-size: 0.85rem;
    opacity: 0.8;
  }

  .trend-up { color: #90EE90; }
  .trend-down { color: #FFB6C1; }
  .trend-neutral { color: rgba(255,255,255,0.7); }

  /* Right Column Panels */
  .quick-stats-panel, .chart-panel, .activities-panel, .assignment-panel {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
  }

  .panel-header {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #e1e5e9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .panel-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 600;
  }

  .view-all-link {
    color: #667eea;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .view-all-link:hover {
    text-decoration: underline;
  }

  /* Quick Stats */
  .stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1px;
    background: #e1e5e9;
  }

  .stat-item {
    background: white;
    padding: 20px;
    text-align: center;
  }

  .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
  }

  .stat-label {
    font-size: 0.85rem;
    color: #6c757d;
  }

  /* Chart Panel */
  .chart-controls {
    display: flex;
    gap: 10px;
  }

  .chart-btn {
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .chart-btn.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
  }

  .chart-container {
    padding: 25px;
    height: 250px;
  }

  /* Activities Panel */
  .activities-list {
    padding: 0;
    max-height: 300px;
    overflow-y: auto;
  }

  .activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 25px;
    border-bottom: 1px solid #f1f3f4;
  }

  .activity-item:last-child {
    border-bottom: none;
  }

  .activity-icon {
    font-size: 1.2rem;
    width: 35px;
    height: 35px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .activity-content {
    flex: 1;
  }

  .activity-text {
    font-size: 0.9rem;
    color: #2c3e50;
    margin-bottom: 3px;
  }

  .activity-time {
    font-size: 0.8rem;
    color: #6c757d;
  }

  /* Assignment Panel */
  .assignment-form {
    padding: 25px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
  }

  .form-group select, .form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
  }

  .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .assign-btn {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .assign-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  /* Responsive */
  @media (max-width: 1200px) {
    .main-dashboard-grid {
      grid-template-columns: 1fr;
      gap: 25px;
    }
    
    .right-column {
      grid-row: 1;
    }
  }

  @media (max-width: 768px) {
    .analytics-cards {
      grid-template-columns: 1fr;
    }
    
    .stats-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Program card */
  .program-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    position: relative;
  }

  .program-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 15px;
  }

  .program-header .program-checkbox {
    margin-top: 3px;
  }
    border: 1px solid #e1e5e9;
    position: relative;
    overflow: hidden;
  }
  .program-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }
  .program-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
  }

  .program-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .program-title::before {
    content: '🎓';
    font-size: 1.2rem;
  }

  .program-stats {
    background: rgba(102, 126, 234, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
  }

  .enrollment-count {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    color: #667eea;
    font-weight: 600;
  }
  .enrollment-count::before {
    content: '👥';
    font-size: 1.1rem;
  }

  .program-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
  }

  .view-enrollees-btn, .delete-program-btn {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .view-enrollees-btn {
    background: #17a2b8;
    color: white;
  }
  .view-enrollees-btn:hover {
    background: #138496;
    transform: scale(1.05);
  }

  .delete-program-btn {
    background: #dc3545;
    color: white;
  }
  .delete-program-btn:hover {
    background: #c82333;
    transform: scale(1.05);
  }

  /* Add button */
  .add-program-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    gap: 10px;
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 100;
  }
  .add-program-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  }

  /* Modal styles */
  .modal-bg {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  .modal-bg.active {
    display: flex;
  }

  .modal {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease;
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-50px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .modal h3 {
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-size: 1.5rem;
    text-align: center;
  }

  .modal input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
  }

  .modal input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
  }

  .cancel-btn, .add-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1rem;
  }

  .cancel-btn {
    background: #6c757d;
    color: white;
  }
  .cancel-btn:hover {
    background: #5a6268;
  }

  .add-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  .add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  /* Empty state */
  .no-programs {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }
  .no-programs::before {
    content: '🎓';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }

  /* Messages */
  .success-message, .error-message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
  }

  .success-message {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
  }

  .error-message {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
  }

  .error-message ul {
    margin: 0;
    padding-left: 20px;
  }

  /* Enrollments modal specific styles */
  #enrollmentsModal .modal {
    max-width: 600px;
  }

  #enrollmentsList {
    max-height: 400px;
    overflow-y: auto;
    padding: 0;
    margin: 15px 0;
    list-style: none;
  }

  #enrollmentsList li {
    padding: 12px 15px;
    background: #f8f9fa;
    margin-bottom: 8px;
    border-radius: 8px;
    border-left: 3px solid #667eea;
  }

  .loading {
    text-align: center;
    color: #6c757d;
    font-style: italic;
  }

  /* Batch program modal styles */
  .batch-program-item {
    padding: 20px;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    margin-bottom: 20px;
    position: relative;
    background: #f8f9fa;
  }

  .batch-program-item h4 {
    color: #667eea;
    margin: 0 0 15px 0;
    font-weight: 600;
  }

  .remove-program-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .remove-program-btn:hover {
    background: #c82333;
  }

  .add-another-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    width: 100%;
  }

  .add-another-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
  }
</style>
@endpush

@section('content')
<!-- Display messages -->
@if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="error-message">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="error-message">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Analytics Cards Section -->
<div class="analytics-cards">
    <div class="analytics-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-icon">🎓</div>
        <div class="card-content">
            <div class="card-number">{{ $totalPrograms ?? 0 }}</div>
            <div class="card-label">Total Programs</div>
            <div class="card-trend">
                @if(($newProgramsThisMonth ?? 0) > 0)
                    <span class="trend-up">↗ +{{ $newProgramsThisMonth }} this month</span>
                @else
                    <span class="trend-neutral">→ No change</span>
                @endif
            </div>
        </div>
    </div>

    <div class="analytics-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="card-icon">👥</div>
        <div class="card-content">
            <div class="card-number">{{ $totalEnrollments ?? 0 }}</div>
            <div class="card-label">Total Enrollments</div>
            <div class="card-trend">
                @if(($newEnrollmentsThisWeek ?? 0) > 0)
                    <span class="trend-up">↗ +{{ $newEnrollmentsThisWeek }} this week</span>
                @else
                    <span class="trend-neutral">→ No change</span>
                @endif
            </div>
        </div>
    </div>

    <div class="analytics-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="card-icon">📚</div>
        <div class="card-content">
            <div class="card-number">{{ $activePrograms ?? 0 }}</div>
            <div class="card-label">Active Programs</div>
            <div class="card-trend">
                @if(($archivedPrograms ?? 0) > 0)
                    <span class="trend-down">📁 {{ $archivedPrograms }} archived</span>
                @else
                    <span class="trend-neutral">→ All active</span>
                @endif
            </div>
        </div>
    </div>

    <div class="analytics-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="card-icon">📈</div>
        <div class="card-content">
            <div class="card-number">{{ number_format($avgEnrollmentPerProgram ?? 0, 1) }}</div>
            <div class="card-label">Avg Enrollment/Program</div>
            <div class="card-trend">
                @if(($completionRate ?? 0) > 0)
                    <span class="trend-up">✓ {{ $completionRate }}% completion</span>
                @else
                    <span class="trend-neutral">→ No data</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="main-dashboard-grid">
    <!-- Left Column -->
    <div class="left-column">
        <!-- Programs Management -->
        <div class="programs-container">
            <div class="programs-header">
                <h1>Programs Management</h1>
                <div class="header-buttons">
                    <a href="{{ route('admin.programs.archived') }}" class="view-archived-btn">
                        📁 View Archived
                    </a>
                    <button type="button" class="add-module-btn batch-upload-btn" id="showBatchModal">
                        📤 Batch Upload
                    </button>
                </div>
            </div>

    <!-- Main Dashboard Grid -->
    <div class="main-dashboard-grid">
        <!-- Left Column - Programs -->
        <div class="left-column">
            <!-- Programs Display -->
            <div class="programs-grid">
                @forelse($programs as $program)
                    <div class="program-card">
                        <div class="program-title">{{ $program->program_name }}</div>
                        
                        <div class="program-stats">
                            <div class="enrollment-count">
                                Enrolled Students: {{ $program->enrollments->count() }}
                            </div>
                        </div>

                        <div class="program-actions">
                            <button type="button" class="view-enrollees-btn" data-program-id="{{ $program->program_id }}">
                                👥 View Enrollees
                            </button>
                            <button type="button" class="archive-btn" data-program-id="{{ $program->program_id }}">
                                📁 Archive
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="no-programs">
                        No programs found.<br>
                        <small>Click "Add Program" to create your first program.</small>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column - Analytics & Tools -->
        <div class="right-column">
            <!-- Quick Stats -->
            <div class="quick-stats-panel">
                <div class="panel-header">
                    <h3>📊 Quick Stats</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ $mostPopularProgram->program_name ?? 'N/A' }}</div>
                        <div class="stat-label">Most Popular Program</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $mostPopularProgram->enrollments_count ?? 0 }}</div>
                        <div class="stat-label">Enrollments</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $recentProgramsCount ?? 0 }}</div>
                        <div class="stat-label">Added This Week</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($avgProgramRating ?? 0, 1) }}</div>
                        <div class="stat-label">Avg Rating</div>
                    </div>
                </div>
            </div>

            <!-- Program Analytics Chart -->
            <div class="chart-panel">
                <div class="panel-header">
                    <h3>📈 Program Analytics</h3>
                    <div class="chart-controls">
                        <button class="chart-btn active" data-chart="enrollments">Enrollments</button>
                        <button class="chart-btn" data-chart="completion">Completion</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="programChart"></canvas>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="activities-panel">
                <div class="panel-header">
                    <h3>🔄 Recent Activities</h3>
                    <a href="#" class="view-all-link">View all</a>
                </div>
                <div class="activities-list">
                    @forelse($recentActivities ?? [] as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">{{ $activity['icon'] }}</div>
                        <div class="activity-content">
                            <div class="activity-text">{{ $activity['text'] }}</div>
                            <div class="activity-time">{{ $activity['time'] }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="activity-item">
                        <div class="activity-icon">📚</div>
                        <div class="activity-content">
                            <div class="activity-text">New program "Advanced Programming" created</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">👥</div>
                        <div class="activity-content">
                            <div class="activity-text">25 students enrolled in "Web Development"</div>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">🎓</div>
                        <div class="activity-content">
                            <div class="activity-text">Program "Data Science" updated</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Course Assignment Form -->
            <div class="assignment-panel">
                <div class="panel-header">
                    <h3>👨‍🎓 Course Assignment</h3>
                </div>
                <form id="courseAssignmentForm" class="assignment-form">
                    @csrf
                    <div class="form-group">
                        <label for="student_select">Select Student:</label>
                        <select id="student_select" name="student_id" required>
                            <option value="">Choose a student...</option>
                            @foreach($students ?? [] as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="program_select">Select Program:</label>
                        <select id="program_select" name="program_id" required>
                            <option value="">Choose a program...</option>
                            @foreach($programs ?? [] as $program)
                                <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assignment_notes">Notes (optional):</label>
                        <textarea id="assignment_notes" name="notes" rows="3" placeholder="Additional notes about this assignment..."></textarea>
                    </div>
                    <button type="submit" class="assign-btn">
                        ✓ Assign Program
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Program Button -->
<button class="add-program-btn" id="showAddModal">
    <span style="font-size:1.3em;">&#43;</span> Add Program
</button>

<!-- Add Program Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <h3>Create New Program</h3>
        <form action="{{ route('admin.programs.store') }}" method="POST">
            @csrf
            <input type="text" name="program_name" placeholder="Program Name" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Program</button>
            </div>
        </form>
    </div>
</div>

<!-- Enrollments Modal -->
<div class="modal-bg" id="enrollmentsModal">
    <div class="modal">
        <h3>Enrolled Students</h3>
        <div class="loading" id="loadingMessage">Loading enrollments...</div>
        <ul id="enrollmentsList" style="display: none;"></ul>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" id="closeEnrollmentsModal">Close</button>
        </div>
    </div>
</div>

<!-- Batch Upload Modal -->
<div class="modal-bg" id="batchModalBg">
    <div class="modal" style="max-width: 800px; width: 90vw; max-height: 90vh; overflow-y: auto;">
        <h3>Batch Upload Programs</h3>
        <form action="{{ route('admin.programs.batch-store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="batchProgramForm">
            @csrf
            
            <div id="batchProgramsContainer">
                <!-- Initial program item -->
                <div class="batch-program-item" data-index="0">
                    <button type="button" class="remove-program-btn" onclick="removeBatchProgram(0)">×</button>
                    <h4>Program 1</h4>
                    
                    <input type="text" name="programs[0][program_name]" placeholder="Program Name" required>
                    <textarea name="programs[0][program_description]" placeholder="Program Description (optional)"></textarea>
                </div>
            </div>

            <button type="button" class="add-another-btn" id="addAnotherProgram">+ Add Another Program</button>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelBatchModal">Cancel</button>
                <button type="submit" class="add-btn">Create All Programs</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize Analytics Chart
    const ctx = document.getElementById('programChart');
    if (ctx) {
        const programChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
                datasets: [{
                    label: 'Enrollments',
                    data: {!! json_encode($enrollmentData ?? [12, 19, 15, 25, 22, 30]) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }, {
                    label: 'Completions',
                    data: {!! json_encode($completionData ?? [8, 12, 10, 18, 15, 22]) !!},
                    borderColor: '#764ba2',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#764ba2',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    hidden: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });

        // Chart controls
        document.querySelectorAll('.chart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const chartType = this.getAttribute('data-chart');
                if (chartType === 'enrollments') {
                    programChart.data.datasets[0].hidden = false;
                    programChart.data.datasets[1].hidden = true;
                } else if (chartType === 'completion') {
                    programChart.data.datasets[0].hidden = true;
                    programChart.data.datasets[1].hidden = false;
                }
                programChart.update();
            });
        });
    }

    // Course Assignment Form
    const assignmentForm = document.getElementById('courseAssignmentForm');
    if (assignmentForm) {
        assignmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.assign-btn');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = '⏳ Assigning...';
            submitBtn.disabled = true;
            
            fetch('/admin/programs/assign', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification('Program assigned successfully!', 'success');
                    assignmentForm.reset();
                } else {
                    showNotification(data.message || 'Error assigning program', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while assigning the program', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Add Program Modal
    const showAddModal = document.getElementById('showAddModal');
    const addModalBg = document.getElementById('addModalBg');
    const cancelAddModal = document.getElementById('cancelAddModal');

    showAddModal.addEventListener('click', function() {
        addModalBg.classList.add('active');
    });

    cancelAddModal.addEventListener('click', function() {
        addModalBg.classList.remove('active');
    });

    addModalBg.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

    // Batch Upload Modal
    const showBatchModal = document.getElementById('showBatchModal');
    const batchModalBg = document.getElementById('batchModalBg');
    const cancelBatchModal = document.getElementById('cancelBatchModal');

    if (showBatchModal && batchModalBg) {
        showBatchModal.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Batch upload button clicked');
            batchModalBg.classList.add('active');
            batchModalBg.style.display = 'flex';
        });

        cancelBatchModal.addEventListener('click', function() {
            batchModalBg.classList.remove('active');
            batchModalBg.style.display = 'none';
        });

        batchModalBg.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                this.style.display = 'none';
            }
        });
    }

    // Add another program functionality
    let programIndex = 1;
    const addAnotherProgram = document.getElementById('addAnotherProgram');
    const batchProgramsContainer = document.getElementById('batchProgramsContainer');

    if (addAnotherProgram) {
        addAnotherProgram.addEventListener('click', function() {
            const newProgramItem = document.createElement('div');
            newProgramItem.className = 'batch-program-item';
            newProgramItem.setAttribute('data-index', programIndex);
            newProgramItem.innerHTML = `
                <button type="button" class="remove-program-btn" onclick="removeBatchProgram(${programIndex})">×</button>
                <h4>Program ${programIndex + 1}</h4>
                
                <input type="text" name="programs[${programIndex}][program_name]" placeholder="Program Name" required>
                <textarea name="programs[${programIndex}][program_description]" placeholder="Program Description (optional)"></textarea>
            `;
            batchProgramsContainer.appendChild(newProgramItem);
            programIndex++;
        });
    }

    // Enrollments Modal
    const enrollmentsModal = document.getElementById('enrollmentsModal');
    const closeEnrollmentsModal = document.getElementById('closeEnrollmentsModal');
    const enrollmentsList = document.getElementById('enrollmentsList');
    const loadingMessage = document.getElementById('loadingMessage');

    // View Enrollees functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-enrollees-btn')) {
            const programId = e.target.dataset.programId;
            
            // Show modal and loading
            enrollmentsModal.classList.add('active');
            loadingMessage.style.display = 'block';
            enrollmentsList.style.display = 'none';
            enrollmentsList.innerHTML = '';

            // Fetch enrollments
            fetch(`/admin/programs/${programId}/enrollments`)
                .then(response => response.json())
                .then(data => {
                    console.log('Enrollment data received:', data);
                    loadingMessage.style.display = 'none';
                    enrollmentsList.style.display = 'block';
                    
                    if (data.enrollments && data.enrollments.length > 0) {
                        enrollmentsModal.querySelector('h3').textContent = 
                            `${data.program_name} - ${data.total_enrollments} Enrolled Students`;
                        
                        data.enrollments.forEach(enrollment => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <div style="font-weight: bold; font-size: 1.1em; color: #2c3e50; margin-bottom: 5px;">
                                    👤 ${enrollment.student_name || 'Unknown Student'}
                                </div>
                                <div style="font-size: 0.9em; color: #667eea; margin-bottom: 3px;">
                                    📧 ${enrollment.email || 'No email available'}
                                </div>
                                <div style="font-size: 0.85em; color: #6c757d;">
                                    📅 Enrolled: ${enrollment.enrolled_at || 'Unknown date'}
                                </div>
                            `;
                            enrollmentsList.appendChild(li);
                        });
                    } else {
                        enrollmentsModal.querySelector('h3').textContent = 'No Students Enrolled';
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <div style="text-align: center; font-style: italic; color: #6c757d; padding: 20px;">
                                No students enrolled in this program yet.
                            </div>
                        `;
                        enrollmentsList.appendChild(li);
                    }
                })
                .catch(error => {
                    console.error('Error fetching enrollments:', error);
                    loadingMessage.textContent = 'Error loading enrollments.';
                });
        }
    });

    closeEnrollmentsModal.addEventListener('click', function() {
        enrollmentsModal.classList.remove('active');
        enrollmentsModal.querySelector('h3').textContent = 'Enrolled Students';
        enrollmentsList.innerHTML = '';
        loadingMessage.textContent = 'Loading enrollments...';
    });

    enrollmentsModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            enrollmentsModal.querySelector('h3').textContent = 'Enrolled Students';
            enrollmentsList.innerHTML = '';
            loadingMessage.textContent = 'Loading enrollments...';
        }
    });

    // Archive functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('archive-btn')) {
            const programId = e.target.dataset.programId;
            const isArchived = e.target.textContent.includes('Unarchive');
            
            if (confirm(isArchived ? 'Are you sure you want to unarchive this program?' : 'Are you sure you want to archive this program?')) {
                fetch(`/admin/programs/${programId}/toggle-archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Refresh the page to update the UI
                    } else {
                        alert('Error: ' + (data.message || 'Something went wrong'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while archiving the program.');
                });
            }
        }
    });

    // Batch selection functionality
    const selectAllCheckbox = document.getElementById('selectAllPrograms');
    const programCheckboxes = document.querySelectorAll('.program-checkbox');
    const batchDeleteBtn = document.getElementById('batchDeletePrograms');

    selectAllCheckbox.addEventListener('change', function() {
        programCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBatchDeleteButton();
    });

    programCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.program-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === programCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < programCheckboxes.length;
            toggleBatchDeleteButton();
        });
    });


});

// Global function for removing batch program items
function removeBatchProgram(index) {
    const programItem = document.querySelector(`[data-index="${index}"]`);
    if (programItem) {
        programItem.remove();
    }
}
</script>
@endpush
