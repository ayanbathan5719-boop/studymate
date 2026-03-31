@extends('student.layouts.master')

@section('title', 'Resources - ' . $unit->code)
@section('page-icon', 'fa-folder-open')
@section('page-title', $unit->code . ' Resources')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.resources.index') }}"><i class="fas fa-folder-open"></i> Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $unit->code }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="unit-resources-container">
    {{-- Unit Header --}}
    <div class="unit-header-card">
        <div class="unit-header-content">
            <span class="unit-code-badge">{{ $unit->code }}</span>
            <h1>{{ $unit->name }}</h1>
            <p class="unit-description">{{ $unit->description ?? 'No description available.' }}</p>
        </div>
        
        <div class="unit-stats">
            <div class="stat">
                <span class="stat-value">{{ $resources->total() }}</span>
                <span class="stat-label">Resources</span>
            </div>
            <div class="stat">
                <span class="stat-value">{{ $unit->topics->count() }}</span>
                <span class="stat-label">Topics</span>
            </div>
            <a href="{{ route('student.topics.index', $unit->id) }}" class="btn-view-topics">
                <i class="fas fa-chart-line"></i> View Topics
            </a>
        </div>
    </div>

    {{-- Resources Grid --}}
    <div class="resources-grid">
        @forelse($resources as $resource)
            <div class="resource-card">
                <div class="resource-icon {{ $resource->type }}">
                    @if($resource->type === 'pdf')
                        <i class="fas fa-file-pdf"></i>
                    @elseif($resource->type === 'video')
                        <i class="fas fa-video"></i>
                    @elseif($resource->type === 'link')
                        <i class="fas fa-link"></i>
                    @else
                        <i class="fas fa-file-alt"></i>
                    @endif
                </div>

                <div class="resource-content">
                    <div class="resource-header">
                        @if($resource->topic)
                            <span class="topic-badge">{{ $resource->topic->name }}</span>
                        @endif
                        <span class="resource-date">
                            <i class="fas fa-calendar-alt"></i> 
                            {{ $resource->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    
                    <h3 class="resource-title">
                        <a href="{{ route('student.resources.show', $resource->id) }}">
                            {{ $resource->title }}
                        </a>
                    </h3>
                    
                    <div class="resource-meta">
                        <span class="meta-item">
                            <i class="fas fa-user"></i> 
                            {{ $resource->user->name ?? 'Unknown' }}
                        </span>
                        
                        <span class="meta-item">
                            <i class="fas fa-download"></i> 
                            {{ $resource->downloads_count ?? 0 }} downloads
                        </span>
                    </div>
                </div>

                <div class="resource-actions">
                    <a href="{{ route('student.resources.show', $resource->id) }}" 
                       class="btn-view" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    
                    @if($resource->type !== 'link' && $resource->file_path)
                        <a href="{{ route('student.resources.download', $resource->id) }}" 
                           class="btn-download" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                    @elseif($resource->type === 'link' && $resource->url)
                        <a href="{{ $resource->url }}" 
                           target="_blank" 
                           class="btn-external" title="Open Link">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3>No Resources Found</h3>
                <p>There are no resources available for this unit yet.</p>
                <a href="{{ route('student.resources.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to All Resources
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($resources->hasPages())
        <div class="pagination-container">
            {{ $resources->links() }}
        </div>
    @endif

    {{-- Topics Quick Access --}}
    @if($unit->topics->count() > 0)
        <div class="topics-section">
            <h2><i class="fas fa-chart-line"></i> Browse by Topic</h2>
            <div class="topics-grid">
                @foreach($unit->topics as $topic)
                    <a href="{{ route('student.resources.by-topic', $topic->id) }}" class="topic-card">
                        <div class="topic-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="topic-info">
                            <h4>{{ $topic->name }}</h4>
                            <span class="resource-count">
                                {{ $topic->resources_count ?? $topic->resources->count() }} resources
                            </span>
                        </div>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.unit-resources-container {
    max-width: 1400px;
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
    min-width: 300px;
}

.unit-code-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 6px 16px;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 16px;
    backdrop-filter: blur(5px);
}

.unit-header-content h1 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.unit-description {
    font-size: 1rem;
    opacity: 0.9;
    line-height: 1.6;
    margin: 0;
}

.unit-stats {
    flex: 1;
    min-width: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 20px;
    backdrop-filter: blur(5px);
    display: flex;
    flex-direction: column;
    gap: 16px;
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

.btn-view-topics {
    background: white;
    color: #f59e0b;
    text-decoration: none;
    padding: 12px;
    border-radius: 40px;
    text-align: center;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-view-topics:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    color: #d97706;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.resource-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 20px;
    display: flex;
    gap: 16px;
    transition: all 0.2s ease;
}

.resource-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
    border-color: #f59e0b;
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

.resource-content {
    flex: 1;
    min-width: 0;
}

.resource-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}

.topic-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 600;
}

.resource-date {
    color: #94a3b8;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.resource-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.resource-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s ease;
}

.resource-title a:hover {
    color: #f59e0b;
}

.resource-meta {
    display: flex;
    gap: 12px;
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
    font-size: 0.7rem;
}

.resource-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-view,
.btn-download,
.btn-external {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-view {
    background: #f59e0b;
    color: white;
}

.btn-view:hover {
    background: #d97706;
    transform: scale(1.05);
}

.btn-download {
    background: #10b981;
    color: white;
}

.btn-download:hover {
    background: #059669;
    transform: scale(1.05);
}

.btn-external {
    background: #3b82f6;
    color: white;
}

.btn-external:hover {
    background: #2563eb;
    transform: scale(1.05);
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
    color: #334155;
}

.pagination-container {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}

.topics-section {
    margin-top: 48px;
}

.topics-section h2 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.topics-section h2 i {
    color: #f59e0b;
}

.topics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

.topic-card {
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

.topic-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
    border-color: #f59e0b;
}

.topic-icon {
    width: 40px;
    height: 40px;
    background: #fffbeb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f59e0b;
}

.topic-info {
    flex: 1;
}

.topic-info h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
}

.resource-count {
    font-size: 0.8rem;
    color: #64748b;
}

.topic-card i:last-child {
    color: #f59e0b;
    opacity: 0;
    transition: all 0.2s ease;
}

.topic-card:hover i:last-child {
    opacity: 1;
    transform: translateX(5px);
}

@media (max-width: 768px) {
    .unit-resources-container {
        padding: 16px;
    }
    
    .unit-header-card {
        flex-direction: column;
        text-align: center;
    }
    
    .unit-stats {
        width: 100%;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
    }
    
    .resource-card {
        flex-direction: column;
    }
    
    .resource-actions {
        flex-direction: row;
        justify-content: flex-end;
    }
    
    .topics-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush