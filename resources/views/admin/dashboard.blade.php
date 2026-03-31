@extends('admin.layouts.master')

@section('title', 'Admin Dashboard')
@section('page-icon', 'fa-tachometer-alt')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Section --}}
    <div class="welcome-section">
        <h1>Welcome back, {{ Auth::user()->name }}!</h1>
        <p>Here's what's happening with StudyMate today.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        {{-- Total Courses --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_courses'] ?? 0 }}</h3>
                <p>Total Courses</p>
                <a href="{{ route('admin.courses.index') }}" class="stat-link">
                    View Courses <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Total Units --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_units'] ?? 0 }}</h3>
                <p>Total Units</p>
                <a href="{{ route('admin.units.index') }}" class="stat-link">
                    View Units <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Resources Stat Card --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_resources'] ?? 0 }}</h3>
                <p>Total Resources</p>
                <a href="{{ route('resources.index') }}" class="stat-link">
                    Manage Resources <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Total Lecturers --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_lecturers'] ?? 0 }}</h3>
                <p>Total Lecturers</p>
                <a href="{{ route('admin.lecturers.index') }}" class="stat-link">
                    View Lecturers <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Total Students --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_students'] ?? 0 }}</h3>
                <p>Total Students</p>
                <a href="{{ route('admin.students.index') }}" class="stat-link">
                    View Students <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Forum Posts --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_forum_posts'] ?? 0 }}</h3>
                <p>Forum Posts</p>
                <a href="{{ route('admin.forum.index') }}" class="stat-link">
                    View Forum <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Activity Section --}}
    <div class="recent-activity">
        <div class="section-header">
            <h2><i class="fas fa-history"></i> Recent Activity</h2>
        </div>
        
        <div class="activity-list">
            @forelse($recentActivities ?? [] as $activity)
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-circle"></i>
                    </div>
                    <div class="activity-content">
                        <p>{{ $activity->description }}</p>
                        <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>No recent activity</p>
                </div>
            @endforelse
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
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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

.stat-content {
    flex: 1;
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

.recent-activity {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    padding: 24px;
}

.section-header h2 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-header h2 i {
    color: #f59e0b;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border-radius: 16px;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: #f8fafc;
}

.activity-icon i {
    color: #f59e0b;
    font-size: 8px;
    margin-top: 8px;
}

.activity-content {
    flex: 1;
}

.activity-content p {
    color: #334155;
    margin-bottom: 4px;
}

.activity-time {
    color: #94a3b8;
    font-size: 0.8rem;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #94a3b8;
}

.empty-state i {
    font-size: 40px;
    margin-bottom: 12px;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        padding: 20px;
    }
}
</style>
@endpush