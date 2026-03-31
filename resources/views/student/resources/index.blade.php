@extends('student.layouts.master')

@section('title', 'Learning Resources')
@section('page-icon', 'fa-graduation-cap')
@section('page-title', 'Learning Resources')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Resources</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    /* Clean Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        padding: 32px;
        margin-bottom: 32px;
        color: white;
    }
    .hero-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .hero-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 20px;
    }
    .stats-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .stat-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 40px;
        padding: 8px 20px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .stat-number {
        font-size: 1.3rem;
        font-weight: 700;
    }

    /* Filter Bar */
    .filter-bar {
        background: white;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 32px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .filter-grid {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .filter-group {
        flex: 1;
        min-width: 180px;
    }
    .filter-group label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 5px;
        display: block;
    }
    .filter-group input, .filter-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.85rem;
    }
    .btn-filter {
        background: #667eea;
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    .btn-filter:hover { background: #5a67d8; }
    .btn-clear {
        background: #e2e8f0;
        color: #475569;
        padding: 10px 20px;
        border-radius: 12px;
        text-decoration: none;
        display: inline-block;
    }

    /* Category Tabs - Clear Type Distinction */
    .category-tabs {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 32px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 8px;
    }
    .category-tab {
        padding: 8px 24px;
        border-radius: 40px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .category-tab i {
        margin-right: 6px;
    }
    .category-tab.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    .category-tab:hover:not(.active) {
        background: #eef2ff;
        transform: translateY(-2px);
    }
    /* Type-specific tab colors on hover */
    .category-tab[data-type="file"]:hover { border-color: #3b82f6; }
    .category-tab[data-type="link"]:hover { border-color: #10b981; }
    .category-tab[data-type="video"]:hover { border-color: #ef4444; }
    .category-tab[data-type="pdf"]:hover { border-color: #f59e0b; }

    /* Resources Grid */
    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .resource-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .resource-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    /* Type-specific card borders on hover */
    .resource-card.file:hover { border-color: #3b82f6; }
    .resource-card.link:hover { border-color: #10b981; }
    .resource-card.video:hover { border-color: #ef4444; }
    .resource-card.pdf:hover { border-color: #f59e0b; }

    /* Type Badges - Clear Visual Distinction */
    .type-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .type-badge.file { background: #dbeafe; color: #1e40af; border-left: 3px solid #3b82f6; }
    .type-badge.link { background: #dcfce7; color: #166534; border-left: 3px solid #10b981; }
    .type-badge.video { background: #fee2e2; color: #991b1b; border-left: 3px solid #ef4444; }
    .type-badge.pdf { background: #fef3c7; color: #92400e; border-left: 3px solid #f59e0b; }

    .resource-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .resource-icon.file { background: #dbeafe; color: #3b82f6; }
    .resource-icon.link { background: #dcfce7; color: #10b981; }
    .resource-icon.video { background: #fee2e2; color: #ef4444; }
    .resource-icon.pdf { background: #fef3c7; color: #f59e0b; }

    .resource-content {
        flex: 1;
        padding: 20px;
    }
    .resource-title {
        font-weight: 700;
        font-size: 1rem;
        color: #1e293b;
        margin-bottom: 6px;
        padding-right: 80px;
    }
    .resource-title a {
        color: inherit;
        text-decoration: none;
    }
    .resource-title a:hover {
        color: #667eea;
    }
    .resource-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 0.7rem;
        color: #64748b;
        margin: 8px 0;
    }
    .resource-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .unit-badge {
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.65rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .resource-actions {
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
    }
    .btn-view, .btn-download, .btn-external {
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-view {
        background: #667eea;
        color: white;
    }
    .btn-download {
        background: #10b981;
        color: white;
    }
    .btn-external {
        background: #3b82f6;
        color: white;
    }
    .btn-study {
        background: #f1f5f9;
        color: #475569;
        border: none;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.75rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-study.active {
        background: #10b981;
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px;
        background: #f8fafc;
        border-radius: 24px;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 16px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-title">
            <i class="fas fa-graduation-cap me-2"></i> Learning Resources
        </div>
        <div class="hero-subtitle">Access study materials from your enrolled units</div>
        <div class="stats-row">
            <div class="stat-badge"><span class="stat-number">{{ $resources->total() ?? 0 }}</span> Total Resources</div>
            <div class="stat-badge"><span class="stat-number">{{ $resources->count() ?? 0 }}</span> This Week</div>
            <div class="stat-badge"><span class="stat-number">{{ auth()->user()->enrolledUnits()->count() ?? 0 }}</span> Your Units</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('student.resources.index') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="search" placeholder="Search resources..." value="{{ request('search') }}">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-layer-group"></i> Unit</label>
                    <select name="unit">
                        <option value="">All Units</option>
                        @foreach(auth()->user()->enrolledUnits ?? [] as $unit)
                            <option value="{{ $unit->code }}" {{ request('unit') == $unit->code ? 'selected' : '' }}>
                                {{ $unit->code }} - {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Apply</button>
                    @if(request()->anyFilled(['search', 'unit', 'type']))
                        <a href="{{ route('student.resources.index') }}" class="btn-clear ms-2">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Category Tabs - Clear Type Distinction -->
    <div class="category-tabs">
        <a href="{{ route('student.resources.index') }}" class="category-tab {{ !request('type') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> All Resources
        </a>
        <a href="{{ route('student.resources.index', ['type' => 'file']) }}" data-type="file" class="category-tab {{ request('type') == 'file' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Files
        </a>
        <a href="{{ route('student.resources.index', ['type' => 'link']) }}" data-type="link" class="category-tab {{ request('type') == 'link' ? 'active' : '' }}">
            <i class="fas fa-link"></i> Links
        </a>
        <a href="{{ route('student.resources.index', ['type' => 'video']) }}" data-type="video" class="category-tab {{ request('type') == 'video' ? 'active' : '' }}">
            <i class="fas fa-video"></i> Videos
        </a>
        <a href="{{ route('student.resources.index', ['type' => 'pdf']) }}" data-type="pdf" class="category-tab {{ request('type') == 'pdf' ? 'active' : '' }}">
            <i class="fas fa-file-pdf"></i> PDF Documents
        </a>
    </div>

    <!-- Resources Grid -->
    @if($resources->count() > 0)
        <div class="resources-grid">
            @foreach($resources as $resource)
                @php
                    $type = $resource->file_type ?? 'file';
                    $badgeClass = $type;
                    $iconClass = $type;
                    $badgeIcon = match($type) {
                        'pdf' => 'fa-file-pdf',
                        'video' => 'fa-video',
                        'link' => 'fa-link',
                        default => 'fa-file-alt'
                    };
                    $typeLabel = match($type) {
                        'pdf' => 'PDF',
                        'video' => 'VIDEO',
                        'link' => 'LINK',
                        default => 'FILE'
                    };
                    $cardClass = $type;
                @endphp
                <div class="resource-card {{ $cardClass }}">
                    <div class="d-flex" style="position: relative;">
                        <div class="resource-icon {{ $iconClass }}" style="margin: 20px 0 0 20px;">
                            <i class="fas {{ $badgeIcon }}"></i>
                        </div>
                        <div class="resource-content">
                            <div class="type-badge {{ $badgeClass }}">
                                <i class="fas {{ $badgeIcon }}"></i> {{ $typeLabel }}
                            </div>
                            <div class="resource-title">
                                <a href="{{ route('student.resources.viewer', $resource) }}" target="_blank">
                                    {{ \Illuminate\Support\Str::limit($resource->title, 60) }}
                                </a>
                            </div>
                            <div class="resource-meta">
                                <span><i class="fas fa-user"></i> {{ $resource->user->name ?? 'Unknown' }}</span>
                                <span><i class="far fa-clock"></i> {{ $resource->created_at->diffForHumans() }}</span>
                                <span><i class="fas fa-download"></i> {{ number_format($resource->download_count ?? 0) }}</span>
                            </div>
                            <div>
                                <span class="unit-badge"><i class="fas fa-layer-group"></i> {{ $resource->unit_code }}</span>
                                @if($resource->description)
                                    <span class="unit-badge ms-2"><i class="fas fa-tag"></i> {{ \Illuminate\Support\Str::limit($resource->description, 50) }}</span>
                                @endif
                            </div>
                            <div class="resource-actions">
                                @if($type == 'link')
                                    <a href="{{ $resource->url }}" target="_blank" class="btn-external">
                                        <i class="fas fa-external-link-alt"></i> Open Link
                                    </a>
                                @else
                                    <a href="{{ route('student.resources.viewer', $resource) }}" target="_blank" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('student.resources.download', $resource) }}" class="btn-download">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @endif
                                <button class="btn-study" data-resource-id="{{ $resource->id }}">
                                    <i class="fas fa-bookmark"></i> Mark Studied
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(method_exists($resources, 'links'))
            <div class="d-flex justify-content-center">
                {{ $resources->withQueryString()->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h4>No Resources Found</h4>
            <p>No learning resources match your filters. Try adjusting your search criteria.</p>
            @if(request()->anyFilled(['search', 'unit', 'type']))
                <a href="{{ route('student.resources.index') }}" class="btn-filter mt-3">Clear Filters</a>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-study').forEach(button => {
    button.addEventListener('click', function() {
        const resourceId = this.getAttribute('data-resource-id');
        const btn = this;
        
        fetch(`/student/resources/${resourceId}/mark-studied`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ duration: 15 })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Studied';
                
                const toast = document.createElement('div');
                toast.innerHTML = '✓ Marked as studied!';
                toast.style.cssText = 'position:fixed; bottom:20px; right:20px; background:#10b981; color:white; padding:10px 20px; border-radius:30px; z-index:9999;';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            } else {
                alert(data.message || 'Error marking as studied');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>
@endpush
@endsection