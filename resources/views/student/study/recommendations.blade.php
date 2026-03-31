@extends('student.layouts.master')

@section('title', 'Study Recommendations')
@section('page-icon', 'fa-lightbulb')
@section('page-title', 'Personalized Study Recommendations')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.study.index') }}">Study Progress</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recommendations</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .recommendations-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Header */
    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        color: white;
        text-align: center;
    }

    .header-section h1 {
        font-size: 2rem;
        margin-bottom: 15px;
    }

    .header-section p {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .section-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .section-icon.in-progress {
        background: #fef3c7;
        color: #f59e0b;
    }

    .section-icon.not-started {
        background: #dbeafe;
        color: #3b82f6;
    }

    .section-icon.ai {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .section-header h2 {
        color: #1e293b;
        font-size: 1.3rem;
        margin: 0;
    }

    .section-header p {
        color: #64748b;
        font-size: 0.9rem;
        margin: 5px 0 0 0;
    }

    /* Recommendation Cards */
    .recommendation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .recommendation-card {
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .recommendation-card:hover {
        transform: translateY(-3px);
        border-color: #f59e0b;
        box-shadow: 0 10px 30px rgba(245, 158, 11, 0.15);
    }

    .recommendation-card.in-progress {
        border-left: 4px solid #f59e0b;
    }

    .recommendation-card.not-started {
        border-left: 4px solid #3b82f6;
    }

    .recommendation-card.ai {
        background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .unit-code {
        background: #f59e0b;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .priority-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .priority-high {
        background: #fee2e2;
        color: #ef4444;
    }

    .priority-medium {
        background: #fef3c7;
        color: #f59e0b;
    }

    .priority-low {
        background: #d1fae5;
        color: #10b981;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 10px;
    }

    .card-description {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    /* Progress Bar */
    .progress-container {
        margin: 15px 0;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }

    .progress-bar {
        height: 8px;
        background: #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        border-radius: 20px;
        transition: width 0.3s ease;
    }

    /* Meta Info */
    .meta-info {
        display: flex;
        gap: 15px;
        margin: 15px 0;
        color: #64748b;
        font-size: 0.85rem;
    }

    .meta-info i {
        color: #f59e0b;
        margin-right: 5px;
    }

    /* Action Button */
    .card-action {
        margin-top: 15px;
        text-align: right;
    }

    .btn-study {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 10px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-study:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        color: white;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
        color: #1e293b;
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

    /* AI Insights */
    .ai-insights {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .insight-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .insight-item:last-child {
        border-bottom: none;
    }

    .insight-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 1.2rem;
    }

    .insight-content {
        flex: 1;
    }

    .insight-title {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .insight-desc {
        color: #64748b;
        font-size: 0.9rem;
    }

    /* Study Streak */
    .streak-card {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 16px;
        padding: 25px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .streak-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .streak-info h3 {
        font-size: 1.3rem;
        margin-bottom: 5px;
    }

    .streak-info p {
        opacity: 0.9;
    }

    .streak-days {
        margin-left: auto;
        text-align: center;
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
</style>
@endpush

@section('content')
<div class="recommendations-container">
    <!-- Header -->
    <div class="header-section">
        <h1><i class="fas fa-lightbulb"></i> Your Study Recommendations</h1>
        <p>Personalized suggestions based on your learning progress and study habits</p>
    </div>

    <!-- In Progress Section -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon in-progress">
                <i class="fas fa-spinner"></i>
            </div>
            <div>
                <h2>Continue Where You Left Off</h2>
                <p>Topics you're currently working on</p>
            </div>
        </div>

        @if($inProgress->count() > 0)
            <div class="recommendation-grid">
                @foreach($inProgress as $progress)
                    <div class="recommendation-card in-progress">
                        <div class="card-header">
                            <span class="unit-code">{{ $progress->unit_code }}</span>
                            <span class="priority-badge priority-high">
                                <i class="fas fa-clock"></i> In Progress
                            </span>
                        </div>
                        <h3 class="card-title">{{ $progress->topic->title }}</h3>
                        <p class="card-description">{{ Str::limit($progress->topic->description ?? 'Continue studying this topic', 80) }}</p>
                        
                        <div class="progress-container">
                            <div class="progress-label">
                                <span>Progress</span>
                                <span>{{ $progress->progress_percentage }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progress->progress_percentage }}%"></div>
                            </div>
                        </div>

                        <div class="meta-info">
                            <span><i class="fas fa-hourglass-half"></i> {{ $progress->formatted_time_spent }}</span>
                            @if($progress->last_accessed_at)
                                <span><i class="fas fa-history"></i> {{ $progress->last_accessed_at->diffForHumans() }}</span>
                            @endif
                        </div>

                        <div class="card-action">
                            <a href="{{ route('student.study.start', [$progress->unit_code, $progress->topic_id]) }}" class="btn-study">
                                <i class="fas fa-redo"></i> Continue
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <h3>No Topics In Progress</h3>
                <p>Start studying a topic to see it here</p>
            </div>
        @endif
    </div>

    <!-- Not Started Section -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon not-started">
                <i class="fas fa-play"></i>
            </div>
            <div>
                <h2>Ready to Start</h2>
                <p>Topics you haven't begun yet</p>
            </div>
        </div>

        @if(count($notStarted) > 0)
            <div class="recommendation-grid">
                @foreach($notStarted as $item)
                    <div class="recommendation-card not-started">
                        <div class="card-header">
                            <span class="unit-code">{{ $item['unit']->code }}</span>
                            <span class="priority-badge priority-low">
                                <i class="fas fa-circle"></i> Not Started
                            </span>
                        </div>
                        <h3 class="card-title">{{ $item['topic']->title }}</h3>
                        @if($item['topic']->description)
                            <p class="card-description">{{ Str::limit($item['topic']->description, 80) }}</p>
                        @endif
                        
                        <div class="meta-info">
                            @if($item['topic']->estimated_minutes)
                                <span><i class="far fa-clock"></i> {{ $item['topic']->formatted_time }}</span>
                            @endif
                        </div>

                        <div class="card-action">
                            <a href="{{ route('student.study.start', [$item['unit']->code, $item['topic']->id]) }}" class="btn-study">
                                <i class="fas fa-play"></i> Start Studying
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>All Topics Started!</h3>
                <p>You've started every available topic. Great job!</p>
            </div>
        @endif
    </div>

    <!-- AI-Powered Insights -->
    <div class="section-card ai">
        <div class="section-header">
            <div class="section-icon ai">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <h2>AI-Powered Insights</h2>
                <p>Smart recommendations based on your learning patterns</p>
            </div>
        </div>

        <div class="ai-insights">
            @php
                $totalTopics = $inProgress->count() + count($notStarted);
                $completedPercentage = 0; // This would come from actual stats
                $bestTime = "morning"; // This would come from analyzing study patterns
            @endphp

            <div class="insight-item">
                <div class="insight-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="insight-content">
                    <div class="insight-title">Best Time to Study</div>
                    <div class="insight-desc">You're most productive in the {{ $bestTime }}. Schedule your study sessions then!</div>
                </div>
            </div>

            <div class="insight-item">
                <div class="insight-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="insight-content">
                    <div class="insight-title">Pacing Suggestion</div>
                    <div class="insight-desc">Based on your progress, try completing 2 topics this week to stay on track.</div>
                </div>
            </div>

            <div class="insight-item">
                <div class="insight-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="insight-content">
                    <div class="insight-title">Concept Review</div>
                    <div class="insight-desc">Review previously mastered concepts every few days to strengthen retention.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
        <a href="{{ route('student.study.index') }}" class="btn-study btn-secondary">
            <i class="fas fa-chart-line"></i> View Progress Dashboard
        </a>
        <a href="{{ route('student.units.available') }}" class="btn-study">
            <i class="fas fa-layer-group"></i> Browse Units
        </a>
    </div>
</div>
@endsection