@extends('lecturer.layouts.master')

@section('title', 'Unit Details')
@section('page-icon', 'fa-layer-group')
@section('page-title', 'Unit Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/lecturer/units">My Units</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $unit->code }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="unit-show-container">
    <div class="row">
        <div class="col-md-8">
            <!-- Unit Details Card -->
            <div class="unit-details-card">
                <div class="unit-header">
                    <h1>{{ $unit->name }}</h1>
                    <span class="unit-code-badge">{{ $unit->code }}</span>
                </div>
                
                <div class="unit-meta">
                    <p><i class="fas fa-book"></i> <strong>Course:</strong> {{ $unit->course->name }}</p>
                    <p><i class="fas fa-users"></i> <strong>Enrolled Students:</strong> {{ $unit->students_count ?? 0 }}</p>
                </div>
                
                <div class="unit-description">
                    <h3>Description</h3>
                    <p>{{ $unit->description ?: 'No description provided.' }}</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-file"></i>
                    <div class="stat-content">
                        <span class="stat-value">{{ $unit->resources_count ?? 0 }}</span>
                        <span class="stat-label">Resources</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-comments"></i>
                    <div class="stat-content">
                        <span class="stat-value">{{ $unit->forum_posts_count ?? 0 }}</span>
                        <span class="stat-label">Forum Posts</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-content">
                        <span class="stat-value">{{ $unit->active_deadlines_count ?? 0 }}</span>
                        <span class="stat-label">Active Deadlines</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="actions-card">
                <h3>Quick Actions</h3>
                <a href="/lecturer/resources?unit={{ $unit->id }}" class="action-btn">
                    <i class="fas fa-file"></i> View Resources
                </a>
                <a href="/lecturer/resources/create?unit={{ $unit->id }}" class="action-btn">
                    <i class="fas fa-upload"></i> Upload Resource
                </a>
                <a href="/lecturer/deadlines?unit={{ $unit->id }}" class="action-btn">
                    <i class="fas fa-clock"></i> View Deadlines
                </a>
                <a href="/lecturer/deadlines/create?unit={{ $unit->id }}" class="action-btn">
                    <i class="fas fa-calendar-plus"></i> Set Deadline
                </a>
                <a href="/lecturer/forum?unit={{ $unit->code }}" class="action-btn">
                    <i class="fas fa-comments"></i> Go to Forum
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.unit-show-container {
    padding: 20px 0;
}

.row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.unit-details-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 25px;
}

.unit-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.unit-header h1 {
    font-size: 1.8rem;
    color: #1e293b;
    margin: 0;
}

.unit-code-badge {
    background: #f59e0b;
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.unit-meta {
    margin-bottom: 20px;
}

.unit-meta p {
    margin-bottom: 8px;
    color: #475569;
}

.unit-meta i {
    color: #f59e0b;
    width: 20px;
    margin-right: 8px;
}

.unit-description h3 {
    color: #1e293b;
    font-size: 1.1rem;
    margin-bottom: 10px;
}

.unit-description p {
    color: #475569;
    line-height: 1.6;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-2px);
    border-color: #f59e0b;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-card i {
    font-size: 24px;
    color: #f59e0b;
    background: #fffbeb;
    padding: 12px;
    border-radius: 10px;
}

.stat-content {
    flex: 1;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}

.stat-label {
    color: #64748b;
    font-size: 0.8rem;
}

.actions-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}

.actions-card h3 {
    color: #1e293b;
    font-size: 1.1rem;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: #f8fafc;
    color: #475569;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
}

.action-btn:hover {
    background: #fffbeb;
    border-color: #f59e0b;
    color: #f59e0b;
    transform: translateX(5px);
}

.action-btn i {
    width: 20px;
    color: #f59e0b;
}

@media (max-width: 768px) {
    .row {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection