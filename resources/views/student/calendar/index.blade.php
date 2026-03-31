@extends('student.layouts.master')

@section('title', 'Calendar')
@section('page-icon', 'fa-calendar-alt')
@section('page-title', 'My Calendar')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    .calendar-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .calendar-header h1 {
        font-size: 2rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .calendar-header h1 i {
        color: #f59e0b;
    }

    .legend {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: #475569;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    .legend-color.deadline {
        background: #f59e0b;
    }

    .legend-color.deadline-past {
        background: #ef4444;
    }

    .legend-color.study {
        background: #10b981;
    }

    #calendar {
        background: white;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .fc-toolbar-title {
        font-size: 1.3rem !important;
        color: #1e293b;
    }

    .fc-button-primary {
        background: #f59e0b !important;
        border-color: #f59e0b !important;
    }

    .fc-button-primary:hover {
        background: #d97706 !important;
        border-color: #d97706 !important;
    }

    .fc-button-primary:disabled {
        background: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
    }

    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        padding: 2px 4px;
    }

    .fc-event-title {
        font-weight: 500;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .modal-header h3 {
        font-size: 1.3rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #94a3b8;
    }

    .modal-body {
        margin-bottom: 20px;
    }

    .event-detail {
        margin-bottom: 15px;
    }

    .event-detail-label {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 4px;
    }

    .event-detail-value {
        font-size: 1rem;
        color: #1e293b;
    }

    .event-detail-value.unit {
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #f59e0b;
        color: white;
    }

    .btn-primary:hover {
        background: #d97706;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column;
            gap: 10px;
        }
        
        .legend {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="calendar-container">
    <div class="calendar-header">
        <h1><i class="fas fa-calendar-alt"></i> My Calendar</h1>
        
        <div class="legend">
            <div class="legend-item">
                <span class="legend-color deadline"></span>
                <span>Upcoming Deadline</span>
            </div>
            <div class="legend-item">
                <span class="legend-color deadline-past"></span>
                <span>Overdue Deadline</span>
            </div>
            <div class="legend-item">
                <span class="legend-color study"></span>
                <span>Study Session</span>
            </div>
        </div>
    </div>

    <div id="calendar"></div>
</div>

<!-- Event Details Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-calendar-alt" style="color: #f59e0b;"></i> <span id="modalTitle"></span></h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="event-detail">
                <div class="event-detail-label">Type</div>
                <div class="event-detail-value" id="modalType"></div>
            </div>
            <div class="event-detail">
                <div class="event-detail-label">Unit</div>
                <div class="event-detail-value" id="modalUnit"></div>
            </div>
            <div class="event-detail">
                <div class="event-detail-label">Date & Time</div>
                <div class="event-detail-value" id="modalDateTime"></div>
            </div>
            <div class="event-detail">
                <div class="event-detail-label">Description</div>
                <div class="event-detail-value" id="modalDescription"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            <a href="#" id="modalAction" class="btn btn-primary">View Details</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
            events: {
                url: '{{ route("student.calendar.events") }}',
                method: 'GET',
                failure: function() {
                    console.error('Error loading events');
                }
            },
            eventClick: function(info) {
                showEventDetails(info.event);
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: 'short'
            },
            height: 'auto',
            firstDay: 1 // Monday
        });
        
        calendar.render();
    });

    function showEventDetails(event) {
        const props = event.extendedProps;
        
        document.getElementById('modalTitle').textContent = event.title;
        document.getElementById('modalType').textContent = props.type === 'deadline' ? 'Deadline' : 'Study Session';
        document.getElementById('modalUnit').textContent = props.unit || 'N/A';
        document.getElementById('modalDateTime').textContent = formatDateTime(event.start, event.end);
        document.getElementById('modalDescription').textContent = props.description || 'No description';
        document.getElementById('modalAction').href = props.url || '#';
        
        document.getElementById('eventModal').style.display = 'flex';
    }

    function formatDateTime(start, end) {
        if (!start) return 'N/A';
        
        const startDate = new Date(start);
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        
        if (end && start !== end) {
            const endDate = new Date(end);
            return startDate.toLocaleDateString('en-US', options) + ' - ' + 
                   endDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
        
        return startDate.toLocaleDateString('en-US', options);
    }

    function closeModal() {
        document.getElementById('eventModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('eventModal');
        if (e.target === modal) {
            closeModal();
        }
    });
</script>
@endpush