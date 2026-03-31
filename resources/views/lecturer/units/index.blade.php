@extends('lecturer.layouts.master')

@section('title', 'My Units')
@section('page-icon', 'fa-layer-group')
@section('page-title', 'My Units')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Units</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .units-container {
        padding: 20px 0;
    }

    .units-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .unit-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .unit-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        border-color: #f59e0b;
    }

    .unit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        position: relative;
    }

    .unit-code {
        font-size: 0.8rem;
        font-weight: 600;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .unit-name {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .unit-course {
        font-size: 0.9rem;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .unit-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        padding: 15px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 4px;
    }

    .unit-actions {
        padding: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-action {
        flex: 1;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        color: #475569;
        font-size: 0.9rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        background: white;
        min-width: 100px;
    }

    .btn-action:hover {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
        grid-column: 1 / -1;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #334155;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 25px;
    }

    @media (max-width: 768px) {
        .units-grid {
            grid-template-columns: 1fr;
        }
        
        .btn-action {
            min-width: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="units-container">
    <div class="units-grid">
        @forelse($units as $unit)
            <div class="unit-card">
                <div class="unit-header">
                    <span class="unit-code">{{ $unit->code }}</span>
                    <h2 class="unit-name">{{ $unit->name }}</h2>
                    <div class="unit-course">
                        <i class="fas fa-book"></i> {{ $unit->course->name ?? 'No Course' }}
                    </div>
                </div>

                <div class="unit-stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $unit->resources_count }}</div>
                        <div class="stat-label">Resources</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $unit->forum_posts_count }}</div>
                        <div class="stat-label">Forum Posts</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $unit->students_count }}</div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>

                {{-- Unit Actions with Topics Button --}}
                <div class="unit-actions">
                    <a href="{{ route('lecturer.topics.index', $unit->code) }}" class="btn-action">
                        <i class="fas fa-list-ul"></i> Topics
                    </a>
                    <a href="{{ route('lecturer.resources.index', ['unit' => $unit->code]) }}" class="btn-action">
                        <i class="fas fa-file"></i> Resources
                    </a>
                    <a href="{{ route('lecturer.deadlines.index', ['unit' => $unit->id]) }}" class="btn-action">
                        <i class="fas fa-clock"></i> Deadlines
                    </a>
                    <a href="{{ route('lecturer.forum.index', ['unit' => $unit->code]) }}" class="btn-action">
                        <i class="fas fa-comments"></i> Forum
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-layer-group"></i>
                <h3>No Units Assigned</h3>
                <p>You haven't been assigned to any units yet.</p>
                <a href="/lecturer/dashboard" class="btn-primary" style="background: #f59e0b; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection