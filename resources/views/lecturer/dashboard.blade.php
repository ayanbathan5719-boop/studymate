@extends('lecturer.layouts.master')

@section('title', 'Lecturer Dashboard')
@section('page-icon', 'fa-tachometer-alt')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Section --}}
    <div class="welcome-section">
        <h1>Welcome back, {{ Auth::user()->name }}!</h1>
        <p>Here's what's happening with your units.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        {{-- My Units --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['units_count'] ?? 0 }}</h3>
                <p>My Units</p>
                <a href="{{ route('lecturer.units.index') }}" class="stat-link">
                    View Units <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Resources Stats --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="stat-content">
                {{-- CHANGED: from 'total_resources' to 'resources_count' to match controller --}}
                <h3>{{ $stats['resources_count'] ?? 0 }}</h3>
                <p>My Resources</p>
                <a href="{{ route('lecturer.resources.index') }}" class="stat-link">
                    View Resources <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Active Deadlines --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['active_deadlines'] ?? 0 }}</h3>
                <p>Active Deadlines</p>
                <a href="{{ route('lecturer.deadlines.index') }}" class="stat-link">
                    View Deadlines <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Forum Activity --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['forum_posts_count'] ?? 0 }}</h3>
                <p>Forum Posts</p>
                <a href="{{ route('lecturer.forum.index') }}" class="stat-link">
                    View Forum <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="quick-actions-section">
        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
        <div class="quick-actions-grid">
            {{-- Upload Resource --}}
            <a href="{{ route('lecturer.resources.create') }}" class="quick-action-card">
                <div class="quick-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-upload"></i>
                </div>
                <div class="quick-info">
                    <h4>Upload Resource</h4>
                    <p>Share learning materials with students</p>
                </div>
            </a>

            {{-- Create Deadline --}}
            <a href="{{ route('lecturer.deadlines.create') }}" class="quick-action-card">
                <div class="quick-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="quick-info">
                    <h4>Set Deadline</h4>
                    <p>Create assignment deadlines</p>
                </div>
            </a>

            {{-- Create Forum Post --}}
            <a href="{{ route('lecturer.forum.create') }}" class="quick-action-card">
                <div class="quick-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="quick-info">
                    <h4>Create Post</h4>
                    <p>Start a forum discussion</p>
                </div>
            </a>

            {{-- Manage Topics --}}
            <a href="#" onclick="return false;" class="quick-action-card has-submenu">
                <div class="quick-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="quick-info">
                    <h4>Manage Topics</h4>
                    <p>Select a unit below</p>
                </div>
            </a>
        </div>

        {{-- Unit-specific topic links --}}
        @if(isset($myUnits) && $myUnits->count() > 0)
            <div class="unit-topics-grid">
                @foreach($myUnits as $unit)
                    <a href="{{ route('lecturer.topics.index', $unit->id) }}" class="unit-topic-link">
                        <span class="unit-code">{{ $unit->code }}</span>
                        <span class="unit-name">{{ Str::limit($unit->name, 30) }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Activity Grid --}}
    <div class="recent-activity-grid">
        {{-- Recent Resources --}}
        <div class="activity-card">
            <h3><i class="fas fa-folder-open"></i> Recently Uploaded</h3>
            <div class="activity-list">
                @forelse($recentResources as $resource)
                    <div class="activity-item">
                        <i class="fas fa-file"></i>
                        <div class="activity-content">
                            <a href="{{ route('lecturer.resources.show', $resource->id) }}">
                                {{ Str::limit($resource->title, 40) }}
                            </a>
                            <span class="activity-time">{{ $resource->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No resources uploaded yet</p>
                @endforelse
            </div>
            <a href="{{ route('lecturer.resources.index') }}" class="view-all-link">
                View All Resources <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="activity-card">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Deadlines</h3>
            <div class="activity-list">
                @forelse($pendingDeadlines as $deadline)
                    <div class="activity-item">
                        <i class="fas fa-clock"></i>
                        <div class="activity-content">
                            <a href="{{ route('lecturer.deadlines.edit', $deadline->id) }}">
                                {{ Str::limit($deadline->title, 40) }}
                            </a>
                            <span class="activity-time due-date {{ isset($deadline->due_date) && $deadline->due_date->isPast() ? 'overdue' : '' }}">
                                {{ isset($deadline->due_date) ? $deadline->due_date->diffForHumans() : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No upcoming deadlines</p>
                @endforelse
            </div>
            <a href="{{ route('lecturer.deadlines.index') }}" class="view-all-link">
                View All Deadlines <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- Recent Forum Posts --}}
        <div class="activity-card">
            <h3><i class="fas fa-comments"></i> Recent Forum Activity</h3>
            <div class="activity-list">
                @forelse($recentForumPosts as $post)
                    <div class="activity-item">
                        <i class="fas fa-comment"></i>
                        <div class="activity-content">
                            <a href="{{ route('lecturer.forum.show', $post->id) }}">
                                {{ Str::limit($post->title, 40) }}
                            </a>
                            <span class="activity-time">
                                {{ $post->created_at->diffForHumans() }}
                                <span class="unit-badge">{{ $post->unit->code ?? 'N/A' }}</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No recent forum posts</p>
                @endforelse
            </div>
            <a href="{{ route('lecturer.forum.index') }}" class="view-all-link">
                View Forum <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
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
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
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

.quick-actions-section h2 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-actions-section h2 i {
    color: #f59e0b;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 16px;
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
    transition: all 0.2s ease;
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

.unit-topics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
    margin-top: 8px;
}

.unit-topic-link {
    background: #f8fafc;
    border-radius: 16px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.unit-topic-link:hover {
    background: #fffbeb;
    transform: translateX(5px);
}

.unit-code {
    font-size: 0.75rem;
    font-weight: 600;
    color: #f59e0b;
    background: white;
    padding: 4px 8px;
    border-radius: 20px;
}

.unit-name {
    flex: 1;
    color: #334155;
    font-size: 0.85rem;
}

.unit-topic-link i {
    color: #f59e0b;
    font-size: 0.8rem;
    opacity: 0;
    transition: all 0.2s ease;
}

.unit-topic-link:hover i {
    opacity: 1;
    transform: translateX(5px);
}

.recent-activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 24px;
}

.activity-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    padding: 24px;
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
}

.due-date.overdue {
    color: #ef4444;
}

.unit-badge {
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 12px;
    color: #475569;
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

    .unit-topics-grid {
        grid-template-columns: 1fr;
    }

    .recent-activity-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
