@extends('student.layouts.master')

@section('title', $unit->code . ' - ' . $unit->name)
@section('page-icon', 'fa-book')
@section('page-title', $unit->code . ': ' . $unit->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.units.available') }}">My Units</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $unit->code }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="unit-study-container">
    {{-- Unit Header --}}
    <div class="unit-header">
        <div class="unit-title">
            <h1>{{ $unit->code }}: {{ $unit->name }}</h1>
            <p class="unit-description">{{ $unit->description ?? 'Study materials and topics for this unit.' }}</p>
        </div>
        <div class="unit-progress">
            <div class="progress-circle">
                <span class="progress-value">{{ $overallProgress ?? 0 }}%</span>
            </div>
            <span class="progress-label">Overall Progress</span>
        </div>
    </div>

    {{-- Study Navigation Tabs --}}
    <div class="study-tabs">
        <button class="tab-btn active" data-tab="topics">📚 Topics</button>
        <button class="tab-btn" data-tab="resources">📄 All Resources</button>
        <button class="tab-btn" data-tab="progress">📊 My Progress</button>
    </div>

    {{-- Topics Tab --}}
    <div class="tab-content active" id="topics-tab">
        @if($topics && $topics->count() > 0)
            <div class="topics-grid">
                @foreach($topics as $topic)
                    <div class="topic-card" onclick="window.location.href='{{ route('topics.show', [$unit->id, $topic->id]) }}'">
                        <div class="topic-header">
                            <h3>{{ $topic->name }}</h3>
                            <span class="topic-progress">{{ $topic->progress_percentage ?? 0 }}%</span>
                        </div>
                        <p class="topic-description">{{ Str::limit($topic->description ?? 'No description', 100) }}</p>
                        <div class="topic-stats">
                            <span><i class="fas fa-file-alt"></i> {{ $topic->resources_count ?? 0 }} resources</span>
                            <span><i class="fas fa-check-circle"></i> {{ $topic->completed_resources ?? 0 }} studied</span>
                        </div>
                        <div class="topic-progress-bar">
                            <div class="progress-fill" style="width: {{ $topic->progress_percentage ?? 0 }}%"></div>
                        </div>
                        <button class="btn-study-topic">Continue Studying →</button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3>No Topics Yet</h3>
                <p>Topics will appear here once your lecturer adds them.</p>
                <a href="{{ route('student.resources.index', ['unit' => $unit->code]) }}" class="btn-primary">Browse Resources</a>
            </div>
        @endif
    </div>

    {{-- Resources Tab --}}
    <div class="tab-content" id="resources-tab">
        <div class="resources-grid">
            @forelse($resources as $resource)
                <div class="resource-card" onclick="window.location.href='{{ route('student.resources.viewer', $resource->id) }}'">
                    <div class="resource-type {{ $resource->file_type }}">
                        @if($resource->file_type === 'pdf')
                            <i class="fas fa-file-pdf"></i>
                        @elseif($resource->file_type === 'video')
                            <i class="fas fa-video"></i>
                        @elseif($resource->file_type === 'link')
                            <i class="fas fa-link"></i>
                        @else
                            <i class="fas fa-file-alt"></i>
                        @endif
                    </div>
                    <div class="resource-info">
                        <h4>{{ $resource->title }}</h4>
                        <p class="resource-meta">
                            <span><i class="fas fa-download"></i> {{ $resource->download_count ?? 0 }}</span>
                            <span><i class="fas fa-eye"></i> {{ $resource->views_count ?? 0 }}</span>
                        </p>
                        @if($resource->topic)
                            <span class="topic-tag">{{ $resource->topic->name }}</span>
                        @endif
                    </div>
                    <div class="resource-actions">
                        <button class="btn-view" onclick="event.stopPropagation(); window.location.href='{{ route('student.resources.viewer', $resource->id) }}'">
                            <i class="fas fa-play"></i> Study
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Resources Yet</h3>
                    <p>Resources will appear here once your lecturer adds them.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Progress Tab --}}
    <div class="tab-content" id="progress-tab">
        <div class="progress-overview">
            <div class="overall-progress-card">
                <h3>Overall Progress</h3>
                <div class="big-progress-circle">
                    <span class="big-progress-value">{{ $overallProgress ?? 0 }}%</span>
                </div>
                <p>{{ $completedTopics ?? 0 }} of {{ $totalTopics ?? 0 }} topics completed</p>
            </div>
            
            <div class="recent-activity-card">
                <h3>Recent Activity</h3>
                @forelse($recentActivity ?? [] as $activity)
                    <div class="activity-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Studied {{ $activity->resource->title ?? 'a resource' }}</strong>
                            <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No recent activity. Start studying!</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Continue Studying Section (if last studied) --}}
    @if($lastStudiedTopic)
    <div class="continue-studying-banner">
        <div class="banner-content">
            <div class="banner-icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="banner-info">
                <h3>Continue Studying</h3>
                <p>You were last studying <strong>{{ $lastStudiedTopic->name }}</strong></p>
            </div>
            <a href="{{ route('topics.show', [$unit->id, $lastStudiedTopic->id]) }}" class="btn-resume">
                Resume <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.unit-study-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
}

