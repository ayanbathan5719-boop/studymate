@extends('student.layouts.master')

@section('title', $topic->name . ' - Resources')
@section('page-icon', 'fa-file-alt')
@section('page-title', $topic->name)

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('topics.index', $unit->id) }}">{{ $unit->code }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $topic->name }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="topic-resources-container">
    <div class="topic-header">
        <h1>{{ $topic->name }}</h1>
        @if($topic->description)
            <p class="topic-description">{{ $topic->description }}</p>
        @endif
        <div class="topic-meta">
            <span class="meta-item">
                <i class="fas fa-clock"></i> Estimated: {{ $topic->estimated_minutes ?? 'N/A' }} minutes
            </span>
            <span class="meta-item">
                <i class="fas fa-file"></i> {{ $resources->count() }} resources
            </span>
        </div>
    </div>

    <div class="resources-list">
        @forelse($resources as $resource)
            <!-- FIXED: Changed from 'student.resources.show' to 'student.resources.viewer' -->
            <a href="{{ route('student.resources.viewer', $resource->id) }}" class="resource-item">
                <div class="resource-icon {{ $resource->file_type }}">
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
                    <h3>{{ $resource->title }}</h3>
                    @if($resource->description)
                        <p>{{ Str::limit($resource->description, 80) }}</p>
                    @endif
                    <div class="resource-meta">
                        <span><i class="fas fa-download"></i> {{ $resource->download_count ?? 0 }} downloads</span>
                        <span><i class="fas fa-eye"></i> {{ $resource->views_count ?? 0 }} views</span>
                    </div>
                </div>
                <div class="resource-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No resources yet</h3>
                <p>No learning materials have been added for this topic yet.</p>
                <a href="{{ route('topics.index', $unit->id) }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Topics
                </a>
            </div>
        @endforelse
    </div>

    <div class="navigation-buttons">
        <a href="{{ route('topics.index', $unit->id) }}" class="btn-back-to-topics">
            <i class="fas fa-arrow-left"></i> Back to All Topics
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
.topic-resources-container {
    max-width: 900px;
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
}

.breadcrumb-item a:hover {
    color: #f59e0b;
}

.breadcrumb-item.active {
    color: #0f172a;
    font-weight: 500;
}

.topic-header {
    margin-bottom: 32px;
}

.topic-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
}

.topic-description {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 16px;
}

.topic-meta {
    display: flex;
    gap: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
}

.meta-item {
    color: #64748b;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.meta-item i {
    color: #f59e0b;
}

.resources-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 32px;
}

.resource-item {
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

.resource-item:hover {
    transform: translateX(5px);
    border-color: #f59e0b;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.04);
}

.resource-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.resource-icon.pdf {
    background: #fee2e2;
    color: #dc2626;
}

.resource-icon.video {
    background: #dbeafe;
    color: #2563eb;
}

.resource-icon.link {
    background: #fef3c7;
    color: #d97706;
}

.resource-icon.document {
    background: #e0f2fe;
    color: #0284c7;
}

.resource-info {
    flex: 1;
}

.resource-info h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 6px;
}

.resource-info p {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 8px;
    line-height: 1.4;
}

.resource-meta {
    display: flex;
    gap: 16px;
    font-size: 0.75rem;
    color: #94a3b8;
}

.resource-meta i {
    color: #f59e0b;
}

.resource-arrow {
    color: #cbd5e1;
    transition: all 0.2s ease;
}

.resource-item:hover .resource-arrow {
    color: #f59e0b;
    transform: translateX(5px);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 24px;
    border: 2px dashed #f1f5f9;
}

.empty-state i {
    font-size: 48px;
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

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: #f1f5f9;
    color: #475569;
    text-decoration: none;
    border-radius: 40px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #e2e8f0;
}

.navigation-buttons {
    text-align: center;
}

.btn-back-to-topics {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    background: #f1f5f9;
    color: #475569;
    text-decoration: none;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-back-to-topics:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .topic-resources-container {
        padding: 16px;
    }
    
    .resource-item {
        flex-direction: column;
        text-align: center;
    }
    
    .resource-arrow {
        display: none;
    }
}
</style>
@endpush