@extends('student.layouts.master')

@section('title', 'My Study Progress')
@section('page-icon', 'fa-chart-line')
@section('page-title', 'My Study Progress')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Study Progress</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .study-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .welcome-text h1 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .welcome-text p {
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .streak-badge {
        background: rgba(255,255,255,0.2);
        padding: 15px 25px;
        border-radius: 15px;
        text-align: center;
        backdrop-filter: blur(10px);
    }

    .streak-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .streak-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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

    .stat-icon.units {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-icon.topics {
        background: #dbeafe;
        color: #3b82f6;
    }

    .stat-icon.time {
        background: #d1fae5;
        color: #10b981;
    }

    .stat-icon.concepts {
        background: #ede9fe;
        color: #8b5cf6;
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

    .stat-info .stat-detail {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    /* Progress Overview */
    .overview-section {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-header h2 {
        color: #1e293b;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header h2 i {
        color: #f59e0b;
    }

    .view-all {
        color: #f59e0b;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .unit-progress-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .unit-progress-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .unit-progress-item:hover {
        background: #f8fafc;
    }

    .unit-info {
        flex: 1;
    }

    .unit-info h3 {
        color: #1e293b;
        font-size: 1rem;
        margin-bottom: 5px;
    }

    .unit-info .unit-code {
        color: #64748b;
        font-size: 0.85rem;
    }

    .progress-container {
        flex: 2;
        height: 8px;
        background: #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 20px;
        transition: width 0.3s ease;
    }

    .progress-percentage {
        min-width: 50px;
        color: #1e293b;
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Recent Activity */
    .activity-timeline {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 25px;
        padding-left: 25px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        border: 3px solid #f59e0b;
    }

    .timeline-item.completed::before {
        background: #10b981;
        border-color: #10b981;
    }

    .timeline-date {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }

    .timeline-content {
        background: #f8fafc;
        border-radius: 12px;
        padding: 15px;
    }

    .timeline-title {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .timeline-desc {
        color: #64748b;
        font-size: 0.9rem;
    }

    /* Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .chart-header h3 {
        color: #1e293b;
        font-size: 1rem;
        font-weight: 600;
    }

    .chart-container {
        height: 250px;
        position: relative;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
    }

    .empty-icon {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 15px;
    }

    .empty-state h3 {
        color: #1e293b;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #64748b;
    }

    @media (max-width: 768px) {
        .welcome-section {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }
        
        .charts-grid {
            grid-template-columns: 1fr;
        }
        
        .unit-progress-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .progress-container {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="study-container">
    <!-- Welcome Section with Streak -->
    <div class="welcome-section">
        <div class="welcome-text">
            <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
            <p>Keep up the great work on your studies</p>
        </div>
        <div class="streak-badge">
            <div class="streak-number">{{ $streak['current'] }}</div>
            <div class="streak-label">Day Streak</div>
            <div style="font-size: 0.8rem; margin-top: 5px;">Longest: {{ $streak['longest'] }}</div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon units">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <h4>Enrolled Units</h4>
                <div class="stat-value">{{ $stats['total_units'] }}</div>
                <div class="stat-detail">{{ $stats['total_topics'] }} total topics</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon topics">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Topics Completed</h4>
                <div class="stat-value">{{ $stats['completed_topics'] }}</div>
                <div class="stat-detail">{{ $stats['in_progress_topics'] }} in progress</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon time">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Time Spent</h4>
                <div class="stat-value">
                    @php
                        $hours = floor($stats['total_time_spent'] / 60);
                        $minutes = $stats['total_time_spent'] % 60;
                    @endphp
                    {{ $hours }}h {{ $minutes }}m
                </div>
                <div class="stat-detail">Total study time</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon concepts">
                <i class="fas fa-brain"></i>
            </div>
            <div class="stat-info">
                <h4>Concepts Mastered</h4>
                <div class="stat-value">{{ $stats['mastered_concepts'] }}</div>
                <div class="stat-detail">Keep learning!</div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="overview-section">
        <div class="section-header">
            <h2><i class="fas fa-chart-pie"></i> Unit Progress</h2>
            <a href="{{ route('student.units.available') }}" class="view-all">
                View All Units <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        @if($enrolledUnits->count() > 0)
            <div class="unit-progress-list">
                @foreach($enrolledUnits as $unit)
                    @php
                        $unitProgress = $progress->filter(function($p) use ($unit) {
                            return $p->unit_code === $unit->code;
                        });
                        
                        $totalTopics = $unit->topics()->where('status', 'published')->count();
                        $completedTopics = $unitProgress->where('status', 'completed')->count();
                        $percentage = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
                    @endphp

                    <div class="unit-progress-item">
                        <div class="unit-info">
                            <h3>{{ $unit->name }}</h3>
                            <div class="unit-code">{{ $unit->code }}</div>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="progress-percentage">{{ $percentage }}%</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3>No Units Enrolled</h3>
                <p>Enroll in units to start tracking your progress</p>
                <a href="{{ route('student.units.available') }}" class="btn-primary" style="display: inline-block; margin-top: 15px; padding: 10px 25px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 8px; text-decoration: none;">
                    Browse Units
                </a>
            </div>
        @endif
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        <!-- Daily Activity Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-calendar-alt" style="color: #f59e0b; margin-right: 8px;"></i> Daily Study Time (30 days)</h3>
            </div>
            <div class="chart-container">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        <!-- Unit Progress Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie" style="color: #f59e0b; margin-right: 8px;"></i> Progress by Unit</h3>
            </div>
            <div class="chart-container">
                <canvas id="unitChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    @if($recentActivity->count() > 0)
        <div class="activity-timeline">
            <div class="section-header">
                <h2><i class="fas fa-history"></i> Recent Activity</h2>
            </div>

            <div class="timeline">
                @foreach($recentActivity as $activity)
                    <div class="timeline-item {{ $activity->status }}">
                        <div class="timeline-date">
                            {{ $activity->last_accessed_at->diffForHumans() }}
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">
                                <i class="fas {{ $activity->status_icon }}" style="color: {{ $activity->status === 'completed' ? '#10b981' : '#f59e0b' }};"></i>
                                {{ $activity->topic->title ?? 'Unknown Topic' }}
                            </div>
                            <div class="timeline-desc">
                                {{ $activity->unit_code }} • 
                                @if($activity->status === 'completed')
                                    Completed topic
                                @else
                                    {{ $activity->progress_percentage }}% complete • 
                                    {{ floor($activity->time_spent_minutes / 60) }}h {{ $activity->time_spent_minutes % 60 }}m spent
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Fetch statistics for charts - FIXED: Changed from student.study.statistics to study.statistics
    fetch('{{ route("study.statistics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Daily Activity Chart
                const dailyCtx = document.getElementById('dailyChart').getContext('2d');
                const dates = Object.keys(data.daily);
                const minutes = Object.values(data.daily);
                
                new Chart(dailyCtx, {
                    type: 'bar',
                    data: {
                        labels: dates.map(date => {
                            const d = new Date(date);
                            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        }),
                        datasets: [{
                            label: 'Minutes Studied',
                            data: minutes,
                            backgroundColor: '#f59e0b',
                            borderRadius: 6
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
                                title: {
                                    display: true,
                                    text: 'Minutes'
                                }
                            }
                        }
                    }
                });

                // Unit Progress Chart
                const unitCtx = document.getElementById('unitChart').getContext('2d');
                const unitNames = data.units.map(u => u.unit.code);
                const unitPercentages = data.units.map(u => u.percentage);
                
                new Chart(unitCtx, {
                    type: 'doughnut',
                    data: {
                        labels: unitNames,
                        datasets: [{
                            data: unitPercentages,
                            backgroundColor: [
                                '#f59e0b',
                                '#3b82f6',
                                '#10b981',
                                '#8b5cf6',
                                '#ef4444',
                                '#06b6d4'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
</script>
@endpush
@endsection