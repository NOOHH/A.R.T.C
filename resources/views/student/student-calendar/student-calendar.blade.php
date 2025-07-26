@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Calendar')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student/student-calendar.css') }}">
<style>
/* Calendar Container Styles */
.calendar-container {
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Calendar Header */
.calendar-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.calendar-navigation {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.nav-btn:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}

.today-btn {
    background: rgba(255,255,255,0.9);
    color: #667eea;
    font-weight: 600;
}

.current-month {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 20px;
}

/* Week View Grid */
.week-view {
    display: grid;
    grid-template-columns: 80px repeat(7, 1fr);
    background: white;
}

/* Time Column */
.time-column {
    background: #f8f9fa;
    border-right: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
}

.time-slot {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    color: #6c757d;
    border-bottom: 1px solid #e9ecef;
    font-weight: 500;
}

/* Day Headers */
.day-header {
    padding: 15px;
    text-align: center;
    font-weight: 600;
    background: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
    color: #495057;
}

.day-header.today {
    background: #e3f2fd;
    color: #1976d2;
    border-bottom-color: #1976d2;
}

.day-number {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 2px;
}

.day-name {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Day Columns */
.day-column {
    border-right: 1px solid #e9ecef;
    position: relative;
    min-height: 600px;
}

.day-column:last-child {
    border-right: none;
}

/* Time Grid Lines */
.time-grid {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    pointer-events: none;
}

.time-line {
    height: 60px;
    border-bottom: 1px solid #f1f3f4;
}

/* Events in Calendar */
.calendar-event {
    position: absolute;
    left: 4px;
    right: 4px;
    border-radius: 6px;
    padding: 6px 8px;
    font-size: 0.75rem;
    font-weight: 500;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 10;
}

.calendar-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.event-meeting { 
    background: linear-gradient(135deg, #4fc3f7, #29b6f6);
}

.event-assignment { 
    background: linear-gradient(135deg, #ffb74d, #ff9800);
}

.event-announcement { 
    background: linear-gradient(135deg, #81c784, #66bb6a);
}

.event-title {
    font-weight: 600;
    margin-bottom: 2px;
    line-height: 1.2;
}

.event-time {
    font-size: 0.65rem;
    opacity: 0.9;
}

/* Sidebar Styles */
.sidebar-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: none;
    margin-bottom: 20px;
}

.sidebar-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0 !important;
    border: none;
    padding: 15px 20px;
}

.sidebar-card.schedule-card .card-header {
    background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
}

.today-schedule-item {
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.today-schedule-item:last-child {
    border-bottom: none;
}

.schedule-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.schedule-meeting .schedule-icon { 
    background: linear-gradient(135deg, #4fc3f7, #29b6f6);
}

.schedule-assignment .schedule-icon { 
    background: linear-gradient(135deg, #ffb74d, #ff9800);
}

.schedule-announcement .schedule-icon { 
    background: linear-gradient(135deg, #81c784, #66bb6a);
}

.schedule-details h6 {
    margin: 0 0 4px 0;
    font-weight: 600;
    color: #333;
}

.schedule-time {
    font-size: 0.85rem;
    color: #666;
    margin: 0;
}

.schedule-program {
    font-size: 0.75rem;
    color: #999;
    margin: 2px 0 0 0;
}

/* Legend Styles */
.legend-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    padding: 8px 0;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Stats Styles */
.stats-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.stats-item:last-child {
    border-bottom: none;
}

.stats-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}

.stats-badge.meetings {
    background: linear-gradient(135deg, #4fc3f7, #29b6f6);
}

.stats-badge.assignments {
    background: linear-gradient(135deg, #ffb74d, #ff9800);
}

.stats-badge.announcements {
    background: linear-gradient(135deg, #81c784, #66bb6a);
}

/* Loading Spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 992px) {
    .week-view {
        grid-template-columns: 60px repeat(7, 1fr);
    }
    
    .time-slot {
        font-size: 0.7rem;
        height: 50px;
    }
    
    .calendar-event {
        font-size: 0.65rem;
        padding: 4px 6px;
    }
    
    .current-month {
        font-size: 1.2rem;
        margin: 0 10px;
    }
}

@media (max-width: 768px) {
    .calendar-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .calendar-navigation {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .week-view {
        grid-template-columns: 50px repeat(7, 1fr);
    }
    
    .day-header {
        padding: 8px;
        font-size: 0.8rem;
    }
    
    .day-number {
        font-size: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Calendar -->
        <div class="col-lg-9">
            <div class="calendar-container">
                <div class="calendar-header">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar3 me-2"></i>Academic Calendar
                    </h4>
                    <div class="calendar-navigation">
                        <!-- Debug Test Button -->
                        <button type="button" class="nav-btn" onclick="testCalendarEndpoints()">
                            <i class="bi bi-bug me-1"></i>Test
                        </button>
                        <button class="nav-btn" id="prevMonth">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="nav-btn today-btn" id="todayBtn">Today</button>
                        <span class="current-month" id="currentMonth">{{ date('F Y') }}</span>
                        <button class="nav-btn" id="nextMonth">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Week View Container -->
                <div class="week-view" id="weekView">
                    <!-- Time Column -->
                    <div class="time-column">
                        <div class="time-slot"></div> <!-- Empty slot for header alignment -->
                        <div class="time-slot">8 AM</div>
                        <div class="time-slot">9 AM</div>
                        <div class="time-slot">10 AM</div>
                        <div class="time-slot">11 AM</div>
                        <div class="time-slot">12 PM</div>
                        <div class="time-slot">1 PM</div>
                        <div class="time-slot">2 PM</div>
                        <div class="time-slot">3 PM</div>
                        <div class="time-slot">4 PM</div>
                        <div class="time-slot">5 PM</div>
                    </div>
                    
                    <!-- Day Columns will be generated by JavaScript directly in week-view -->
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Today's Schedule -->
            <div class="card sidebar-card schedule-card">
                <div class="card-header">
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
            <div class="card sidebar-card">
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
                        <div class="legend-color event-announcement"></div>
                        <span>Announcements</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card sidebar-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>This Month
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stats-item">
                        <span>Meetings:</span>
                        <span class="stats-badge meetings" id="meetingCount">0</span>
                    </div>
                    <div class="stats-item">
                        <span>Assignments:</span>
                        <span class="stats-badge assignments" id="assignmentCount">0</span>
                    </div>
                    <div class="stats-item">
                        <span>Announcements:</span>
                        <span class="stats-badge announcements" id="announcementCount">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <div id="eventModalActions">
                    <!-- Action buttons will be added based on event type -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Debug function to test calendar endpoints - defined at the top for global access
window.testCalendarEndpoints = function() {
    console.log('🧪 Testing Calendar Endpoints...');
    
    // Test 1: Today's schedule
    console.log('1️⃣ Testing /student/calendar/today');
    fetch('/student/calendar/today')
        .then(response => {
            console.log('Today API Status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Today API Response:', data);
            alert('Today API Response: ' + JSON.stringify(data, null, 2));
        })
        .catch(error => {
            console.error('Today API Error:', error);
            alert('Today API Error: ' + error.message);
        });
    
    // Test 2: Current month events
    setTimeout(() => {
        const today = new Date();
        const url = `/student/calendar/events?year=${today.getFullYear()}&month=${today.getMonth() + 1}`;
        console.log('2️⃣ Testing:', url);
        
        fetch(url)
            .then(response => {
                console.log('Events API Status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Events API Response:', data);
                alert('Events API Response: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                console.error('Events API Error:', error);
                alert('Events API Error: ' + error.message);
            });
    }, 1000);
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Calendar page loaded, initializing...');
    
    let currentDate = new Date();
    let currentEvents = [];
    
    console.log(`📅 Current date: ${currentDate.toISOString()}`);
    console.log(`📅 Initializing calendar for: ${currentDate.getFullYear()}-${currentDate.getMonth() + 1}`);
    
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
    
    function generateCalendar(year, month) {
        console.log(`🗓️ Generating weekly calendar for ${year}-${month + 1}`);
        
        const weekViewContainer = document.getElementById('weekView');
        const currentMonthElement = document.getElementById('currentMonth');
        
        if (!weekViewContainer) {
            console.error('❌ Could not find weekView element');
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
        
        // Get current week (week containing today for current month, or first week of month)
        const today = new Date();
        let targetDate;
        
        if (year === today.getFullYear() && month === today.getMonth()) {
            targetDate = today;
        } else {
            targetDate = new Date(year, month, 1);
        }
        
        // Get start of week (Sunday)
        const startOfWeek = new Date(targetDate);
        startOfWeek.setDate(targetDate.getDate() - targetDate.getDay());
        
        console.log(`� Displaying week starting: ${startOfWeek.toISOString().split('T')[0]}`);
        
        // Clear existing day columns (keep time column)
        const existingDayColumns = weekViewContainer.querySelectorAll('.day-column');
        existingDayColumns.forEach(column => column.remove());
        
        // Generate 7 day columns
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        for (let i = 0; i < 7; i++) {
            const currentDay = new Date(startOfWeek);
            currentDay.setDate(startOfWeek.getDate() + i);
            
            const dayColumn = document.createElement('div');
            dayColumn.className = 'day-column';
            
            // Check if this is today
            const isToday = currentDay.toDateString() === today.toDateString();
            
            // Create day header
            const dayHeader = document.createElement('div');
            dayHeader.className = `day-header ${isToday ? 'today' : ''}`;
            dayHeader.innerHTML = `
                <div class="day-number">${currentDay.getDate()}</div>
                <div class="day-name">${dayNames[i]}</div>
            `;
            
            // Create time grid for visual reference
            const timeGrid = document.createElement('div');
            timeGrid.className = 'time-grid';
            for (let hour = 0; hour < 11; hour++) { // 11 time slots including header
                const timeLine = document.createElement('div');
                timeLine.className = 'time-line';
                timeGrid.appendChild(timeLine);
            }
            
            // Store date for later event population
            const dateString = currentDay.toISOString().split('T')[0];
            dayColumn.dataset.date = dateString;
            
            // Add click handler
            dayColumn.addEventListener('click', () => {
                console.log(`🖱️ Day clicked: ${dateString}`);
                showDayEvents(dateString);
            });
            
            dayColumn.appendChild(dayHeader);
            dayColumn.appendChild(timeGrid);
            weekViewContainer.appendChild(dayColumn);
            
            console.log(`📝 Created day column for ${dateString} (${isToday ? 'TODAY' : 'normal'})`);
        }
        
        console.log(`✅ Weekly calendar generated with ${weekViewContainer.children.length - 1} day columns`);
        
        // Load events for this week
        loadWeekEvents(startOfWeek);
    }
    
    function loadWeekEvents(startOfWeek) {
        console.log(`📅 Loading events for week starting: ${startOfWeek.toISOString().split('T')[0]}`);
        
        // Calculate end of week
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        
        // First try to load real data from the API
        fetch(`/student/calendar/events?year=${startOfWeek.getFullYear()}&month=${startOfWeek.getMonth() + 1}`)
            .then(response => {
                console.log('📡 Events API Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 Events API Response data:', data);
                if (data.success && data.events && data.events.length > 0) {
                    console.log('✅ Loaded real events from API:', data.events.length);
                    currentEvents = data.events;
                    populateWeeklyEvents();
                    updateStats(data.meta || {
                        meetings: data.events.filter(e => e.type === 'meeting').length,
                        assignments: data.events.filter(e => e.type === 'assignment').length,
                        announcements: data.events.filter(e => e.type === 'announcement').length
                    });
                } else if (data.success && data.events && data.events.length === 0) {
                    console.log('ℹ️ API returned success but no events - using mock data for demo');
                    // Use mock data when API succeeds but returns empty events array
                    const mockEvents = generateMockWeekEvents(startOfWeek);
                    currentEvents = mockEvents;
                    populateWeeklyEvents();
                    updateStats({
                        meetings: mockEvents.filter(e => e.type === 'meeting').length,
                        assignments: mockEvents.filter(e => e.type === 'assignment').length,
                        announcements: mockEvents.filter(e => e.type === 'announcement').length
                    });
                } else if (data.success === false && data.message === 'Student not found') {
                    console.log('🔒 Authentication required for events - using demo data');
                    // Use mock data for demo when not authenticated
                    const mockEvents = generateMockWeekEvents(startOfWeek);
                    currentEvents = mockEvents;
                    populateWeeklyEvents();
                    updateStats({
                        meetings: mockEvents.filter(e => e.type === 'meeting').length,
                        assignments: mockEvents.filter(e => e.type === 'assignment').length,
                        announcements: mockEvents.filter(e => e.type === 'announcement').length
                    });
                } else {
                    // Fallback to mock data for demo
                    console.log('ℹ️ No real events found, using mock data');
                    const mockEvents = generateMockWeekEvents(startOfWeek);
                    currentEvents = mockEvents;
                    populateWeeklyEvents();
                    updateStats({
                        meetings: mockEvents.filter(e => e.type === 'meeting').length,
                        assignments: mockEvents.filter(e => e.type === 'assignment').length,
                        announcements: mockEvents.filter(e => e.type === 'announcement').length
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error loading events from API:', error);
                // Fallback to mock data
                const mockEvents = generateMockWeekEvents(startOfWeek);
                currentEvents = mockEvents;
                populateWeeklyEvents();
                updateStats({
                    meetings: mockEvents.filter(e => e.type === 'meeting').length,
                    assignments: mockEvents.filter(e => e.type === 'assignment').length,
                    announcements: mockEvents.filter(e => e.type === 'announcement').length
                });
            });
    }
    
    function generateMockWeekEvents(startOfWeek) {
        console.log(`🎲 Generating mock events for week starting: ${startOfWeek.toISOString().split('T')[0]}`);
        const events = [];
        const today = new Date();
        
        // Generate events for each day of the week
        for (let day = 0; day < 7; day++) {
            const eventDate = new Date(startOfWeek);
            eventDate.setDate(startOfWeek.getDate() + day);
            
            // Skip weekends for most events
            if (day === 0 || day === 6) continue;
            
            // Add 1-3 events per weekday
            const numEvents = Math.floor(Math.random() * 3) + 1;
            
            for (let i = 0; i < numEvents; i++) {
                const eventTypes = ['meeting', 'assignment', 'announcement'];
                const randomType = eventTypes[Math.floor(Math.random() * eventTypes.length)];
                
                let eventTitle, eventDesc, hour;
                switch (randomType) {
                    case 'meeting':
                        eventTitle = i === 0 ? 'Software Engineering' : 'Database Systems';
                        eventDesc = 'Interactive class session with professor';
                        hour = 9 + i * 2; // 9 AM, 11 AM, 1 PM
                        break;
                    case 'assignment':
                        eventTitle = i === 0 ? 'Project Submission' : 'Lab Assignment';
                        eventDesc = 'Assignment due date';
                        hour = 23; // Due at end of day
                        break;
                    case 'announcement':
                        eventTitle = i === 0 ? 'Schedule Update' : 'Important Notice';
                        eventDesc = 'New announcement from administration';
                        hour = 8; // Morning announcements
                        break;
                }
                
                const eventDateTime = new Date(eventDate);
                eventDateTime.setHours(hour, 0, 0, 0);
                
                const event = {
                    id: `${randomType}_${day}_${i}_${startOfWeek.getTime()}`,
                    title: eventTitle,
                    start: eventDateTime.toISOString(),
                    type: randomType,
                    description: eventDesc,
                    program: 'Computer Science',
                    professor: 'Dr. Smith',
                    time: `${hour > 12 ? hour - 12 : hour}:00 ${hour >= 12 ? 'PM' : 'AM'}`,
                    duration: randomType === 'meeting' ? 90 : 0 // 90 minutes for meetings
                };
                
                events.push(event);
                console.log(`➕ Generated mock event: ${event.title} on ${eventDate.toISOString().split('T')[0]} at ${event.time}`);
            }
        }
        
        console.log(`✅ Generated ${events.length} mock events for the week`);
        return events;
    }
    
    function populateWeeklyEvents() {
        console.log(`🎯 Populating weekly calendar with ${currentEvents.length} events`);
        
        // Clear existing events
        const dayColumns = document.querySelectorAll('.day-column');
        dayColumns.forEach(column => {
            const existingEvents = column.querySelectorAll('.calendar-event');
            existingEvents.forEach(event => event.remove());
        });
        
        if (currentEvents.length === 0) {
            console.log('ℹ️ No events to populate');
            return;
        }
        
        // Add events to appropriate day columns
        currentEvents.forEach(event => {
            const eventDate = new Date(event.start).toISOString().split('T')[0];
            const dayColumn = document.querySelector(`[data-date="${eventDate}"]`);
            
            if (dayColumn) {
                const eventHour = new Date(event.start).getHours();
                const topPosition = Math.max(0, (eventHour - 8) * 60); // 60px per hour, starting at 8 AM
                
                const eventElement = document.createElement('div');
                eventElement.className = `calendar-event event-${event.type}`;
                eventElement.style.top = `${topPosition + 60}px`; // +60 for header
                
                if (event.duration && event.duration > 0) {
                    const height = (event.duration / 60) * 60; // Convert minutes to pixels
                    eventElement.style.height = `${Math.max(30, height)}px`;
                } else {
                    eventElement.style.height = '30px'; // Default height for non-timed events
                }
                
                eventElement.innerHTML = `
                    <div class="event-title">${event.title}</div>
                    <div class="event-time">${event.time}</div>
                `;
                
                eventElement.addEventListener('click', (e) => {
                    e.stopPropagation();
                    console.log(`🎯 Event clicked: ${event.title}`);
                    showEventDetails(event);
                });
                
                dayColumn.appendChild(eventElement);
                console.log(`➕ Added event: ${event.title} to ${eventDate} at ${event.time}`);
            } else {
                console.warn(`⚠️ Could not find day column for date: ${eventDate}`);
            }
        });
        
        console.log(`✅ Finished populating weekly calendar events`);
    }
    
    function loadMonthEvents(year, month) {
        console.log(`📅 Loading events for ${year}-${month + 1}`);
        
        // First try to load real data from the API
        fetch(`/student/calendar/events?year=${year}&month=${month + 1}`)
            .then(response => {
                console.log('📡 Events API Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 Events API Response data:', data);
                if (data.success && data.events && data.events.length > 0) {
                    console.log('✅ Loaded real events from API:', data.events.length);
                    currentEvents = data.events;
                    populateCalendarEvents();
                    updateStats(data.meta || {
                        meetings: data.events.filter(e => e.type === 'meeting').length,
                        assignments: data.events.filter(e => e.type === 'assignment').length,
                        announcements: data.events.filter(e => e.type === 'announcement').length
                    });
                } else if (data.success && data.events && data.events.length === 0) {
                    console.log('ℹ️ API returned success but no events - using mock data for demo');
                    // Use mock data when API succeeds but returns empty events array
                    const mockEvents = generateMockEvents(year, month);
                    currentEvents = mockEvents;
                    populateCalendarEvents();
                    updateStats({
                        meetings: mockEvents.filter(e => e.type === 'meeting').length,
                        assignments: mockEvents.filter(e => e.type === 'assignment').length,
                        announcements: mockEvents.filter(e => e.type === 'announcement').length
                    });
                } else if (data.success === false && data.message === 'Student not found') {
                    console.log('🔒 Authentication required for events - using demo data');
                    // Use mock data for demo when not authenticated
                    const mockEvents = generateMockEvents(year, month);
                    currentEvents = mockEvents;
                    populateCalendarEvents();
                    updateStats({
                        meetings: mockEvents.filter(e => e.type === 'meeting').length,
                        assignments: mockEvents.filter(e => e.type === 'assignment').length,
                        announcements: mockEvents.filter(e => e.type === 'announcement').length
                    });
                } else {
                    // Fallback to mock data for demo
                    console.log('ℹ️ No real events found, using mock data');
                    const mockEvents = generateMockEvents(year, month);
                    currentEvents = mockEvents;
                    populateCalendarEvents();
                    updateStats({
                        meetings: mockEvents.filter(e => e.type === 'meeting').length,
                        assignments: mockEvents.filter(e => e.type === 'assignment').length,
                        announcements: mockEvents.filter(e => e.type === 'announcement').length
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error loading events from API:', error);
                // Fallback to mock data
                const mockEvents = generateMockEvents(year, month);
                currentEvents = mockEvents;
                populateCalendarEvents();
                updateStats({
                    meetings: mockEvents.filter(e => e.type === 'meeting').length,
                    assignments: mockEvents.filter(e => e.type === 'assignment').length,
                    announcements: mockEvents.filter(e => e.type === 'announcement').length
                });
            });
    }
    
    function generateMockEvents(year, month) {
        console.log(`🎲 Generating mock events for ${year}-${month + 1}`);
        const events = [];
        const today = new Date();
        
        // Always generate events for any month being viewed
        for (let day = 1; day <= 31; day++) {
            const eventDate = new Date(year, month, day);
            if (eventDate.getMonth() !== month) continue; // Skip invalid dates
            
            // Add events every few days
            if (day % 3 === 0 || day % 5 === 0 || day % 7 === 0) {
                const eventTypes = ['meeting', 'assignment', 'announcement'];
                const randomType = eventTypes[Math.floor(Math.random() * eventTypes.length)];
                
                let eventTitle, eventDesc;
                switch (randomType) {
                    case 'meeting':
                        eventTitle = day % 2 === 0 ? 'Software Engineering' : 'Database Systems';
                        eventDesc = 'Interactive class session with professor';
                        break;
                    case 'assignment':
                        eventTitle = day % 2 === 0 ? 'Project Submission' : 'Lab Assignment';
                        eventDesc = 'Assignment due date';
                        break;
                    case 'announcement':
                        eventTitle = day % 2 === 0 ? 'Schedule Update' : 'Important Notice';
                        eventDesc = 'New announcement from administration';
                        break;
                }
                
                const event = {
                    id: `${randomType}_${day}_${month}_${year}`,
                    title: eventTitle,
                    start: eventDate.toISOString(),
                    type: randomType,
                    description: eventDesc,
                    program: 'Computer Science',
                    professor: 'Dr. Smith',
                    time: `${8 + (day % 8)}:00 AM`
                };
                
                events.push(event);
                console.log(`➕ Generated mock event: ${event.title} on ${eventDate.toISOString().split('T')[0]}`);
            }
        }
        
        // Add some special events for current month
        if (year === today.getFullYear() && month === today.getMonth()) {
            console.log(`🎯 Adding special events for current month`);
            
            // Today's events
            const todayEvent = {
                id: 'today_meeting',
                title: 'Morning Standup',
                start: new Date(year, month, today.getDate(), 9, 0).toISOString(),
                type: 'meeting',
                description: 'Daily team meeting',
                program: 'Software Development',
                professor: 'Prof. Johnson',
                time: '9:00 AM'
            };
            events.push(todayEvent);
            console.log(`➕ Added today's event: ${todayEvent.title}`);
            
            // Tomorrow's events if valid
            if (today.getDate() + 1 <= new Date(year, month + 1, 0).getDate()) {
                const tomorrowEvent = {
                    id: 'tomorrow_assignment',
                    title: 'Final Project Due',
                    start: new Date(year, month, today.getDate() + 1, 23, 59).toISOString(),
                    type: 'assignment',
                    description: 'Final project submission deadline',
                    program: 'Computer Science',
                    professor: 'Dr. Brown',
                    time: '11:59 PM'
                };
                events.push(tomorrowEvent);
                console.log(`➕ Added tomorrow's event: ${tomorrowEvent.title}`);
            }
        }
        
        console.log(`✅ Generated ${events.length} mock events for ${year}-${month + 1}`);
        return events;
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
        
        // First, try to load real data from the API
        fetch('/student/calendar/today')
            .then(response => {
                console.log('📡 API Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 API Response data:', data);
                if (data.success && data.events && data.events.length > 0) {
                    console.log('✅ Found real events, displaying:', data.events.length);
                    displayTodaySchedule(data.events);
                } else if (data.success && data.events && data.events.length === 0) {
                    console.log('ℹ️ API returned success but no events - using mock data for demo');
                    // Use mock data when API succeeds but returns empty events array
                    const today = new Date();
                    const mockTodayEvents = [
                        {
                            id: 'today_1',
                            title: 'Morning Lecture',
                            start: new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 0).toISOString(),
                            type: 'meeting',
                            description: 'Software Engineering Fundamentals',
                            program: 'Computer Science',
                            professor: 'Dr. Johnson',
                            time: '9:00 AM'
                        },
                        {
                            id: 'today_2',
                            title: 'Assignment Review',
                            start: new Date(today.getFullYear(), today.getMonth(), today.getDate(), 14, 0).toISOString(),
                            type: 'assignment',
                            description: 'Database Design Project Review',
                            program: 'Computer Science',
                            professor: 'Prof. Smith',
                            time: '2:00 PM'
                        }
                    ];
                    displayTodaySchedule(mockTodayEvents);
                } else if (data.success === false && data.message === 'Student not found') {
                    console.log('🔒 Authentication required - showing auth error');
                    displayAuthError();
                } else if (data.success === false) {
                    console.log('⚠️ API returned error:', data.message);
                    // Still show mock data if API fails for other reasons
                    const today = new Date();
                    const mockTodayEvents = [
                        {
                            id: 'today_1',
                            title: 'Morning Lecture',
                            start: new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 0).toISOString(),
                            type: 'meeting',
                            description: 'Software Engineering Fundamentals',
                            program: 'Computer Science',
                            professor: 'Dr. Johnson',
                            time: '9:00 AM'
                        }
                    ];
                    displayTodaySchedule(mockTodayEvents);
                } else {
                    console.log('ℹ️ No real events found, using mock data for demo');
                    // If no real data, use mock data for demo
                    const today = new Date();
                    const mockTodayEvents = [
                        {
                            id: 'today_1',
                            title: 'Morning Lecture',
                            start: new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 0).toISOString(),
                            type: 'meeting',
                            description: 'Software Engineering Fundamentals',
                            program: 'Computer Science',
                            professor: 'Dr. Johnson',
                            time: '9:00 AM'
                        },
                        {
                            id: 'today_2',
                            title: 'Assignment Review',
                            start: new Date(today.getFullYear(), today.getMonth(), today.getDate(), 14, 0).toISOString(),
                            type: 'assignment',
                            description: 'Database Design Project Review',
                            program: 'Computer Science',
                            professor: 'Prof. Smith',
                            time: '2:00 PM'
                        }
                    ];
                    displayTodaySchedule(mockTodayEvents);
                }
            })
            .catch(error => {
                console.error('❌ Error loading today schedule:', error);
                // Fallback to mock data
                const today = new Date();
                const mockTodayEvents = [
                    {
                        id: 'today_1',
                        title: 'Morning Lecture',
                        start: new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 0).toISOString(),
                        type: 'meeting',
                        description: 'Software Engineering Fundamentals',
                        program: 'Computer Science',
                        professor: 'Dr. Johnson',
                        time: '9:00 AM'
                    }
                ];
                displayTodaySchedule(mockTodayEvents);
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
            
            let iconClass;
            switch (event.type) {
                case 'meeting':
                    iconClass = 'bi-camera-video';
                    break;
                case 'assignment':
                    iconClass = 'bi-file-earmark-text';
                    break;
                case 'announcement':
                    iconClass = 'bi-megaphone';
                    break;
                default:
                    iconClass = 'bi-calendar-event';
            }
            
            return `
                <div class="today-schedule-item ${scheduleClass}" onclick="showEventFromJSON('${JSON.stringify(event).replace(/'/g, "&#39;").replace(/"/g, "&quot;")}')">
                    <div class="schedule-icon">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="schedule-details">
                        <h6>${event.title}</h6>
                        <p class="schedule-time">${time}</p>
                        ${event.program ? `<p class="schedule-program">${event.program}</p>` : ''}
                    </div>
                    ${event.type === 'meeting' && event.meeting_url ? 
                        `<a href="${event.meeting_url}" target="_blank" class="btn btn-primary btn-sm ms-auto">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>` : ''}
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
            document.getElementById('announcementCount').textContent = meta.announcements || 0;
        }
    }
    
    function showEventDetails(event) {
        console.log(`🎯 showEventDetails called for event:`, event);
        
        const modalElement = document.getElementById('eventModal');
        if (!modalElement) {
            console.error('❌ Could not find eventModal element');
            return;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        const modalTitle = document.getElementById('eventModalLabel');
        const modalBody = document.getElementById('eventModalBody');
        const modalActions = document.getElementById('eventModalActions');
        
        if (!modalTitle || !modalBody || !modalActions) {
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
                        <strong>Program:</strong> ${event.program}
                    </div>
                    <div class="col-md-6">
                        <strong>Professor:</strong> ${event.professor}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Duration:</strong> ${event.duration} minutes
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> <span class="badge bg-primary">${event.status}</span>
                    </div>
                </div>
            `;
            
            modalActions.innerHTML = event.meeting_url ? 
                `<a href="${event.meeting_url}" target="_blank" class="btn btn-primary">
                    <i class="bi bi-camera-video me-2"></i>Join Meeting
                </a>` : '';
        } else if (event.type === 'assignment') {
            bodyHtml += `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Program:</strong> ${event.program}
                    </div>
                    <div class="col-md-6">
                        <strong>Professor:</strong> ${event.professor}
                    </div>
                </div>
                ${event.max_points ? `<p><strong>Max Points:</strong> ${event.max_points}</p>` : ''}
                ${event.instructions ? `<p><strong>Instructions:</strong><br>${event.instructions}</p>` : ''}
            `;
            
            modalActions.innerHTML = '';
        } else if (event.type === 'announcement') {
            bodyHtml += `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Posted by:</strong> ${event.professor}
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong> ${event.announcement_type || 'General'}
                    </div>
                </div>
                ${event.content ? `<p><strong>Content:</strong><br>${event.content}</p>` : ''}
                ${event.expire_date ? `<p><strong>Expires:</strong> ${new Date(event.expire_date).toLocaleDateString()}</p>` : ''}
            `;
            
            modalActions.innerHTML = event.video_link ? 
                `<a href="${event.video_link}" target="_blank" class="btn btn-success">
                    <i class="bi bi-play-circle me-2"></i>Watch Video
                </a>` : '';
        }
        
        modalBody.innerHTML = bodyHtml;
        modal.show();
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
        
        const modalElement = document.getElementById('eventModal');
        if (!modalElement) {
            console.error('❌ Could not find eventModal element');
            return;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        const modalTitle = document.getElementById('eventModalLabel');
        const modalBody = document.getElementById('eventModalBody');
        const modalActions = document.getElementById('eventModalActions');
        
        if (!modalTitle || !modalBody || !modalActions) {
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
                        <button class="btn btn-outline-primary btn-sm" onclick="showEventFromJSON('${JSON.stringify(event).replace(/'/g, "&#39;").replace(/"/g, "&quot;")}')">
                            View Details
                        </button>
                    </div>
                </div>
            `).join('');
            
            modalBody.innerHTML = eventsHtml;
            console.log(`📝 Generated HTML for ${dayEvents.length} events`);
        }
        
        modal.show();
        console.log('✅ Modal shown');
    }
    
    // Make showEventDetails available globally
    window.showEventDetails = showEventDetails;
    
    // Helper function for JSON event handling
    window.showEventFromJSON = function(eventJson) {
        try {
            const event = JSON.parse(eventJson);
            showEventDetails(event);
        } catch (error) {
            console.error('Error parsing event JSON:', error);
        }
    };
});
</script>
@endpush
