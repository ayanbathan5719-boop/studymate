@extends('student.layouts.master')

@section('title', $unit->code . ' - Topics')
@section('page-icon', 'fa-chart-line')
@section('page-title', $unit->code . ' - Topics')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $unit->code }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="topics-container">
    {{-- Unit Header --}}
    <div class="unit-header-card">
        <div class="unit-header-content">
            <div class="unit-badge">{{ $unit->code }}</div>
            <h1>{{ $unit->name }}</h1>
            @if($unit->description)
                <p class="unit-description">{{ $unit->description }}</p>
            @endif
        </div>
        
        <div class="unit-stats">
            <div class="stat">
                <span class="stat-value">{{ $topics->count() }}</span>
                <span class="stat-label">Topics</span>
            </div>
            <div class="stat">
                <span class="stat-value">{{ $unit->resources_count ?? 0 }}</span>
                <span class="stat-label">Resources</span>
            </div>
        </div>
    </div>

    {{-- Topics Grid --}}
    <div class="topics-grid">
        @forelse($topics as $topic)
            @php
                $topicProgress = $progress[$topic->id] ?? 0;
                $isCompleted = $topicProgress >= 100;
            @endphp
            
            <a href="{{ route('topics.show', [$unit->id, $topic->id]) }}" class="topic-card {{ $isCompleted ? 'completed' : '' }}">
                <div class="topic-header">
                    <div class="topic-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="topic-order">Topic {{ $loop->iteration }}</div>
                    @if($isCompleted)
                        <div class="completed-badge">
                            <i class="fas fa-check-circle"></i> Completed
                        </div>
                    @endif
                </div>
                
                <h3 class="topic-title">{{ $topic->name }}</h3>
                
                @if($topic->description)
                    <p class="topic-description">{{ Str::limit($topic->description, 80) }}</p>
                @endif
                
                <div class="topic-meta">
                    @if($topic->estimated_minutes)
                        <span class="meta-item">
                            <i class="fas fa-clock"></i> {{ $topic->estimated_minutes }} mins
                        </span>
                    @endif
                    
                    @php
                        $topicResourcesCount = $topic->resources_count ?? 0;
                    @endphp
                    <span class="meta-item">
                        <i class="fas fa-file"></i> {{ $topicResourcesCount }} resources
                    </span>
                </div>
                
                <div class="topic-progress">
                    <div class="progress-label">
                        <span>Progress</span>
                        <span>{{ $topicProgress }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $topicProgress }}%"></div>
                    </div>
                </div>
                
                <div class="topic-footer">
                    <span class="continue-link">
                        @if($isCompleted)
                            <i class="fas fa-redo"></i> Review Topic
                        @elseif($topicProgress > 0)
                            <i class="fas fa-play"></i> Continue
                        @else
                            <i class="fas fa-play"></i> Start Learning
                        @endif
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>No Topics Yet</h3>
                <p>Topics will appear here once the lecturer adds them.</p>
                <a href="{{ route('student.dashboard') }}" class="btn-back-dashboard">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        @endforelse
    </div>

    {{-- Overall Unit Progress --}}
    @if($topics->count() > 0)
        @php
            $totalTopics = $topics->count();
            $completedTopics = count(array_filter($progress, function($p) { return $p >= 100; }));
            $overallProgress = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
        @endphp
        
        <div class="overall-progress-card">
            <h3><i class="fas fa-chart-line"></i> Overall Unit Progress</h3>
            <div class="overall-progress">
                <div class="progress-bar-large">
                    <div class="progress-fill-large" style="width: {{ $overallProgress }}%"></div>
                </div>
                <div class="progress-stats">
                    <span><i class="fas fa-check-circle"></i> {{ $completedTopics }} of {{ $totalTopics }} topics completed</span>
                    <span class="progress-percentage">{{ $overallProgress }}% Complete</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Navigation --}}
    <div class="navigation-buttons">
        <a href="{{ route('student.dashboard') }}" class="btn-back-dashboard">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <a href="{{ route('student.resources.index', ['unit' => $unit->code]) }}" class="btn-view-resources">
            <i class="fas fa-folder-open"></i> View All Resources
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
.topics-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 24px;
}

.breadcrumb-item a {
    color: #64748b;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #f59e0b;
}

.breadcrumb-item.active {
    color: #0f172a;
    font-weight: 500;
}

.unit-header-card {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 24px;
    padding: 32px;
    margin-bottom: 32px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 24px;
}

.unit-header-content {
    flex: 2;
    min-width: 250px;
}

.unit-badge {
    background: rgba(255, 255, 255, 0.2);
    display: inline-block;
    padding: 6px 16px;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 16px;
    backdrop-filter: blur(5px);
}

.unit-header-content h1 {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.unit-description {
    font-size: 1rem;
    opacity: 0.9;
    line-height: 1.5;
    margin: 0;
}

.unit-stats {
    flex: 1;
    min-width: 180px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    justify-content: space-around;
    text-align: center;
}

.stat {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.8;
}

.topics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.topic-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 24px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.topic-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    border-color: #f59e0b;
}

.topic-card.completed {
    background: #f0fdf4;
    border-color: #86efac;
}

.topic-header {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.topic-icon {
    width: 36px;
    height: 36px;
    background: #fffbeb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f59e0b;
}

.topic-order {
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    padding: 4px 12px;
    border-radius: 40px;
}

.completed-badge {
    background: #10b981;
    color: white;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.topic-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.topic-description {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
}

.topic-meta {
    display: flex;
    gap: 16px;
    font-size: 0.8rem;
    color: #64748b;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.meta-item i {
    color: #f59e0b;
    font-size: 0.75rem;
}

.topic-progress {
    margin: 8px 0;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #64748b;
    margin-bottom: 6px;
}

.progress-bar {
    height: 6px;
    background: #f1f5f9;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #f59e0b;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.topic-card.completed .progress-fill {
    background: #10b981;
}

.topic-footer {
    margin-top: 8px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.continue-link {
    color: #f59e0b;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.topic-card.completed .continue-link {
    color: #10b981;
}

.topic-card:hover .continue-link i:last-child {
    transform: translateX(5px);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 24px;
    border: 2px dashed #f1f5f9;
}

.empty-icon i {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 20px;
}

.overall-progress-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    padding: 24px;
    margin-bottom: 32px;
}

.overall-progress-card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.overall-progress-card h3 i {
    color: #f59e0b;
}

.progress-bar-large {
    height: 12px;
    background: #f1f5f9;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 12px;
}

.progress-fill-large {
    height: 100%;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 6px;
    transition: width 0.3s ease;
}

.progress-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
    color: #64748b;
}

.progress-stats i {
    color: #10b981;
}

.progress-percentage {
    font-weight: 600;
    color: #f59e0b;
}

.navigation-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-back-dashboard,
.btn-view-resources {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back-dashboard {
    background: #f1f5f9;
    color: #475569;
}

.btn-back-dashboard:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

.btn-view-resources {
    background: #f59e0b;
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.btn-view-resources:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
}

@media (max-width: 768px) {
    .topics-container {
        padding: 16px;
    }
    
    .unit-header-card {
        flex-direction: column;
        text-align: center;
    }
    
    .unit-stats {
        width: 100%;
        justify-content: center;
        gap: 32px;
    }
    
    .topics-grid {
        grid-template-columns: 1fr;
    }
    
    .navigation-buttons {
        flex-direction: column;
    }
    
    .btn-back-dashboard,
    .btn-view-resources {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush