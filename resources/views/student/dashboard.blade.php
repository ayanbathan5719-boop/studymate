@extends('student.layouts.master')

@section('title', 'Student Dashboard')
@section('page-icon', 'fa-tachometer-alt')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Section --}}
    <div class="welcome-section">
        <h1>{{ $greeting }}, {{ Auth::user()->name }}!</h1>
        <p>Ready to continue your learning journey?</p>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        {{-- Enrolled Units --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['enrolled_units'] ?? 0 }}</h3>
                <p>Enrolled Units</p>
                <a href="{{ route('student.units.available') }}" class="stat-link">
                    View Units <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Resources Available --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['total_resources'] ?? 0 }}</h3>
                <p>Learning Resources</p>
                <a href="{{ route('student.resources.index') }}" class="stat-link">
                    Browse Resources <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['upcoming_deadlines'] ?? 0 }}</h3>
                <p>Upcoming Deadlines</p>
                <a href="{{ route('student.deadlines.index') }}" class="stat-link">
                    View Deadlines <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Study Streak --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['current_streak'] ?? 0 }}</h3>
                <p>Day Streak</p>
                <a href="{{ route('study.index') }}" class="stat-link">
                    View Progress <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Pending Requests Stat Card --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $pendingCount ?? 0 }}</h3>
                <p>Pending Requests</p>
                <a href="{{ route('student.units.requests') }}" class="stat-link">
                    View Requests <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="quick-actions-section">
        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
        <div class="quick-actions-grid">
            {{-- Continue Studying --}}
            <a href="{{ route('study.index') }}" class="quick-action-card">
                <div class="quick-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="fas fa-play"></i>
                </div>
                <div class="quick-info">
                    <h4>Continue Studying</h4>
                    <p>Pick up where you left off</p>
                </div>
            </a>

            {{-- Browse Resources --}}
            <a href="{{ route('student.resources.index') }}" class="quick-action-card">
                <div class="quick-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="quick-info">
                    <h4>Learning Resources</h4>
                    <p>Access study materials</p>
                </div>
            </a>

            {{-- Forum --}}
            <a href="{{ route('forum.index') }}" class="quick-action-card">
                <div class="quick-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="quick-info">
                    <h4>Forum</h4>
                    <p>Join discussions</p>
                </div>
            </a>

            {{-- My Requests Quick Action --}}
            <a href="{{ route('student.units.requests') }}" class="quick-action-card">
                <div class="quick-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="quick-info">
                    <h4>My Requests</h4>
                    <p>{{ $pendingCount ?? 0 }} pending {{ ($pendingCount ?? 0) == 1 ? 'request' : 'requests' }}</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Continue Studying Section --}}
    @if(isset($lastStudiedTopic) && $lastStudiedTopic)
    <div class="continue-studying-section">
        <h2><i class="fas fa-play-circle"></i> Continue Studying</h2>
        <div class="continue-card">
            <div class="continue-content">
                <div class="continue-header">
                    <span class="unit-badge">{{ $lastStudiedTopic->unit->code ?? 'N/A' }}</span>
                    <span class="topic-badge">Topic</span>
                </div>
                <h3 class="topic-title">{{ $lastStudiedTopic->name }}</h3>
                <p class="topic-description">{{ Str::limit($lastStudiedTopic->description ?? 'No description available', 100) }}</p>
                
                <div class="continue-progress">
                    <div class="progress-label">
                        <span>Continue where you left off</span>
                        <span>Ready to resume</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-fill" style="width: {{ $lastStudiedTopic->progress_percentage ?? 0 }}%"></div>
                    </div>
                </div>
                
                <a href="{{ route('topics.show', [$lastStudiedTopic->unit_id, $lastStudiedTopic->id]) }}" class="btn-continue">
                    <i class="fas fa-play"></i> Resume Studying
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Recommended Resources --}}
    @if(isset($recommendedResources) && $recommendedResources->count() > 0)
    <div class="recommended-section">
        <h2><i class="fas fa-star"></i> Recommended for You</h2>
        <div class="recommended-grid">
            @foreach($recommendedResources as $resource)
                <a href="{{ route('student.resources.show', $resource->id) }}" class="recommended-card">
                    <div class="resource-type {{ $resource->file_type }}">
                        @if($resource->file_type === 'pdf')
                            <i class="fas fa-file-pdf"></i>
                        @elseif($resource->file_type === 'video')
                            <i class="fas fa-video"></i>
                        @elseif($resource->file_type === 'link')
                            <i class="fas fa-link"></i>
                        @elseif($resource->file_type === 'document')
                            <i class="fas fa-file-word"></i>
                        @else
                            <i class="fas fa-file-alt"></i>
                        @endif
                    </div>
                    <div class="resource-info">
                        <h4>{{ Str::limit($resource->title, 40) }}</h4>
                        <p class="resource-meta">
                            <span class="unit-code">{{ $resource->unit->code ?? 'N/A' }}</span>
                            <span class="download-count">
                                <i class="fas fa-download"></i> {{ $resource->downloads_count ?? 0 }}
                            </span>
                        </p>
                        <p class="resource-author">
                            <i class="fas fa-user"></i> {{ $resource->user->name ?? 'Unknown' }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent Activity Grid --}}
    <div class="recent-activity-grid">
        {{-- Recent Resources --}}
        <div class="activity-card">
            <h3><i class="fas fa-folder-open"></i> New Resources</h3>
            <div class="activity-list">
                @forelse($recentResources as $resource)
                    <div class="activity-item">
                        <i class="fas fa-file"></i>
                        <div class="activity-content">
                            <a href="{{ route('student.resources.show', $resource->id) }}">
                                {{ Str::limit($resource->title, 40) }}
                            </a>
                            <span class="activity-time">
                                {{ $resource->created_at->diffForHumans() }}
                                <span class="unit-badge">{{ $resource->unit->code ?? 'N/A' }}</span>
                            </span>
                            <span class="activity-author">
                                <i class="fas fa-user"></i> {{ $resource->user->name ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No new resources</p>
                @endforelse
            </div>
            <a href="{{ route('student.resources.index') }}" class="view-all-link">
                View All Resources <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="activity-card">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Deadlines</h3>
            <div class="activity-list">
                @forelse($upcomingDeadlines as $deadline)
                    <div class="activity-item">
                        <i class="fas fa-clock"></i>
                        <div class="activity-content">
                            <a href="{{ route('student.deadlines.index') }}#deadline-{{ $deadline->id }}">
                                {{ Str::limit($deadline->title, 40) }}
                            </a>
                            <span class="activity-time due-date">
                                {{ $deadline->due_date->diffForHumans() }}
                                <span class="unit-badge">{{ $deadline->unit->code ?? 'N/A' }}</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No upcoming deadlines</p>
                @endforelse
            </div>
            <a href="{{ route('student.deadlines.index') }}" class="view-all-link">
                View All Deadlines <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- Recent Forum Posts --}}
        <div class="activity-card">
            <h3><i class="fas fa-comments"></i> Forum Activity</h3>
            <div class="activity-list">
                @forelse($recentForumPosts as $post)
                    <div class="activity-item">
                        <i class="fas fa-comment"></i>
                        <div class="activity-content">
                            <a href="{{ route('forum.show', $post->id) }}">
                                {{ Str::limit($post->title, 40) }}
                            </a>
                            <span class="activity-time">
                                {{ $post->created_at->diffForHumans() }}
                                <span class="unit-badge">{{ $post->unit->code ?? 'N/A' }}</span>
                            </span>
                            <span class="activity-author">
                                <i class="fas fa-user"></i> {{ $post->user->name ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No recent forum posts</p>
                @endforelse
            </div>
            <a href="{{ route('forum.index') }}" class="view-all-link">
                View Forum <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    {{-- Your Units Quick Access --}}
    @if($enrolledUnits->count() > 0)
    <div class="your-units-section">
        <h2><i class="fas fa-layer-group"></i> Your Units</h2>
        <div class="units-grid">
            @foreach($enrolledUnits as $unit)
                <a href="{{ route('topics.index', $unit->id) }}" class="unit-card">
                    <span class="unit-code">{{ $unit->code }}</span>
                    <h4>{{ Str::limit($unit->name, 50) }}</h4>
                    <div class="unit-stats">
                        <span><i class="fas fa-file"></i> {{ $unit->resources_count ?? 0 }} resources</span>
                        <span><i class="fas fa-comments"></i> {{ $unit->forum_posts_count ?? 0 }} posts</span>
                    </div>
                    <div class="unit-footer">
                        <span class="view-link">View Topics <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
}

.welcome-section {
    margin-bottom: 32px;
}

.welcome-section h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 8px;
}

.welcome-section p {
    color: #64748b;
    font-size: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
    border-color: #f59e0b;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 28px;
    color: white;
}

.stat-content h3 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
    transition: transform 0.2s ease;
}

.stat-content p {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.stat-link {
    color: #f59e0b;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.stat-link:hover {
    color: #d97706;
    gap: 10px;
}

.quick-actions-section {
    margin-bottom: 40px;
}

.quick-actions-section h2,
.recommended-section h2,
.your-units-section h2,
.continue-studying-section h2 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-actions-section h2 i,
.recommended-section h2 i,
.your-units-section h2 i,
.continue-studying-section h2 i {
    color: #f59e0b;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.quick-action-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
    border-color: #f59e0b;
}

.quick-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-icon i {
    font-size: 24px;
    color: white;
}

.quick-info h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
}

.quick-info p {
    color: #64748b;
    font-size: 0.8rem;
    margin: 0;
}

.continue-studying-section {
    margin-bottom: 40px;
}

.continue-card {
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border-radius: 24px;
    border: 1px solid #fde68a;
    padding: 24px;
    transition: all 0.3s ease;
}

.continue-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(245, 158, 11, 0.1);
}

.continue-content {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.continue-header {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.unit-badge {
    background: #f59e0b;
    color: white;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 600;
}

.topic-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 600;
}

.topic-title {
    font-size: 1.3rem;
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

.continue-progress {
    margin: 8px 0;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 8px;
}

.progress-bar-container {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #f59e0b;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.btn-continue {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    text-decoration: none;
    border-radius: 40px;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.2s ease;
    align-self: flex-start;
}

.btn-continue:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
}

.recommended-section {
    margin-bottom: 40px;
}

.recommended-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
}

