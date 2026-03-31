@extends(Auth::user()->hasRole('admin') ? 'admin.layouts.master' : (Auth::user()->hasRole('lecturer') ? 'lecturer.layouts.master' : 'student.layouts.master'))

@section('title', 'Resources')
@section('page-icon', 'fa-folder-open')
@section('page-title', 'Learning Resources')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/{{ Auth::user()->hasRole('admin') ? 'admin' : (Auth::user()->hasRole('lecturer') ? 'lecturer' : 'student') }}/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            @if($currentUnit)
                <li class="breadcrumb-item"><a href="{{ route('resources.index') }}">Resources</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $currentUnit }}</li>
            @else
                <li class="breadcrumb-item active" aria-current="page">Resources</li>
            @endif
        </ol>
    </nav>
@endsection

@push('styles')
<style>
/* ===== PROFESSIONAL CARD STYLING ===== */
.resources-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
}

/* Header */
.resources-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 20px;
}

.resources-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 12px;
    letter-spacing: -0.02em;
}

.resources-header h1 i {
    color: #f59e0b;
    background: #fffbeb;
    padding: 8px;
    border-radius: 14px;
    font-size: 1.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 12px 28px;
    border: none;
    border-radius: 40px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 28px rgba(245, 158, 11, 0.35);
}

/* Filters Section */
.filters-section {
    background: white;
    border-radius: 24px;
    padding: 20px 24px;
    margin-bottom: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.filters-form {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 180px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    color: #475569;
    font-weight: 500;
    font-size: 0.9rem;
}

.filter-group label i {
    color: #f59e0b;
    margin-right: 5px;
}

.filter-select, .filter-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    font-size: 0.95rem;
    background: white;
    transition: all 0.2s;
}

.filter-select:focus, .filter-input:focus {
    border-color: #f59e0b;
    outline: none;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
}

.btn-filter {
    padding: 12px 24px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    font-weight: 500;
}

.btn-filter:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

/* Resources Grid */
.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

/* Resource Cards */
.resource-card {
    background: white;
    border-radius: 24px;
    padding: 20px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    transition: all 0.2s ease;
    position: relative;
    display: flex;
    flex-direction: column;
}

.resource-card:hover {
    border-color: #e2e8f0;
    box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.resource-card.pinned {
    border-left: 4px solid #f59e0b;
    background: #fffbeb;
}

/* Resource Icon */
.resource-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 16px;
}

.resource-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.resource-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 8px;
    line-height: 1.4;
    flex: 1;
}

.resource-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s;
}

.resource-title a:hover {
    color: #f59e0b;
}

.resource-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 500;
    margin-left: 8px;
}

.badge-pinned {
    background: #fef3c7;
    color: #92400e;
}

.badge-youtube {
    background: #fee2e2;
    color: #b91c1c;
}

.badge-file {
    background: #dbeafe;
    color: #1e40af;
}

.badge-link {
    background: #e2e8f0;
    color: #475569;
}

.resource-description {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 16px;
    flex: 1;
}

/* Resource Meta */
.resource-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    font-size: 0.8rem;
    color: #64748b;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.meta-item i {
    color: #f59e0b;
}

.unit-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 500;
}

.topic-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Resource Footer */
.resource-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    margin-top: auto;
}

.resource-stats {
    display: flex;
    gap: 12px;
    color: #64748b;
    font-size: 0.8rem;
}

.resource-stats i {
    color: #f59e0b;
    margin-right: 3px;
}

.btn-download, .btn-view {
    padding: 8px 20px;
    border: none;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.btn-download {
    background: #f59e0b;
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.btn-download:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
}

.btn-view {
    background: #f1f5f9;
    color: #475569;
}

.btn-view:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

/* Uploader Info */
.uploader-info {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
}

.uploader-avatar {
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 10px;
    overflow: hidden;
}

.uploader-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.uploader-name {
    font-size: 0.8rem;
    color: #64748b;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 64px 24px;
    background: white;
    border-radius: 24px;
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
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 24px;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 32px;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .resources-container {
        padding: 16px;
    }
    
    .filters-form {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
    }
    
    .resource-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .btn-download, .btn-view {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="resources-container">
    <div class="resources-header">
        <h1><i class="fas fa-folder-open"></i> Learning Resources</h1>
        <a href="{{ route('resources.create', ['unit' => $currentUnit]) }}" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Upload Resource
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success" style="background: #f0fdf4; border: 1px solid #dcfce7; color: #166534; padding: 16px 20px; border-radius: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('resources.index') }}" class="filters-form">
            @if($units->count() > 0 && !$currentUnit)
            <div class="filter-group">
                <label><i class="fas fa-layer-group"></i> Unit</label>
                <select name="unit" class="filter-select">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ ($filters['unit'] ?? '') == $unit->code ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($topics->count() > 0)
            <div class="filter-group">
                <label><i class="fas fa-list-ul"></i> Topic</label>
                <select name="topic" class="filter-select">
                    <option value="">All Topics</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ ($filters['topic'] ?? '') == $topic->id ? 'selected' : '' }}>
                            {{ $topic->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="filter-group">
                <label><i class="fas fa-tag"></i> Type</label>
                <select name="type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="file" {{ ($filters['type'] ?? '') == 'file' ? 'selected' : '' }}>Files</option>
                    <option value="link" {{ ($filters['type'] ?? '') == 'link' ? 'selected' : '' }}>Links</option>
                    <option value="youtube" {{ ($filters['type'] ?? '') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-sort"></i> Sort By</label>
                <select name="sort" class="filter-select">
                    <option value="latest" {{ ($filters['sort'] ?? 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="popular" {{ ($filters['sort'] ?? '') == 'popular' ? 'selected' : '' }}>Most Downloaded</option>
                    <option value="views" {{ ($filters['sort'] ?? '') == 'views' ? 'selected' : '' }}>Most Viewed</option>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-search"></i> Search</label>
                <input type="text" name="search" class="filter-input" placeholder="Search resources..." value="{{ $filters['search'] ?? '' }}">
            </div>

            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </form>
    </div>

    <!-- Resources Grid -->
    <div class="resources-grid">
        @forelse($resources as $resource)
            <div class="resource-card @if($resource->is_pinned) pinned @endif">
                <div class="resource-header">
                    <span class="resource-badge 
                        @if($resource->type === 'youtube') badge-youtube
                        @elseif($resource->type === 'file') badge-file
                        @else badge-link @endif">
                        <i class="fas {{ $resource->file_icon }}"></i>
                        {{ ucfirst($resource->type) }}
                    </span>
                    @if($resource->is_pinned)
                        <span class="resource-badge badge-pinned">
                            <i class="fas fa-thumbtack"></i> Pinned
                        </span>
                    @endif
                </div>

                <h3 class="resource-title">
                    <a href="{{ route('resources.show', $resource) }}">{{ $resource->title }}</a>
                </h3>

                @if($resource->description)
                    <p class="resource-description">{{ Str::limit($resource->description, 100) }}</p>
                @endif

                <div class="resource-meta">
                    <span class="unit-badge">{{ $resource->unit_code }}</span>
                    @if($resource->topic)
                        <span class="topic-badge">{{ $resource->topic->title }}</span>
                    @endif
                </div>

                <div class="resource-meta">
                    <span class="meta-item">
                        <i class="fas fa-user"></i> {{ $resource->user->name }}
                    </span>
                    <span class="meta-item">
                        <i class="far fa-clock"></i> {{ $resource->created_at->diffForHumans() }}
                    </span>
                </div>

                <div class="resource-footer">
                    <div class="resource-stats">
                        <span><i class="fas fa-download"></i> {{ number_format($resource->download_count) }}</span>
                        <span><i class="fas fa-eye"></i> {{ number_format($resource->view_count) }}</span>
                    </div>

                    @if($resource->is_file)
                        <a href="{{ route('resources.download', $resource) }}" class="btn-download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    @else
                        <a href="{{ route('resources.show', $resource) }}" class="btn-view">
                            <i class="fas fa-external-link-alt"></i> View
                        </a>
                    @endif
                </div>

                <div class="uploader-info">
                    <div class="uploader-avatar">
                        @if($resource->user->avatar)
                            <img src="{{ Storage::url($resource->user->avatar) }}" alt="{{ $resource->user->name }}">
                        @else
                            {{ substr($resource->user->name, 0, 1) }}
                        @endif
                    </div>
                    <span class="uploader-name">Uploaded by {{ $resource->user->name }}</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Resources Found</h3>
                <p>Be the first to upload a resource for this unit!</p>
                <a href="{{ route('resources.create', ['unit' => $currentUnit]) }}" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Upload Resource
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