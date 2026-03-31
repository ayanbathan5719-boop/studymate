@extends('student.layouts.master')

@section('title', 'Resources - ' . $topic->name)
@section('page-icon', 'fa-tag')
@section('page-title', $topic->name)

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.resources.index') }}"><i class="fas fa-folder-open"></i> Resources</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.resources.by-unit', $topic->unit->code) }}">{{ $topic->unit->code }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $topic->name }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="topic-resources-container">
    {{-- Topic Header --}}
    <div class="topic-header-card">
        <div class="topic-header-content">
            <div class="topic-meta">
                <span class="unit-badge">{{ $topic->unit->code }}</span>
                <span class="topic-badge">Topic</span>
            </div>
            <h1>{{ $topic->name }}</h1>
            @if($topic->description)
                <p class="topic-description">{{ $topic->description }}</p>
            @endif
        </div>
        
        <div class="topic-progress-card">
            <h4>Your Progress</h4>
            @php
                $totalResources = $resources->total();
                $studiedResources = App\Models\StudyProgress::where('student_id', Auth::id())
                    ->whereIn('resource_id', $resources->pluck('id'))
                    ->where('completed', true)
                    ->count();
                $progressPercent = $totalResources > 0 ? round(($studiedResources / $totalResources) * 100) : 0;
            @endphp
            
            <div class="progress-circle" data-progress="{{ $progressPercent }}">
                <span class="progress-value">{{ $progressPercent }}%</span>
            </div>
            
            <div class="progress-stats">
                <span><i class="fas fa-check-circle"></i> {{ $studiedResources }} Completed</span>
                <span><i class="fas fa-clock"></i> {{ $totalResources - $studiedResources }} Remaining</span>
            </div>
            
            <a href="{{ route('student.topics.show', [$topic->unit_id, $topic->id]) }}" class="btn-view-topic">
                <i class="fas fa-chart-line"></i> View Topic Details
            </a>
        </div>
    </div>

    {{-- Resources Grid --}}
    <div class="resources-grid">
        @forelse($resources as $resource)
            <div class="resource-card {{ App\Models\StudyProgress::where('student_id', Auth::id())->where('resource_id', $resource->id)->where('completed', true)->exists() ? 'studied' : '' }}">
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
                        
                        <span class="meta-item">
                            <i class="fas fa-calendar-alt"></i> 
                            {{ $resource->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    
                    @php
                        $isStudied = App\Models\StudyProgress::where('student_id', Auth::id())
                            ->where('resource_id', $resource->id)
                            ->where('completed', true)
                            ->exists();
                    @endphp
                    
                    @if($isStudied)
                        <div class="studied-badge">
                            <i class="fas fa-check-circle"></i> Studied
                        </div>
                    @endif
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
                    
                    @if(!$isStudied)
                        <form action="{{ route('student.resources.mark-studied', $resource->id) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Mark this resource as studied?')">
                            @csrf
                            <button type="submit" class="btn-studied-small" title="Mark as Studied">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <h3>No Resources Found</h3>
                <p>There are no resources available for this topic yet.</p>
                <a href="{{ route('student.resources.by-unit', $topic->unit->code) }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Unit Resources
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
</div>
@endsection

@push('styles')
<style>
.topic-resources-container {
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

.topic-header-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 32px;
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 24px;
}

.topic-header-content {
    flex: 2;
    min-width: 300px;
}

.topic-meta {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
}

.unit-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 6px 16px;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 600;
}

.topic-badge {
    background: #f59e0b;
    color: white;
    padding: 6px 16px;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 600;
}

.topic-header-content h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
}

.topic-description {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.6;
    margin: 0;
}

.topic-progress-card {
    flex: 1;
    min-width: 250px;
    background: #f8fafc;
    border-radius: 20px;
    padding: 24px;
    text-align: center;
}

.topic-progress-card h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
}

.progress-circle {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    position: relative;
    background: conic-gradient(#f59e0b 0deg, #f1f5f9 0deg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-circle::before {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
}

.progress-value {
    position: relative;
    font-size: 1.5rem;
    font-weight: 700;
    color: #f59e0b;
    z-index: 1;
}

.progress-stats {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-bottom: 20px;
    font-size: 0.85rem;
    color: #64748b;
}

.progress-stats i {
    color: #f59e0b;
    margin-right: 4px;
}

.btn-view-topic {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #f59e0b;
    color: white;
    text-decoration: none;
    border-radius: 40px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.btn-view-topic:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
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
    position: relative;
}

.resource-card.studied {
    background: #f0fdf4;
    border-color: #86efac;
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
    flex-wrap: wrap;
    gap: 12px;
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 8px;
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

.studied-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #10b981;
    color: white;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 500;
}

.resource-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-view,
.btn-download,
.btn-external,
.btn-studied-small {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
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

.btn-studied-small {
    background: #8b5cf6;
    color: white;
}

.btn-studied-small:hover {
    background: #7c3aed;
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

@media (max-width: 768px) {
    .topic-resources-container {
        padding: 16px;
    }
    
    .topic-header-card {
        flex-direction: column;
        text-align: center;
    }
    
    .topic-meta {
        justify-content: center;
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
}
</style>
@endpush

@push('scripts')
<script>
// Animate progress circle
document.addEventListener('DOMContentLoaded', function() {
    const circle = document.querySelector('.progress-circle');
    if (circle) {
        const progress = circle.dataset.progress;
        const degrees = (progress / 100) * 360;
        circle.style.background = `conic-gradient(#f59e0b ${degrees}deg, #f1f5f9 ${degrees}deg)`;
    }
});
</script>
@endpush