.recommended-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    padding: 20px;
    display: flex;
    gap: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

.recommended-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
    border-color: #f59e0b;
}

.resource-type {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.resource-type.pdf {
    background: #fee2e2;
    color: #dc2626;
}

.resource-type.video {
    background: #dbeafe;
    color: #2563eb;
}

.resource-type.link {
    background: #fef3c7;
    color: #d97706;
}

.resource-type.document {
    background: #e0f2fe;
    color: #0284c7;
}

.resource-info {
    flex: 1;
}

.resource-info h4 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 8px;
}

.resource-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    margin-bottom: 4px;
}

.unit-code {
    background: #f1f5f9;
    color: #475569;
    padding: 2px 8px;
    border-radius: 12px;
}

.download-count {
    color: #64748b;
}

.download-count i {
    color: #f59e0b;
    font-size: 0.7rem;
}

.resource-author {
    font-size: 0.75rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 4px;
}

.resource-author i {
    color: #f59e0b;
    font-size: 0.7rem;
}

.recent-activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.activity-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    padding: 24px;
    transition: all 0.3s ease;
}

.activity-card:hover {
    border-color: #f59e0b;
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.15);
}

.activity-card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.activity-card h3 i {
    color: #f59e0b;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 16px;
    min-height: 200px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.activity-item i {
    color: #94a3b8;
    font-size: 0.9rem;
    margin-top: 3px;
}

.activity-content {
    flex: 1;
}

.activity-content a {
    color: #334155;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    display: block;
    margin-bottom: 4px;
    transition: color 0.2s ease;
}

.activity-content a:hover {
    color: #f59e0b;
}

.activity-time {
    color: #94a3b8;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 2px;
}

.activity-author {
    color: #94a3b8;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.activity-author i {
    color: #f59e0b;
    font-size: 0.6rem;
}

.due-date {
    color: #ef4444;
}

.empty-message {
    color: #94a3b8;
    text-align: center;
    padding: 20px;
}

.view-all-link {
    color: #f59e0b;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    margin-top: 8px;
}

.view-all-link:hover {
    color: #d97706;
    gap: 10px;
}

.your-units-section {
    margin-top: 20px;
}

.units-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.unit-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    padding: 20px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.unit-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
    border-color: #f59e0b;
}

.unit-code {
    font-size: 0.75rem;
    font-weight: 600;
    color: #f59e0b;
    background: #fffbeb;
    padding: 4px 12px;
    border-radius: 40px;
    display: inline-block;
    align-self: flex-start;
}

.unit-card h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.unit-stats {
    display: flex;
    gap: 16px;
    font-size: 0.8rem;
    color: #64748b;
}

.unit-stats i {
    color: #f59e0b;
    margin-right: 4px;
}

.unit-footer {
    margin-top: 8px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.view-link {
    color: #f59e0b;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .recommended-grid {
        grid-template-columns: 1fr;
    }
    
    .recent-activity-grid {
        grid-template-columns: 1fr;
    }
    
    .units-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Dashboard Card Effects - Hover animations and click interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effect to stat cards with click navigation
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-4px)';
            card.style.transition = 'transform 0.2s ease';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
        
        // Add click effect to navigate
        card.addEventListener('click', () => {
            const link = card.querySelector('.stat-link');
            if (link) {
                window.location.href = link.href;
            }
        });
    });
    
    // Add fade-in animation for cards
    const cards = document.querySelectorAll('.stat-card, .quick-action-card, .recommended-card, .activity-card, .unit-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
    // Add pulse effect to study streak if applicable
    const streakValue = document.querySelector('.stat-card:nth-child(4) .stat-value');
    if (streakValue && parseInt(streakValue.innerText) > 0) {
        setInterval(() => {
            streakValue.style.transform = 'scale(1.05)';
            setTimeout(() => {
                streakValue.style.transform = 'scale(1)';
            }, 200);
        }, 3000);
    }
    
    // Add progress bar animation
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
    
    // Add loading effect to continue button
    const continueBtn = document.querySelector('.btn-continue');
    if (continueBtn) {
        continueBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            setTimeout(() => {
                window.location.href = this.getAttribute('href');
            }, 200);
        });
    }
});
</script>
@endpush