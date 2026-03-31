@extends('student.layouts.master')

@section('title', 'Unit Progress - ' . $unit->code)
@section('page-icon', 'fa-chart-line')
@section('page-title', $unit->name . ' - Progress')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.study.index') }}">Study Progress</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $unit->code }}</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .unit-progress-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Unit Header */
    .unit-header {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .unit-header h1 {
        color: #1e293b;
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .unit-code {
        color: #f59e0b;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    /* Progress Overview */
    .overview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .overview-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .overview-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .overview-icon.topics {
        background: #fef3c7;
        color: #f59e0b;
    }

    .overview-icon.completed {
        background: #d1fae5;
        color: #10b981;
    }

    .overview-icon.time {
        background: #dbeafe;
        color: #3b82f6;
    }

    .overview-icon.concepts {
        background: #ede9fe;
        color: #8b5cf6;
    }

    .overview-info h4 {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .overview-value {
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .overview-detail {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    /* Progress Circle */
    .progress-circle-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .progress-circle {
        width: 200px;
        height: 200px;
        margin: 0 auto 20px;
        position: relative;
    }

    .progress-circle canvas {
        width: 100%;
        height: 100%;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .progress-percentage {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
    }

    .progress-label {
        color: #64748b;
        font-size: 0.9rem;
    }

    .progress-stats {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 20px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1e293b;
    }

    .stat-name {
        color: #64748b;
        font-size: 0.85rem;
    }

    /* Topics List */
    .topics-section {
        background: white;
        border-radius: 16px;
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

    .topic-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .topic-item {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.2s ease;
    }

    .topic-item:hover {
        border-color: #f59e0b;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .topic-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .topic-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .topic-title h3 {
        color: #1e293b;
        font-size: 1.1rem;
        margin: 0;
    }

    .topic-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-completed {
        background: #d1fae5;
        color: #10b981;
    }

    .status-in-progress {
        background: #fef3c7;
        color: #f59e0b;
    }

    .status-not-started {
        background: #f1f5f9;
        color: #64748b;
    }

    .topic-progress {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 10px 0;
    }

    .progress-bar-container {
        flex: 1;
        height: 8px;
        background: #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
    }

    .topic-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        border-radius: 20px;
        transition: width 0.3s ease;
    }

    .topic-percentage {
        min-width: 45px;
        font-weight: 600;
        color: #1e293b;
    }

    .topic-meta {
        display: flex;
        gap: 20px;
        color: #64748b;
        font-size: 0.85rem;
    }

    .topic-meta i {
        color: #f59e0b;
        margin-right: 5px;
    }

    .topic-actions {
        margin-top: 10px;
        text-align: right;
    }

    .btn-study {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-study:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
        color: white;
    }

    .btn-study i {
        font-size: 0.9rem;
    }

    /* Concepts Section */
    .concepts-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .concepts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .concept-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
    }

    .concept-card h3 {
        color: #1e293b;
        font-size: 1rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .concept-card h3 i {
        color: #f59e0b;
    }

    .concept-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .concept-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .concept-item:last-child {
        border-bottom: none;
    }

    .concept-item i {
        width: 20px;
        color: #10b981;
    }

    .concept-item.in-progress i {
        color: #f59e0b;
    }

    .concept-name {
        flex: 1;
        color: #1e293b;
        font-size: 0.95rem;
    }

    /* Recommendations */
    .recommendations-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #e2e8f0;
    }

    .recommendation-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .recommendation-item:last-child {
        border-bottom: none;
    }

    .recommendation-icon {
        width: 40px;
        height: 40px;
        background: #fef3c7;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
    }

    .recommendation-content {
        flex: 1;
    }

    .recommendation-title {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .recommendation-desc {
        color: #64748b;
        font-size: 0.85rem;
    }

    .recommendation-action {
        color: #f59e0b;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .concepts-grid {
            grid-template-columns: 1fr;
        }
        
        .topic-meta {
            flex-wrap: wrap;
            gap: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="unit-progress-container">
    <!-- Unit Header -->
    <div class="unit-header">
        <h1>{{ $unit->name }}</h1>
        <div class="unit-code"><i class="fas fa-code"></i> {{ $unit->code }}</div>
    </div>

    <!-- Overview Cards -->
    <div class="overview-grid">
        <div class="overview-card">
            <div class="overview-icon topics">
                <i class="fas fa-list-ul"></i>
            </div>
            <div class="overview-info">
                <h4>Total Topics</h4>
                <div class="overview-value">{{ $totalTopics }}</div>
            </div>
        </div>

        <div class="overview-card">
            <div class="overview-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="overview-info">
                <h4>Completed</h4>
                <div class="overview-value">{{ $completedTopics }}</div>
                <div class="overview-detail">{{ $inProgressTopics }} in progress</div>
            </div>
        </div>

        <div class="overview-card">
            <div class="overview-icon time">
                <i class="fas fa-clock"></i>
            </div>
            <div class="overview-info">
                <h4>Time Spent</h4>
                <div class="overview-value">
                    @php
                        $hours = floor($totalTimeSpent / 60);
                        $minutes = $totalTimeSpent % 60;
                    @endphp
                    {{ $hours }}h {{ $minutes }}m
                </div>
            </div>
        </div>

        <div class="overview-card">
            <div class="overview-icon concepts">
                <i class="fas fa-brain"></i>
            </div>
            <div class="overview-info">
                <h4>Concepts</h4>
                <div class="overview-value">{{ count($masteredConcepts) }}</div>
                <div class="overview-detail">mastered</div>
            </div>
        </div>
    </div>

    <!-- Progress Circle -->
    <div class="progress-circle-container">
        @php
            $percentage = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
        @endphp
        <div class="progress-circle">
            <canvas id="progressCircle" width="200" height="200"></canvas>
            <div class="progress-text">
                <div class="progress-percentage">{{ $percentage }}%</div>
                <div class="progress-label">Complete</div>
            </div>
        </div>
        <div class="progress-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $completedTopics }}</div>
                <div class="stat-name">Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $inProgressTopics }}</div>
                <div class="stat-name">In Progress</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $totalTopics - $completedTopics - $inProgressTopics }}</div>
                <div class="stat-name">Not Started</div>
            </div>
        </div>
    </div>

    <!-- Topics List -->
    <div class="topics-section">
        <div class="section-header">
            <h2><i class="fas fa-list-ul"></i> Topics</h2>
        </div>

        @if($topics->count() > 0)
            <div class="topic-list">
                @foreach($topics as $topic)
                    @php
                        $topicProgress = $progress[$topic->id] ?? null;
                        $status = $topicProgress ? $topicProgress->status : 'not_started';
                        $progressPercentage = $topicProgress ? $topicProgress->progress_percentage : 0;
                    @endphp

                    <div class="topic-item">
                        <div class="topic-header">
                            <div class="topic-title">
                                <h3>{{ $topic->title }}</h3>
                                <span class="topic-status status-{{ $status }}">
                                    @if($status == 'completed')
                                        <i class="fas fa-check-circle"></i> Completed
                                    @elseif($status == 'in_progress')
                                        <i class="fas fa-spinner"></i> In Progress
                                    @else
                                        <i class="fas fa-circle"></i> Not Started
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if($status != 'not_started')
                            <div class="topic-progress">
                                <div class="progress-bar-container">
                                    <div class="topic-progress-bar" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                                <span class="topic-percentage">{{ $progressPercentage }}%</span>
                            </div>
                        @endif

                        <div class="topic-meta">
                            @if($topic->estimated_minutes)
                                <span><i class="far fa-clock"></i> {{ $topic->formatted_time }}</span>
                            @endif
                            @if($topicProgress && $topicProgress->time_spent_minutes > 0)
                                <span><i class="fas fa-hourglass-half"></i> Spent: {{ $topicProgress->formatted_time_spent }}</span>
                            @endif
                            @if($topicProgress && $topicProgress->last_accessed_at)
                                <span><i class="fas fa-history"></i> Last: {{ $topicProgress->last_accessed_at->diffForHumans() }}</span>
                            @endif
                        </div>

                        <div class="topic-actions">
                            <a href="{{ route('student.study.start', [$unit->code, $topic->id]) }}" class="btn-study">
                                <i class="fas {{ $status == 'not_started' ? 'fa-play' : 'fa-redo' }}"></i>
                                {{ $status == 'not_started' ? 'Start Studying' : 'Continue' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-list-ul" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <h3 style="color: #1e293b;">No Topics Yet</h3>
                <p style="color: #64748b;">The lecturer hasn't added any topics for this unit.</p>
            </div>
        @endif
    </div>

    <!-- Concepts Section -->
    <div class="concepts-section">
        <div class="section-header">
            <h2><i class="fas fa-brain"></i> Concepts Mastered</h2>
        </div>

        <div class="concepts-grid">
            <div class="concept-card">
                <h3><i class="fas fa-check-circle" style="color: #10b981;"></i> Mastered Concepts</h3>
                @if(count($masteredConcepts) > 0)
                    <ul class="concept-list">
                        @foreach($masteredConcepts as $concept)
                            <li class="concept-item">
                                <i class="fas fa-check-circle"></i>
                                <span class="concept-name">{{ $concept }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #64748b; text-align: center;">No concepts mastered yet. Keep studying!</p>
                @endif
            </div>

            <div class="concept-card">
                <h3><i class="fas fa-spinner" style="color: #f59e0b;"></i> In Progress</h3>
                @if(count($conceptsInProgress) > 0)
                    <ul class="concept-list">
                        @foreach($conceptsInProgress as $concept)
                            <li class="concept-item in-progress">
                                <i class="fas fa-circle"></i>
                                <span class="concept-name">{{ $concept }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #64748b; text-align: center;">No concepts in progress.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="recommendations-section">
        <div class="section-header">
            <h2><i class="fas fa-lightbulb"></i> Recommended Next Steps</h2>
        </div>

        @php
            $nextTopic = $topics->first(function($topic) use ($progress) {
                $p = $progress[$topic->id] ?? null;
                return !$p || $p->status != 'completed';
            });
        @endphp

        @if($nextTopic)
            <div class="recommendation-item">
                <div class="recommendation-icon">
                    <i class="fas fa-play"></i>
                </div>
                <div class="recommendation-content">
                    <div class="recommendation-title">Continue with {{ $nextTopic->title }}</div>
                    <div class="recommendation-desc">This is your next topic to study</div>
                </div>
                <a href="{{ route('student.study.start', [$unit->code, $nextTopic->id]) }}" class="recommendation-action">
                    Start <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @endif

        @if($inProgressTopics > 0)
            <div class="recommendation-item">
                <div class="recommendation-icon">
                    <i class="fas fa-redo"></i>
                </div>
                <div class="recommendation-content">
                    <div class="recommendation-title">Review in-progress topics</div>
                    <div class="recommendation-desc">You have {{ $inProgressTopics }} topics in progress</div>
                </div>
                <a href="#topics" class="recommendation-action">
                    Review <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Progress Circle
    const canvas = document.getElementById('progressCircle');
    const ctx = canvas.getContext('2d');
    const percentage = {{ $percentage }};
    
    // Draw circle
    const x = canvas.width / 2;
    const y = canvas.height / 2;
    const radius = 80;
    const startAngle = -Math.PI / 2;
    const endAngle = (percentage / 100) * 2 * Math.PI + startAngle;
    
    // Background circle
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, 2 * Math.PI);
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth = 12;
    ctx.stroke();
    
    // Progress circle
    ctx.beginPath();
    ctx.arc(x, y, radius, startAngle, endAngle);
    ctx.strokeStyle = '#f59e0b';
    ctx.lineWidth = 12;
    ctx.lineCap = 'round';
    ctx.stroke();
</script>
@endpush
@endsection