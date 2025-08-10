@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Calendar')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student/student-calendar.css') }}">
<style>
.calendar-day {
    height: 100%;
    min-height: 120px;
    border: 1px solid #e9ecef;
    padding: 8px;
    position: relative;
    background: white;
    transition: all 0.2s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.today {
    background-color: #e3f2fd;
    border-color: #2196f3;
    font-weight: bold;
}

.calendar-day.has-events {
    background-color: #fff3cd;
    border-color: #ffc107;
}

.day-number {
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 2px;
    color: #495057;
}

.event-item {
    font-size: 0.7rem;
    padding: 1px 4px;
    margin: 1px 0;
    border-radius: 2px;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    max-width: 100%;
}

.event-item:hover {
    opacity: 0.8;
}

.event-meeting { background-color: #007bff; }
.event-assignment { background-color: #fd7e14; }
.event-quiz { background-color: #dc3545; }
.event-announcement { background-color: #20c997; }

.event-counter {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background: #6c757d;
    color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    font-size: 0.6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 3px;
}

.today-schedule-item {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    border-left: 4px solid;
}

.schedule-meeting { 
    background-color: #e3f2fd; 
    border-left-color: #2196f3; 
}

.schedule-assignment { 
    background-color: #fff3e0; 
    border-left-color: #ff9800; 
}

.schedule-announcement { 
    background-color: #e8f5e8; 
    border-left-color: #4caf50; 
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Calendar grid fixes */
#calendarDays {
    overflow: hidden !important;
}

#calendarDays .row {
    margin: 0 !important;
    min-height: 120px;
    overflow: hidden;
}

#calendarDays .col {
    padding: 0 !important;
    border-right: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    overflow: hidden;
    flex: 1;
    display: flex;
    flex-direction: column;
}

#calendarDays .col:last-child {
    border-right: none;
}

/* Weekday header alignment */
.calendar-weekdays .col {
    padding: 0.5rem !important;
    text-align: center;
    font-weight: bold;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-right: 1px solid #e9ecef;
}

.calendar-weekdays .col:last-child {
    border-right: none;
}

.calendar-day.empty {
    background-color: #f8f9fa;
    color: #adb5bd;
}

.calendar-day.empty .day-number {
    color: #adb5bd;
}

/* Ensure no scrollbars anywhere in the calendar */
.card-body {
    overflow: hidden !important;
}

.container-fluid {
    padding-left: 0 !important;
    padding-right: 0 !important;
    overflow: hidden !important;
}
.container-fluid .row {
  margin-left: 0 !important;
  margin-right: 0 !important;
}

.container-fluid .row > [class*="col-"] {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
/* Custom Modal Styles - Full page overlay */
.custom-modal {
    display: none;
    position: fixed;
    z-index: 999999;
    left: 0;
    top: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

.custom-modal.show {
    display: flex !important;
    align-items: center;
    justify-content: center;
}

.custom-modal-content {
    background-color: #fff;
    margin: auto;
    padding: 0;
    border: none;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    animation: modalSlideIn 0.3s ease-out;
    position: relative;
    z-index: 1000000;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.custom-modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.custom-modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
}

.custom-modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    font-weight: bold;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.custom-modal-close:hover {
    background-color: #e9ecef;
    color: #333;
}

.custom-modal-body {
    padding: 1.5rem;
}

.custom-modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0 0 8px 8px;
}

.custom-modal-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.custom-modal-btn-secondary {
    background-color: #6c757d;
    color: white;
}

.custom-modal-btn-secondary:hover {
    background-color: #5a6268;
}

.custom-modal-btn-primary {
    background-color: #007bff;
    color: white;
}

.custom-modal-btn-primary:hover {
    background-color: #0056b3;
}

/* Ensure no other elements interfere */
.calendar-day,
.event-item,
.event-counter {
    z-index: 1 !important;
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden !important;
    position: fixed !important;
    width: 100% !important;
}

/* Remove white gap around the logo image */
.header-left img {
    margin: 0 !important;
    padding: 0 !important;
    display: block !important;
}

.brand-link {
    margin: 0 !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
}

.brand-text {
    margin: 0 !important;
    padding: 0 !important;
}

/* kill container padding */
.container-fluid {
  padding-left: 0 !important;
  padding-right: 0 !important;
}

/* kill the row's negative margins */
.container-fluid .row {
  margin-left: 0 !important;
  margin-right: 0 !important;
}

/* kill each column's built‑in padding */
.container-fluid .row > [class*="col-"] {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Main Calendar -->
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar3 me-2"></i>Academic Calendar
                    </h4>
                    <div class="d-flex align-items-center">
                        <div class="calendar-navigation">
                            <button class="btn btn-outline-light btn-sm me-2" id="prevMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="btn btn-light btn-sm me-2" id="todayBtn">Today</button>
                            <span class="current-month fw-bold" id="currentMonth">{{ date('F Y') }}</span>
                            <button class="btn btn-outline-light btn-sm ms-2" id="nextMonth">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Calendar Weekdays -->
                    <div class="row g-0 calendar-weekdays">
                        <div class="col">Sunday</div>
                        <div class="col">Monday</div>
                        <div class="col">Tuesday</div>
                        <div class="col">Wednesday</div>
                        <div class="col">Thursday</div>
                        <div class="col">Friday</div>
                        <div class="col">Saturday</div>
                    </div>
                    
                    <!-- Calendar Days -->
                    <div id="calendarDays" style="overflow: hidden;">
                        <!-- Calendar days will be generated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-3">
            
            <!-- Today's Schedule -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-day me-2"></i>Today's Schedule
                    </h5>
                </div>
                <div class="card-body" id="todaySchedule">
                    <div class="text-center">
                        <div class="loading-spinner"></div>
                        <p class="mt-2 mb-0">Loading schedule...</p>
                    </div>
                </div>
            </div>
            
            <!-- Event Legend -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Event Types
                    </h6>
                </div>
                <div class="card-body">
                    <div class="legend-item">
                        <div class="legend-color event-meeting"></div>
                        <span>Class Meetings</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color event-assignment"></div>
                        <span>Assignment Due</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color event-quiz"></div>
                        <span>Quiz Due</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color event-announcement"></div>
                        <span>Announcements</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>This Month
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Meetings:</span>
                        <span class="badge bg-primary" id="meetingCount">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Assignments:</span>
                        <span class="badge bg-warning" id="assignmentCount">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Quizzes:</span>
                        <span class="badge bg-danger" id="quizCount">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Announcements:</span>
                        <span class="badge bg-info" id="announcementCount">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Event Details Modal -->
<div id="eventModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title" id="eventModalLabel">Event Details</h5>
            <button type="button" class="custom-modal-close" id="eventModalClose" aria-label="Close">&times;</button>
        </div>
        <div class="custom-modal-body" id="eventModalBody">
            <!-- Event details will be loaded here -->
        </div>
        <div class="custom-modal-footer" id="eventModalActions">
            <button type="button" class="custom-modal-btn custom-modal-btn-secondary" id="eventModalCloseBtn">Close</button>
            <!-- Action buttons will be added based on event type -->
        </div>
    </div>
</div>

<!-- Custom Event Modal Backdrop -->
<div id="customEventModalBackdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 999998;"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Calendar page loaded, initializing...');
    
    let currentDate = new Date();
    let currentEvents = [];
    
    console.log(`📅 Current date: ${currentDate.toISOString()}`);
    console.log(`📅 Initializing calendar for: ${currentDate.getFullYear()}-${currentDate.getMonth() + 1}`);
    
    // Update calendar icon based on current day
    updateCalendarIcon();
    
    // Initialize calendar
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    loadTodaySchedule();
    
    // Navigation event listeners
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const todayBtn = document.getElementById('todayBtn');
    
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', () => {
            console.log('⬅️ Previous month clicked');
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
    } else {
        console.error('❌ Could not find prevMonth button');
    }
    
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', () => {
            console.log('➡️ Next month clicked');
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
    } else {
        console.error('❌ Could not find nextMonth button');
    }
    
    if (todayBtn) {
        todayBtn.addEventListener('click', () => {
            console.log('📅 Today button clicked');
            const today = new Date();
            currentDate = new Date(today);
            generateCalendar(today.getFullYear(), today.getMonth());
        });
    } else {
        console.error('❌ Could not find todayBtn button');
    }
    
    // Modal event listeners
    const modal = document.getElementById('eventModal');
    const modalClose = document.getElementById('eventModalClose');
    const modalCloseBtn = document.getElementById('eventModalCloseBtn');
    
    function closeModal() {
        const modal = document.getElementById('eventModal');
        if (modal) {
            closeCustomEventModal(modal);
        }
    }
    
    if (modalClose) {
        modalClose.addEventListener('click', closeModal);
    }
    
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });
    
    function generateCalendar(year, month) {
        console.log(`🗓️ Generating calendar for ${year}-${month + 1}`);
        
        const calendarDays = document.getElementById('calendarDays');
        const currentMonthElement = document.getElementById('currentMonth');
        
        if (!calendarDays) {
            console.error('❌ Could not find calendarDays element');
            return;
        }
        
        if (!currentMonthElement) {
            console.error('❌ Could not find currentMonth element');
            return;
        }
        
        // Update month display
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];
        currentMonthElement.textContent = `${monthNames[month]} ${year}`;
        
        // Get first day and number of days in month
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();
        
        console.log(`📅 Calendar info: daysInMonth=${daysInMonth}, startingDayOfWeek=${startingDayOfWeek}`);
        
        calendarDays.innerHTML = '';
        
        // Calculate number of weeks needed
        const totalCells = Math.ceil((daysInMonth + startingDayOfWeek) / 7) * 7;
        const weeksNeeded = totalCells / 7;
        
        console.log(`📊 Generating ${weeksNeeded} weeks with ${totalCells} total cells`);
        
        // Generate calendar rows
        for (let week = 0; week < weeksNeeded; week++) {
            const weekRow = document.createElement('div');
            weekRow.className = 'row g-0';
            
            for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
                const cellIndex = week * 7 + dayOfWeek;
                const dayNumber = cellIndex - startingDayOfWeek + 1;
                
                const dayCol = document.createElement('div');
                dayCol.className = 'col';
                
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                
                if (dayNumber > 0 && dayNumber <= daysInMonth) {
                    dayElement.innerHTML = `<div class="day-number">${dayNumber}</div>`;
                    
                    // Check if this is today
                    const today = new Date();
                    if (year === today.getFullYear() && 
                        month === today.getMonth() && 
                        dayNumber === today.getDate()) {
                        dayElement.classList.add('today');
                        console.log(`🎯 Today's date found: ${dayNumber}`);
                    }
                    
                    // Add click handler for day
                    dayElement.addEventListener('click', () => {
                        console.log(`🖱️ Day clicked: ${year}-${month + 1}-${dayNumber}`);
                        showDayEvents(year, month, dayNumber);
                    });
                    
                    // Store date for later event population
                    const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
                    dayElement.dataset.date = dateString;
                    console.log(`📝 Set data-date="${dateString}" for day ${dayNumber}`);
                } else {
                    dayElement.classList.add('empty');
                }
                
                dayCol.appendChild(dayElement);
                weekRow.appendChild(dayCol);
            }
            
            calendarDays.appendChild(weekRow);
        }
        
        console.log(`✅ Calendar generated with ${calendarDays.children.length} week rows`);
        
        // Load events for this month
        loadMonthEvents(year, month);
    }
    
    function loadMonthEvents(year, month) {
        console.log(`📅 Loading events for ${year}-${month + 1}`);
        
        // Load real data from the API
        fetch(`/student/calendar/events?year=${year}&month=${month + 1}`)
            .then(response => {
                console.log('📡 Events API Response status:', response.status);
                console.log('📡 Events API Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('📊 Events API Response data:', data);
                console.log('📊 Raw events array:', JSON.stringify(data.events, null, 2));
                
                if (data.success && data.events) {
                    console.log(`✅ Loaded ${data.events.length} real events from database`);
                    console.log('🔍 Event details:');
                    data.events.forEach((event, index) => {
                        console.log(`  ${index + 1}. ${event.type}: ${event.title} (${event.start})`);
                    });
                    currentEvents = data.events;
                    populateCalendarEvents();
                    updateStats(data.meta || {
                        meetings: data.events.filter(e => e.type === 'meeting').length,
                        assignments: data.events.filter(e => e.type === 'assignment').length,
                        quizzes: data.events.filter(e => e.type === 'quiz').length,
                        announcements: data.events.filter(e => e.type === 'announcement').length
                    });
                    

                } else if (data.success === false && data.message === 'Student not found') {
                    console.log('🔒 Authentication required - please log in');
                    currentEvents = [];
                    populateCalendarEvents();
                    updateStats({ meetings: 0, assignments: 0, quizzes: 0, announcements: 0 });
                } else {
                    console.log('ℹ️ No events found for this period');
                    currentEvents = [];
                    populateCalendarEvents();
                    updateStats({ meetings: 0, assignments: 0, quizzes: 0, announcements: 0 });
                }
            })
            .catch(error => {
                console.error('❌ Error loading events from API:', error);
                console.error('❌ Error details:', error.message);
                currentEvents = [];
                populateCalendarEvents();
                updateStats({ meetings: 0, assignments: 0, quizzes: 0, announcements: 0 });
            });
    }
    
    function populateCalendarEvents() {
        console.log(`🎯 Populating calendar with ${currentEvents.length} events`);
        
        // Clear existing events
        const existingDays = document.querySelectorAll('.calendar-day');
        console.log(`🧹 Clearing events from ${existingDays.length} calendar days`);
        
        existingDays.forEach(day => {
            day.classList.remove('has-events');
            const existingEvents = day.querySelectorAll('.event-item, .event-counter');
            existingEvents.forEach(event => event.remove());
        });
        
        if (currentEvents.length === 0) {
            console.log('ℹ️ No events to populate');
            return;
        }
        
        // Group events by date
        const eventsByDate = {};
        currentEvents.forEach(event => {
            const eventDate = new Date(event.start).toISOString().split('T')[0];
            if (!eventsByDate[eventDate]) {
                eventsByDate[eventDate] = [];
            }
            eventsByDate[eventDate].push(event);
        });
        
        console.log('📅 Events grouped by date:', eventsByDate);
        
        // Add events to calendar days
        Object.keys(eventsByDate).forEach(date => {
            const dayElement = document.querySelector(`[data-date="${date}"]`);
            console.log(`🔍 Looking for day element with date: ${date}`, dayElement);
            
            if (dayElement) {
                console.log(`✅ Found day element for ${date}, adding ${eventsByDate[date].length} events`);
                dayElement.classList.add('has-events');
                const events = eventsByDate[date];
                
                // Show first 3 events
                events.slice(0, 3).forEach(event => {
                    const eventElement = document.createElement('div');
                    eventElement.className = `event-item event-${event.type}`;
                    eventElement.textContent = event.title;
                    eventElement.title = event.description || event.title;
                    eventElement.addEventListener('click', (e) => {
                        e.stopPropagation();
                        console.log(`🎯 Event clicked: ${event.title}`);
                        showEventDetails(event);
                    });
                    dayElement.appendChild(eventElement);
                    console.log(`➕ Added event: ${event.title} (${event.type})`);
                });
                
                // Show counter if more than 3 events
                if (events.length > 3) {
                    const counter = document.createElement('div');
                    counter.className = 'event-counter';
                    counter.textContent = `+${events.length - 3}`;
                    counter.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showDayEvents(date);
                    });
                    dayElement.appendChild(counter);
                    console.log(`➕ Added counter: +${events.length - 3}`);
                }
            } else {
                console.warn(`⚠️ Could not find day element for date: ${date}`);
            }
        });
        
        console.log(`✅ Finished populating calendar events`);
    }
    
    function loadTodaySchedule() {
        console.log('🕐 Loading today schedule...');
        
        // Load real data from the API
        fetch('/student/calendar/today')
            .then(response => {
                console.log('📡 API Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 API Response data:', data);
                if (data.success && data.events) {
                    console.log(`✅ Found ${data.events.length} real events for today`);
                    displayTodaySchedule(data.events);
                } else if (data.success === false && data.message === 'Student not found') {
                    console.log('🔒 Authentication required - showing auth error');
                    displayAuthError();
                } else {
                    console.log('ℹ️ No events found for today');
                    displayTodaySchedule([]);
                }
            })
            .catch(error => {
                console.error('❌ Error loading today schedule:', error);
                displayTodaySchedule([]);
            });
    }
    
    function displayTodaySchedule(events) {
        console.log('📋 Displaying today schedule with events:', events);
        const container = document.getElementById('todaySchedule');
        
        if (!container) {
            console.error('❌ Could not find todaySchedule container');
            return;
        }
        
        if (events.length === 0) {
            console.log('ℹ️ No events to display');
            container.innerHTML = `
                <div class="text-center py-3">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">No events scheduled for today</p>
                    <small class="text-muted">Check back tomorrow for new schedule</small>
                </div>
            `;
            return;
        }
        
        const scheduleHtml = events.map(event => {
            const scheduleClass = `schedule-${event.type}`;
            const time = event.time || new Date(event.start).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            return `
                <div class="today-schedule-item ${scheduleClass}" onclick="showEventDetails(${JSON.stringify(event).replace(/"/g, '&quot;')})">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${event.title}</h6>
                            <small class="text-muted">${time}</small>
                            ${event.program ? `<br><small class="text-muted">${event.program}</small>` : ''}
                        </div>
                        ${event.type === 'meeting' && event.meeting_url ? 
                            `<a href="${event.meeting_url}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="bi bi-camera-video"></i>
                            </a>` : ''}
                    </div>
                </div>
            `;
        }).join('');
        
        container.innerHTML = scheduleHtml;
    }
    
    function displayAuthError() {
        const container = document.getElementById('todaySchedule');
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-shield-exclamation text-warning" style="font-size: 2.5rem;"></i>
                <h6 class="mt-3 mb-2 text-warning">Authentication Required</h6>
                <p class="text-muted mb-3">Please log in to view your schedule</p>
                <a href="/login" class="btn btn-primary btn-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
            </div>
        `;
    }
    
    function updateStats(meta) {
        if (meta) {
            document.getElementById('meetingCount').textContent = meta.meetings || 0;
            document.getElementById('assignmentCount').textContent = meta.assignments || 0;
            document.getElementById('quizCount').textContent = meta.quizzes || 0;
            document.getElementById('announcementCount').textContent = meta.announcements || 0;
        }
    }
    
    function showEventDetails(event) {
        console.log(`🎯 showEventDetails called for event:`, event);
        
        const modal = document.getElementById('eventModal');
        const modalTitle = document.getElementById('eventModalLabel');
        const modalBody = document.getElementById('eventModalBody');
        const modalActions = document.getElementById('eventModalActions');
        
        if (!modal || !modalTitle || !modalBody || !modalActions) {
            console.error('❌ Could not find modal elements');
            return;
        }
        
        modalTitle.textContent = event.title;
        
        let bodyHtml = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Type:</strong> ${event.type.charAt(0).toUpperCase() + event.type.slice(1)}
                </div>
                <div class="col-md-6">
                    <strong>Date:</strong> ${new Date(event.start).toLocaleDateString()}
                </div>
            </div>
            <hr>
        `;
        
        if (event.description) {
            bodyHtml += `<p><strong>Description:</strong><br>${event.description}</p>`;
        }
        
        // Add type-specific information
        if (event.type === 'meeting') {
            bodyHtml += `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Program:</strong> ${event.program || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Professor:</strong> ${event.professor || 'N/A'}
                    </div>
                </div>
            `;
            
            modalActions.innerHTML = event.meeting_url ? 
                `<a href="${event.meeting_url}" target="_blank" class="custom-modal-btn custom-modal-btn-primary">
                    <i class="bi bi-camera-video"></i>Join Meeting
                </a>` : '';
        } else if (event.type === 'assignment') {
            bodyHtml += `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Program:</strong> ${event.program || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Professor:</strong> ${event.professor || 'N/A'}
                    </div>
                </div>
            `;
            
            // Extract assignment ID and program info for intelligent redirect
            const assignmentId = event.id.replace('assignment_', '');
            const programName = event.program || '';
            const courseId = event.course_id || '';
            
            // Add visit button for assignments - redirect to specific course
            modalActions.innerHTML = `
                <button type="button" class="custom-modal-btn custom-modal-btn-primary" onclick="redirectToAssignmentFromCalendar('${assignmentId}', '${programName}', '${courseId}')">
                    <i class="bi bi-file-earmark-text"></i>View Assignment
                </button>
            `;
        } else if (event.type === 'quiz') {
            bodyHtml += `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Program:</strong> ${event.program || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Professor:</strong> ${event.professor || 'N/A'}
                    </div>
                </div>
            `;
            
            // Extract quiz ID for redirection
            const quizId = event.id.replace('quiz_', '');
            
            // Add visit button for quizzes - redirect to content view
            modalActions.innerHTML = `
                <button type="button" class="custom-modal-btn custom-modal-btn-danger" onclick="redirectToQuizFromCalendar('${quizId}')">
                    <i class="bi bi-clipboard-check"></i>Take Quiz
                </button>
            `;
        } else if (event.type === 'announcement') {
            bodyHtml += `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Posted by:</strong> ${event.professor || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong> ${event.announcement_type || 'General'}
                    </div>
                </div>
            `;
            
            // Extract announcement ID and data for modal opening
            const announcementId = event.id.replace('announcement_', '');
            const announcementTitle = event.title || '';
            const announcementContent = event.content || event.description || '';
            const announcementType = event.announcement_type || 'general';
            const announcementTime = event.time || '';
            
            // Add visit button for announcements - redirect to dashboard and open modal
            modalActions.innerHTML = `
                <a href="/student/dashboard" class="custom-modal-btn custom-modal-btn-primary" onclick="redirectToAnnouncementFromCalendar('${announcementId}', '${announcementTitle.replace(/'/g, "\\'")}', '${announcementContent.replace(/'/g, "\\'")}', '${announcementType}', '${announcementTime}')">
                    <i class="bi bi-megaphone"></i>View Announcement
                </a>
            `;
        } else if (event.type === 'lesson') {
            bodyHtml += `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Program:</strong> ${event.program || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Module:</strong> ${event.module || 'N/A'}
                    </div>
                </div>
            `;
            
            // Extract lesson details for intelligent redirect
            const lessonId = event.id.replace('lesson_', '');
            const programId = event.program_id || '';
            const moduleId = event.module_id || '';
            const courseId = event.course_id || '';
            
            // Add visit button for lessons - redirect to specific course content
            modalActions.innerHTML = `
                <a href="/student/enrolled-courses" class="custom-modal-btn custom-modal-btn-primary" onclick="redirectToLessonFromCalendar('${lessonId}', '${programId}', '${moduleId}', '${courseId}')">
                    <i class="bi bi-book"></i>View Lesson
                </a>
            `;
        }
        
        modalBody.innerHTML = bodyHtml;
        
        // Show custom modal
        showCustomEventModal(modal);
        
        console.log('✅ Custom modal shown');
    }
    
    function showDayEvents(dateOrYear, month, day) {
        console.log(`📅 showDayEvents called with:`, { dateOrYear, month, day });
        
        let targetDate;
        if (typeof dateOrYear === 'string') {
            targetDate = dateOrYear;
        } else {
            targetDate = `${dateOrYear}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        }
        
        console.log(`🎯 Looking for events on: ${targetDate}`);
        console.log(`📊 Current events count: ${currentEvents.length}`);
        
        const dayEvents = currentEvents.filter(event => {
            const eventDate = new Date(event.start).toISOString().split('T')[0];
            const matches = eventDate === targetDate;
            if (matches) {
                console.log(`✅ Found event for ${targetDate}: ${event.title}`);
            }
            return matches;
        });
        
        console.log(`📋 Found ${dayEvents.length} events for ${targetDate}`);
        
        const modal = document.getElementById('eventModal');
        const modalTitle = document.getElementById('eventModalLabel');
        const modalBody = document.getElementById('eventModalBody');
        const modalActions = document.getElementById('eventModalActions');
        
        if (!modal || !modalTitle || !modalBody || !modalActions) {
            console.error('❌ Could not find modal elements');
            return;
        }
        
        modalTitle.textContent = `Events for ${new Date(targetDate).toLocaleDateString()}`;
        modalActions.innerHTML = '';
        
        if (dayEvents.length === 0) {
            modalBody.innerHTML = '<p class="text-muted">No events scheduled for this day.</p>';
            console.log('ℹ️ No events found for this day');
        } else {
            const eventsHtml = dayEvents.map(event => `
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${event.title}</h6>
                            <small class="text-muted">${event.type.charAt(0).toUpperCase() + event.type.slice(1)}</small>
                            ${event.description ? `<p class="mt-2 mb-0">${event.description}</p>` : ''}
                        </div>
                        <button class="btn btn-outline-primary btn-sm" onclick="showEventDetails(${JSON.stringify(event).replace(/"/g, '&quot;')})">
                            View Details
                        </button>
                    </div>
                </div>
            `).join('');
            
            modalBody.innerHTML = eventsHtml;
            console.log(`📝 Generated HTML for ${dayEvents.length} events`);
        }
        
        // Show custom modal
        showCustomEventModal(modal);
        
        console.log('✅ Custom modal shown');
    }
    
    // Make showEventDetails available globally
    window.showEventDetails = showEventDetails;
    
    // Custom Event Modal Functions
    function showCustomEventModal(modalElement) {
        console.log('🎯 showCustomEventModal called');
        
        // Remove any existing backdrops
        removeAllEventBackdrops();
        
        // Create and show backdrop
        const backdrop = document.getElementById('customEventModalBackdrop');
        if (backdrop) {
            backdrop.style.display = 'block';
            backdrop.style.zIndex = '999998';
        }
        
        // Hide navbar and sidebar
        const navbar = document.querySelector('.main-header');
        const sidebar = document.querySelector('#studentSidebar');
        const wrapper = document.querySelector('.main-content-area');
        
        if (navbar) {
            navbar.style.display = 'none';
            console.log('✅ Navbar hidden');
        }
        
        if (sidebar) {
            sidebar.style.display = 'none';
            console.log('✅ Sidebar hidden');
        }
        
        if (wrapper) {
            wrapper.style.marginLeft = '0';
            console.log('✅ Wrapper margins reset');
        }
        
        // Apply fullscreen modal styles
        modalElement.style.cssText = `
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 999999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(0, 0, 0, 0.6) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            margin: 0 !important;
            padding: 20px !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        `;
        
        // Style the modal content
        const modalContent = modalElement.querySelector('.custom-modal-content');
        if (modalContent) {
            modalContent.style.cssText = `
                background: white !important;
                border-radius: 12px !important;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4) !important;
                max-width: 90% !important;
                max-height: 90vh !important;
                width: auto !important;
                margin: 0 !important;
                overflow: hidden !important;
                animation: modalSlideIn 0.3s ease-out !important;
            `;
        }
        
        // Show modal
        modalElement.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-open');
        
        console.log('✅ Custom event modal displayed');
    }
    
    function closeCustomEventModal(modalElement) {
        console.log('🎯 closeCustomEventModal called');
        
        // Hide modal
        modalElement.style.display = 'none';
        modalElement.style.cssText = '';
        
        // Reset modal content styles
        const modalContent = modalElement.querySelector('.custom-modal-content');
        if (modalContent) {
            modalContent.style.cssText = '';
        }
        
        // Remove backdrop
        const backdrop = document.getElementById('customEventModalBackdrop');
        if (backdrop) {
            backdrop.style.display = 'none';
        }
        
        // Restore navbar and sidebar
        const navbar = document.querySelector('.main-header');
        const sidebar = document.querySelector('#studentSidebar');
        const wrapper = document.querySelector('.main-content-area');
        
        if (navbar) {
            navbar.style.display = '';
            console.log('✅ Navbar restored');
        }
        
        if (sidebar) {
            sidebar.style.display = '';
            console.log('✅ Sidebar restored');
        }
        
        if (wrapper) {
            wrapper.style.marginLeft = '';
            console.log('✅ Wrapper margins restored');
        }
        
        // Restore body scroll
        document.body.style.overflow = '';
        document.body.classList.remove('modal-open');
        
        console.log('✅ Custom event modal closed');
    }
    
    function removeAllEventBackdrops() {
        console.log('🧹 Removing all event modal backdrops');
        
        // Remove custom backdrop
        const customBackdrop = document.getElementById('customEventModalBackdrop');
        if (customBackdrop) {
            customBackdrop.style.display = 'none';
        }
        
        // Remove any Bootstrap backdrops
        const bootstrapBackdrops = document.querySelectorAll('.modal-backdrop');
        bootstrapBackdrops.forEach(backdrop => {
            backdrop.remove();
        });
        
        console.log('✅ All event modal backdrops removed');
    }
    
    // Function to update calendar icon based on current day
    function updateCalendarIcon() {
        const today = new Date();
        const dayOfWeek = today.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        
        const dayIcons = {
            0: 'bi-calendar-sun',      // Sunday
            1: 'bi-calendar-week',     // Monday
            2: 'bi-calendar-week',     // Tuesday
            3: 'bi-calendar-week',     // Wednesday
            4: 'bi-calendar-week',     // Thursday
            5: 'bi-calendar-week',     // Friday
            6: 'bi-calendar-sun'       // Saturday
        };
        
        const calendarIcon = document.querySelector('.bi-calendar-day');
        if (calendarIcon) {
            calendarIcon.className = `bi ${dayIcons[dayOfWeek]} me-2`;
            console.log(`📅 Updated calendar icon to: ${dayIcons[dayOfWeek]} for day ${dayOfWeek}`);
        }
    }

    // Function to redirect to assignment from calendar
    window.redirectToAssignmentFromCalendar = function(assignmentId, programName, courseId) {
        console.log(`🔗 Redirecting to assignment ${assignmentId} in program: ${programName} for course: ${courseId}`);
        
        // Close the modal first
        const modal = document.getElementById('eventModal');
        if (modal) {
            closeCustomEventModal(modal);
        }
        
        // Store assignment info in sessionStorage for the course page to use
        sessionStorage.setItem('calendarAssignmentId', assignmentId);
        sessionStorage.setItem('calendarProgramName', programName);
        sessionStorage.setItem('calendarCourseId', courseId); // Store courseId
        
        // If we have a specific assignment ID, navigate directly to the content page
        if (assignmentId && assignmentId !== '' && assignmentId !== 'null') {
            console.log(`🎯 Navigating directly to content: ${assignmentId}`);
            window.location.href = `/student/content/${assignmentId}/view`;
        } else if (courseId && courseId !== '' && courseId !== 'null') {
            console.log(`🎯 Navigating to course: ${courseId}`);
            window.location.href = `/student/course/${courseId}`;
        } else {
            // Fallback to enrolled courses page
            console.log(`⚠️ No specific course ID, redirecting to enrolled courses`);
            window.location.href = '/student/enrolled-courses';
        }
    };

    // Function to redirect to quiz from calendar
    window.redirectToQuizFromCalendar = function(quizId) {
        console.log(`🔗 Redirecting to quiz ${quizId}`);
        
        // Close the modal first
        const modal = document.getElementById('eventModal');
        if (modal) {
            closeCustomEventModal(modal);
        }
        
        // Navigate directly to the quiz content page
        if (quizId && quizId !== '' && quizId !== 'null') {
            console.log(`🎯 Navigating directly to quiz content: ${quizId}`);
            window.location.href = `/student/content/${quizId}/view`;
        } else {
            console.log(`⚠️ No quiz ID provided, redirecting to dashboard`);
            window.location.href = '/student/dashboard';
        }
    };

    // Function to redirect to announcement from calendar
    window.redirectToAnnouncementFromCalendar = function(announcementId, title, content, type, time) {
        console.log(`🔗 Redirecting to announcement ${announcementId} with title: ${title}`);
        
        // Close the modal first
        const modal = document.getElementById('eventModal');
        if (modal) {
            closeCustomEventModal(modal);
        }
        
        // Store announcement data in sessionStorage for the dashboard to use
        sessionStorage.setItem('calendarAnnouncementId', announcementId);
        sessionStorage.setItem('calendarAnnouncementTitle', title);
        sessionStorage.setItem('calendarAnnouncementContent', content);
        sessionStorage.setItem('calendarAnnouncementType', type);
        sessionStorage.setItem('calendarAnnouncementTime', time);
        sessionStorage.setItem('openAnnouncementModal', 'true');
        
        // Redirect to the dashboard
        window.location.href = '/student/dashboard';
    };

    // Function to redirect to lesson from calendar
    window.redirectToLessonFromCalendar = function(lessonId, programId, moduleId, courseId) {
        console.log(`🔗 Redirecting to lesson ${lessonId} in program ${programId}, module ${moduleId}, course ${courseId}`);
        
        // Close the modal first
        const modal = document.getElementById('eventModal');
        if (modal) {
            closeCustomEventModal(modal);
        }
        
        // Store lesson info in sessionStorage for the course page to use
        sessionStorage.setItem('calendarLessonId', lessonId);
        sessionStorage.setItem('calendarProgramId', programId);
        sessionStorage.setItem('calendarModuleId', moduleId);
        sessionStorage.setItem('calendarCourseId', courseId);
        
        // If we have a specific lesson ID, navigate directly to the content page
        if (lessonId && lessonId !== '' && lessonId !== 'null') {
            console.log(`🎯 Navigating directly to content: ${lessonId}`);
            window.location.href = `/student/content/${lessonId}/view`;
        } else if (courseId && courseId !== '' && courseId !== 'null') {
            console.log(`🎯 Navigating to course: ${courseId}`);
            window.location.href = `/student/course/${courseId}`;
        } else {
            // Fallback to enrolled courses page
            console.log(`⚠️ No specific course ID, redirecting to enrolled courses`);
            window.location.href = '/student/enrolled-courses';
        }
    };
});
</script>
@endpush