.unit-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.unit-title h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
}

.unit-description {
    color: #6b7280;
    font-size: 0.9rem;
}

.progress-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.progress-value {
    font-size: 1.2rem;
    font-weight: bold;
    color: white;
}

.progress-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-align: center;
}

.study-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 1px solid #e5e7eb;
}

.tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    font-size: 1rem;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
}

.tab-btn.active {
    color: #f59e0b;
    border-bottom: 2px solid #f59e0b;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.topics-grid, .resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.topic-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
    padding: 20px;
    cursor: pointer;
    transition: all 0.2s;
}

.topic-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    border-color: #f59e0b;
}

.topic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.topic-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
}

.topic-progress {
    background: #fef3c7;
    color: #d97706;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.topic-description {
    color: #6b7280;
    font-size: 0.85rem;
    line-height: 1.5;
    margin-bottom: 12px;
}

.topic-stats {
    display: flex;
    gap: 16px;
    font-size: 0.75rem;
    color: #9ca3af;
    margin-bottom: 12px;
}

.topic-stats i {
    margin-right: 4px;
}

.topic-progress-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 16px;
}

.progress-fill {
    height: 100%;
    background: #f59e0b;
    border-radius: 3px;
    transition: width 0.3s;
}

.btn-study-topic {
    width: 100%;
    padding: 10px;
    background: #f3f4f6;
    border: none;
    border-radius: 12px;
    color: #374151;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-study-topic:hover {
    background: #f59e0b;
    color: white;
}

.resource-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 16px;
    display: flex;
    gap: 16px;
    cursor: pointer;
    transition: all 0.2s;
}

.resource-card:hover {
    border-color: #f59e0b;
    transform: translateY(-2px);
}

.resource-type {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.resource-type.pdf { background: #fee2e2; color: #dc2626; }
.resource-type.video { background: #dbeafe; color: #2563eb; }
.resource-type.link { background: #fef3c7; color: #d97706; }

.resource-info {
    flex: 1;
}

.resource-info h4 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
}

.resource-meta {
    display: flex;
    gap: 12px;
    font-size: 0.7rem;
    color: #9ca3af;
    margin-bottom: 8px;
}

.topic-tag {
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    color: #6b7280;
}

.resource-actions .btn-view {
    background: #f59e0b;
    color: white;
    border: none;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.75rem;
    cursor: pointer;
}

.continue-studying-banner {
    margin-top: 32px;
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid #fde68a;
}

.banner-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.banner-icon i {
    font-size: 48px;
    color: #f59e0b;
}

.banner-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 4px;
}

.banner-info p {
    color: #6b7280;
    font-size: 0.85rem;
}

.btn-resume {
    margin-left: auto;
    background: #f59e0b;
    color: white;
    padding: 10px 24px;
    border-radius: 40px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-resume:hover {
    background: #d97706;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 1.2rem;
    color: #374151;
    margin-bottom: 8px;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 20px;
}

.btn-primary {
    background: #f59e0b;
    color: white;
    padding: 10px 24px;
    border-radius: 40px;
    text-decoration: none;
    display: inline-block;
}

@media (max-width: 768px) {
    .unit-header {
        flex-direction: column;
        text-align: center;
    }
    
    .banner-content {
        flex-direction: column;
        text-align: center;
    }
    
    .btn-resume {
        margin-left: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tabId = btn.getAttribute('data-tab');
        
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(tabId + '-tab').classList.add('active');
    });
});
</script>
@endpush
@endsection