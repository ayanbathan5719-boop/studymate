@extends('lecturer.layouts.master')

@section('title', $unit->code . ' - Unit Resources')
@section('page-icon', 'fa-folder-open')
@section('page-title', $unit->code . ' Resources')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.units.index') }}">My Units</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $unit->code }} Resources</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .unit-resources-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    .unit-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 24px;
        padding: 32px;
        margin-bottom: 32px;
        color: white;
    }

    .unit-header h1 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .unit-header .unit-code {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 16px;
    }

    .unit-header p {
        margin-bottom: 0;
        opacity: 0.9;
    }

    .unit-stats {
        display: flex;
        gap: 24px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .stat-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 40px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
    }

    .resource-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        transition: all 0.3s ease;
        display: flex;
        gap: 16px;
    }

    .resource-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        border-color: #f59e0b;
    }

    .resource-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }

    .resource-icon.pdf {
        background: #fee2e2;
        color: #ef4444;
    }

    .resource-icon.video {
        background: #dbeafe;
        color: #3b82f6;
    }

    .resource-icon.document {
        background: #d1fae5;
        color: #10b981;
    }

    .resource-icon.link {
        background: #fef3c7;
        color: #f59e0b;
    }

    .resource-info {
        flex: 1;
    }

    .resource-info h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .resource-info p {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .resource-meta {
        display: flex;
        gap: 16px;
        font-size: 0.7rem;
        color: #94a3b8;
        flex-wrap: wrap;
    }

    .resource-meta i {
        margin-right: 4px;
    }

    .resource-badge {
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.65rem;
        color: #475569;
    }

    .resource-badge.forum {
        background: #fef3c7;
        color: #f59e0b;
    }

    .resource-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-view, .btn-download {
        padding: 8px 16px;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-view:hover, .btn-download:hover {
        background: #f59e0b;
        color: white;
        transform: translateY(-2px);
    }

    .btn-view i, .btn-download i {
        font-size: 0.75rem;
    }

    .empty-state {
        text-align: center;
        padding: 64px 24px;
        background: white;
        border-radius: 24px;
        border: 2px dashed #e2e8f0;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #334155;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 24px;
    }

    .empty-state .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 40px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
    }

    .empty-state .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    .pagination-wrapper {
        margin-top: 32px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .unit-resources-container {
            padding: 16px;
        }

        .unit-header {
            padding: 24px;
        }

        .resources-grid {
            grid-template-columns: 1fr;
        }

        .resource-card {
            flex-direction: column;
            text-align: center;
        }

        .resource-icon {
            margin: 0 auto;
        }

        .resource-meta {
            justify-content: center;
        }

        .resource-actions {
            justify-content: center;
            margin-top: 12px;
        }

        .unit-stats {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="unit-resources-container">
    <div class="unit-header">
        <h1><i class="fas fa-folder-open"></i> {{ $unit->name }}</h1>
        <div class="unit-code">{{ $unit->code }}</div>
        <p>All resources shared in this unit, including files and links from forum posts.</p>
        <div class="unit-stats">
            <span class="stat-badge"><i class="fas fa-file-alt"></i> {{ $resources->total() }} Resources</span>
            <span class="stat-badge"><i class="fas fa-users"></i> {{ $unit->students()->count() }} Students</span>
            <span class="stat-badge"><i class="fas fa-comments"></i> {{ $unit->forumPosts()->count() }} Forum Posts</span>
        </div>
    </div>

    <div class="resources-grid">
        @forelse($resources as $resource)
            <div class="resource-card">
                <div class="resource-icon {{ $resource->file_type ?? $resource->type ?? 'document' }}">
                    @if(($resource->file_type ?? $resource->type) === 'pdf')
                        <i class="fas fa-file-pdf"></i>
                    @elseif(($resource->file_type ?? $resource->type) === 'video')
                        <i class="fas fa-video"></i>
                    @elseif(($resource->file_type ?? $resource->type) === 'link')
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
                        <span><i class="fas fa-download"></i> {{ number_format($resource->download_count ?? 0) }} downloads</span>
                        <span><i class="fas fa-eye"></i> {{ number_format($resource->views_count ?? 0) }} views</span>
                        <span><i class="fas fa-calendar"></i> {{ $resource->created_at->format('M d, Y') }}</span>
                        @if($resource->source === 'forum')
                            <span class="resource-badge forum"><i class="fas fa-comments"></i> From Forum</span>
                        @elseif($resource->is_official)
                            <span class="resource-badge"><i class="fas fa-check-circle"></i> Official</span>
                        @endif
                    </div>
                    @if($resource->user)
                        <div class="resource-meta" style="margin-top: 8px;">
                            <span><i class="fas fa-user"></i> Uploaded by: {{ $resource->user->name ?? 'Unknown' }}</span>
                        </div>
                    @endif
                </div>
                <div class="resource-actions">
                    @if(($resource->file_type ?? $resource->type) === 'link')
                        <a href="{{ $resource->url }}" target="_blank" class="btn-view">
                            <i class="fas fa-external-link-alt"></i> Visit Link
                        </a>
                    @else
                        <a href="{{ route('lecturer.resources.download', $resource->id) }}" class="btn-download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    @endif
                    <a href="{{ route('student.resources.show', $resource->id) }}" class="btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Resources Yet</h3>
                <p>Resources shared in this unit's forum will appear here.</p>
                <a href="{{ route('lecturer.forum.index', ['unit' => $unit->code]) }}" class="btn-primary">
                    <i class="fas fa-comments"></i> Go to Unit Forum
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($resources->hasPages())
        <div class="pagination-wrapper">
            {{ $resources->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Track download when resource is downloaded
    document.querySelectorAll('.btn-download').forEach(button => {
        button.addEventListener('click', function(e) {
            const resourceId = this.getAttribute('data-resource-id');
            if (resourceId) {
                fetch(`/lecturer/resources/${resourceId}/track-download`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                }).catch(err => console.error('Error tracking download:', err));
            }
        });
    });
</script>
@endpush