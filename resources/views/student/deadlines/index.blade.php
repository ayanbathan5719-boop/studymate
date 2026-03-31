@extends('student.layouts.master')

@section('title', 'My Deadlines')
@section('page-icon', 'fa-clock')
@section('page-title', 'My Deadlines')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Deadlines</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .deadlines-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .stat-icon.upcoming {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-icon.overdue {
        background: #fee2e2;
        color: #ef4444;
    }

    .stat-icon.completed {
        background: #d1fae5;
        color: #10b981;
    }

    .stat-info h4 {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .stat-info .stat-value {
        color: #1e293b;
        font-size: 2rem;
        font-weight: 600;
    }

    /* Filters */
    .filters-section {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 8px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 30px;
        background: white;
        color: #64748b;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-btn.active {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    .filter-btn i {
        font-size: 0.9rem;
    }

    .filter-btn:hover:not(.active) {
        border-color: #f59e0b;
        color: #1e293b;
    }

    .unit-filter {
        margin-left: auto;
        padding: 8px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 30px;
        color: #1e293b;
        font-size: 0.95rem;
    }

    /* Deadlines List */
    .deadlines-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .deadline-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .deadline-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .deadline-card.overdue {
        border-left: 4px solid #ef4444;
    }

    .deadline-card.upcoming {
        border-left: 4px solid #f59e0b;
    }

    .deadline-card.completed {
        border-left: 4px solid #10b981;
        opacity: 0.8;
    }

    .deadline-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .deadline-title-section {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .deadline-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .deadline-icon.general {
        background: #f1f5f9;
        color: #64748b;
    }

    .deadline-icon.topic {
        background: #fef3c7;
        color: #f59e0b;
    }

    .deadline-icon.assignment {
        background: #d1fae5;
        color: #10b981;
    }

    .deadline-title h3 {
        color: #1e293b;
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .deadline-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #64748b;
        font-size: 0.85rem;
    }

    .deadline-meta i {
        color: #f59e0b;
        margin-right: 4px;
    }

    .deadline-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-general {
        background: #f1f5f9;
        color: #64748b;
    }

    .badge-topic {
        background: #fef3c7;
        color: #f59e0b;
    }

    .badge-assignment {
        background: #d1fae5;
        color: #10b981;
    }

    .deadline-body {
        margin-bottom: 15px;
    }

    .deadline-description {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .deadline-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .deadline-time {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .time-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 0.9rem;
    }

    .time-item i {
        color: #f59e0b;
    }

    .time-item.urgent {
        color: #ef4444;
    }

    .time-item.urgent i {
        color: #ef4444;
    }

    .deadline-actions {
        display: flex;
        gap: 10px;
    }

    .btn-action {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-accept {
        background: #d1fae5;
        color: #10b981;
    }

    .btn-accept:hover:not(:disabled) {
        background: #10b981;
        color: white;
    }

    .btn-accept.accepted {
        background: #10b981;
        color: white;
        cursor: default;
    }

    .btn-decline {
        background: #fee2e2;
        color: #ef4444;
    }

    .btn-decline:hover:not(:disabled) {
        background: #ef4444;
        color: white;
    }

    .btn-decline.declined {
        background: #ef4444;
        color: white;
        cursor: default;
    }

    .btn-view {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-view:hover {
        background: #e2e8f0;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 20px;
        border: 2px dashed #e2e8f0;
    }

    .empty-icon {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #1e293b;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 0;
    }

    /* Calendar View Toggle */
    .view-toggle {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }

    .view-toggle-btn {
        padding: 8px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-toggle-btn.active {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    @media (max-width: 768px) {
        .filters-section {
            flex-direction: column;
            align-items: stretch;
        }
        
        .unit-filter {
            margin-left: 0;
        }
        
        .deadline-footer {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="deadlines-container">
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon upcoming">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Upcoming Deadlines</h4>
                <div class="stat-value">{{ $stats['upcoming'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon overdue">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h4>Overdue</h4>
                <div class="stat-value">{{ $stats['overdue'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Completed</h4>
                <div class="stat-value">{{ $stats['completed'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <div class="filter-group">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-th-large"></i> All
            </button>
            <button class="filter-btn" data-filter="upcoming">
                <i class="fas fa-clock"></i> Upcoming
            </button>
            <button class="filter-btn" data-filter="overdue">
                <i class="fas fa-exclamation-triangle"></i> Overdue
            </button>
            <button class="filter-btn" data-filter="completed">
                <i class="fas fa-check-circle"></i> Completed
            </button>
        </div>

        <select class="unit-filter" id="unitFilter">
            <option value="all">All Units</option>
            @foreach($units as $unit)
                <option value="{{ $unit->code }}">{{ $unit->code }} - {{ $unit->name }}</option>
            @endforeach
        </select>

        <div class="view-toggle">
            <button class="view-toggle-btn active" id="listView">
                <i class="fas fa-list"></i> List
            </button>
            <button class="view-toggle-btn" id="calendarView">
                <i class="fas fa-calendar"></i> Calendar
            </button>
        </div>
    </div>

    <!-- Deadlines List View -->
    <div id="listViewContainer">
        @if($deadlines->count() > 0)
            <div class="deadlines-list">
                @foreach($deadlines as $deadline)
                    @php
                        $status = $deadline->is_overdue ? 'overdue' : ($deadline->isAcceptedBy(Auth::id()) ? 'completed' : 'upcoming');
                        $acceptance = $deadline->getAcceptanceStatusFor(Auth::id());
                    @endphp
                    
                    <div class="deadline-card {{ $status }}" 
                         data-status="{{ $status }}"
                         data-unit="{{ $deadline->unit->code }}"
                         data-type="{{ $deadline->type }}">
                        
                        <div class="deadline-header">
                            <div class="deadline-title-section">
                                <div class="deadline-icon {{ $deadline->type }}">
                                    <i class="fas {{ $deadline->type_icon }}"></i>
                                </div>
                                <div class="deadline-title">
                                    <h3>{{ $deadline->title }}</h3>
                                    <div class="deadline-meta">
                                        <span>
                                            <i class="fas fa-layer-group"></i> {{ $deadline->unit->code }}
                                        </span>
                                        @if($deadline->topic)
                                            <span>
                                                <i class="fas fa-book-open"></i> {{ $deadline->topic->title }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="deadline-badge badge-{{ $deadline->type }}">
                                <i class="fas {{ $deadline->type_icon }}"></i>
                                {{ ucfirst($deadline->type) }}
                            </span>
                        </div>

                        @if($deadline->description)
                            <div class="deadline-body">
                                <p class="deadline-description">{{ $deadline->description }}</p>
                            </div>
                        @endif

                        <div class="deadline-footer">
                            <div class="deadline-time">
                                <div class="time-item {{ $deadline->is_overdue ? 'urgent' : '' }}">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $deadline->formatted_due_date }}
                                </div>
                                <div class="time-item {{ $deadline->is_overdue ? 'urgent' : '' }}">
                                    <i class="far fa-clock"></i>
                                    {{ $deadline->due_time }}
                                </div>
                                @if(!$deadline->is_overdue && !$acceptance)
                                    <div class="time-item">
                                        <i class="fas fa-hourglass-half"></i>
                                        {{ $deadline->days_remaining }} days remaining
                                    </div>
                                @endif
                            </div>

                            <div class="deadline-actions">
                                @if($deadline->topic)
                                    <a href="{{ route('student.topics.show', [$deadline->unit->code, $deadline->topic_id]) }}" 
                                       class="btn-action btn-view">
                                        <i class="fas fa-book-open"></i> View Topic
                                    </a>
                                @endif

                                @if(!$deadline->is_overdue)
                                    @if($acceptance == 'accepted')
                                        <button class="btn-action btn-accept accepted" disabled>
                                            <i class="fas fa-check-circle"></i> Accepted
                                        </button>
                                    @elseif($acceptance == 'declined')
                                        <button class="btn-action btn-decline declined" disabled>
                                            <i class="fas fa-times-circle"></i> Declined
                                        </button>
                                    @else
                                        <button class="btn-action btn-accept" onclick="handleAcceptance({{ $deadline->id }}, 'accept')">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                        <button class="btn-action btn-decline" onclick="handleAcceptance({{ $deadline->id }}, 'decline')">
                                            <i class="fas fa-times"></i> Decline
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>No Deadlines</h3>
                <p>You don't have any deadlines at the moment. Enjoy your free time!</p>
            </div>
        @endif
    </div>

    <!-- Calendar View (hidden by default) -->
    <div id="calendarViewContainer" style="display: none;">
        <div id="calendar"></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            const unitFilter = document.getElementById('unitFilter').value;
            
            filterDeadlines(filter, unitFilter);
        });
    });

    // Unit filter
    document.getElementById('unitFilter').addEventListener('change', function() {
        const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
        filterDeadlines(activeFilter, this.value);
    });

    function filterDeadlines(status, unit) {
        const cards = document.querySelectorAll('.deadline-card');
        
        cards.forEach(card => {
            let show = true;
            
            if (status !== 'all') {
                show = show && card.dataset.status === status;
            }
            
            if (unit !== 'all') {
                show = show && card.dataset.unit === unit;
            }
            
            card.style.display = show ? 'block' : 'none';
        });
    }

    // View toggle
    document.getElementById('listView').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('calendarView').classList.remove('active');
        document.getElementById('listViewContainer').style.display = 'block';
        document.getElementById('calendarViewContainer').style.display = 'none';
    });

    document.getElementById('calendarView').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('listView').classList.remove('active');
        document.getElementById('listViewContainer').style.display = 'none';
        document.getElementById('calendarViewContainer').style.display = 'block';
        
        // Initialize calendar if not already done
        if (!window.calendar) {
            initializeCalendar();
        }
    });

    // Handle acceptance/decline
    function handleAcceptance(deadlineId, action) {
        if (!confirm(`Are you sure you want to ${action} this deadline?`)) {
            return;
        }

        fetch(`/student/deadlines/${deadlineId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    // Initialize calendar
    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        window.calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                @foreach($deadlines as $deadline)
                {
                    title: '{{ addslashes($deadline->title) }}',
                    start: '{{ $deadline->due_date->format('Y-m-d\TH:i:s') }}',
                    color: '{{ $deadline->is_overdue ? '#ef4444' : ($deadline->isAcceptedBy(Auth::id()) ? '#10b981' : '#f59e0b') }}',
                    url: '{{ $deadline->topic ? route("student.topics.show", [$deadline->unit->code, $deadline->topic_id]) : "#" }}'
                },
                @endforeach
            ],
            eventClick: function(info) {
                if (info.event.url && info.event.url !== '#') {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            }
        });
        
        window.calendar.render();
    }
</script>
@endpush
@endsection