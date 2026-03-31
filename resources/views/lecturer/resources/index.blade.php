@extends('lecturer.layouts.master')

@section('title', 'My Resources')
@section('page-icon', 'fa-folder-open')
@section('page-title', 'My Resources')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Resources</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="resources-container">
    {{-- Header Section --}}
    <div class="resources-header">
        <div class="header-content">
            <h1><i class="fas fa-folder-open"></i> My Resources</h1>
            <p class="subtitle">Manage learning materials for your units</p>
        </div>
        
        <a href="{{ route('lecturer.resources.create') }}" class="btn-create">
            <i class="fas fa-plus"></i> Upload New Resource
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total'] ?? 0 }}</h3>
                <p>Total Resources</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['pdf'] ?? 0 }}</h3>
                <p>PDF Documents</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-video"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['video'] ?? 0 }}</h3>
                <p>Videos</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <i class="fas fa-link"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['link'] ?? 0 }}</h3>
                <p>External Links</p>
            </div>
        </div>
    </div>

    {{-- Search and Filter --}}
    <div class="filters-section">
        <form method="GET" action="{{ route('lecturer.resources.index') }}" class="filter-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" 
                       name="search" 
                       placeholder="Search resources..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="filter-group">
                <select name="unit" class="filter-select">
                    <option value="">All Units</option>
                    @foreach($assignedUnits as $unit)
                        <option value="{{ $unit->code }}" {{ request('unit') == $unit->code ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                
                <select name="type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF Documents</option>
                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
                    <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>External Links</option>
                    <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                </select>
                
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filter
                </button>
                
                @if(request()->anyFilled(['search', 'unit', 'type']))
                    <a href="{{ route('lecturer.resources.index') }}" class="btn-clear">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Resources Table --}}
    <div class="resources-table-card">
        @if($resources->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3>No Resources Found</h3>
                <p>
                    @if(request()->anyFilled(['search', 'unit', 'type']))
                        No resources match your filters. Try adjusting your search criteria.
                    @else
                        You haven't uploaded any resources yet. Click the button below to upload your first resource.
                    @endif
                </p>
                <a href="{{ route('lecturer.resources.create') }}" class="btn-create-empty">
                    <i class="fas fa-plus"></i> Upload Resource
                </a>
            </div>
        @else
            <table class="resources-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Unit</th>
                        <th>Topic</th>
                        <th>Uploaded</th>
                        <th>Downloads</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resources as $resource)
                        <tr>
                            <td>
                                <span class="type-badge {{ $resource->file_type }}">
                                    @if($resource->file_type === 'pdf')
                                        <i class="fas fa-file-pdf"></i>
                                    @elseif($resource->file_type === 'video')
                                        <i class="fas fa-video"></i>
                                    @elseif($resource->file_type === 'link')
                                        <i class="fas fa-link"></i>
                                    @elseif($resource->file_type === 'document')
                                        <i class="fas fa-file-word"></i>
                                    @else
                                        <i class="fas fa-file-alt"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('lecturer.resources.show', $resource->id) }}" class="resource-title-link">
                                    {{ $resource->title }}
                                </a>
                                @if($resource->description)
                                    <small class="resource-description-preview">
                                        {{ Str::limit($resource->description, 50) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="unit-code">{{ $resource->unit_code }}</span>
                            </td>
                            <td class="topic-cell">
                                @if($resource->topic)
                                    {{ $resource->topic->name }}
                                @else
                                    General
                                @endif
                            </td>
                            <td>
                                <span class="upload-date" title="{{ $resource->created_at->format('F j, Y g:i A') }}">
                                    {{ $resource->created_at->diffForHumans() }}
                                </span>
                            </td>
                            <td>
                                <span class="download-count">
                                    <i class="fas fa-download"></i> {{ $resource->downloads_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('lecturer.resources.show', $resource->id) }}" 
                                       class="btn-action btn-view" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($resource->file_type !== 'link' && $resource->file_path)
                                        <a href="{{ route('lecturer.resources.download', $resource->id) }}" 
                                           class="btn-action btn-download" 
                                           title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('lecturer.resources.edit', $resource->id) }}" 
                                       class="btn-action btn-edit" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('lecturer.resources.destroy', $resource->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this resource? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="pagination-container">
                {{ $resources->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Quick Tips --}}
    <div class="tips-section">
        <div class="tip-card">
            <i class="fas fa-lightbulb"></i>
            <div class="tip-content">
                <h4>Quick Tips</h4>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Add descriptive titles and descriptions to help students find resources</li>
                    <li><i class="fas fa-check-circle"></i> Assign resources to specific topics for better organization</li>
                    <li><i class="fas fa-check-circle"></i> PDF files are automatically previewable in the browser</li>
                    <li><i class="fas fa-check-circle"></i> YouTube and Vimeo links will be embedded automatically</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.resources-container {
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

.resources-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 20px;
}

.header-content h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.header-content h1 i {
    color: #f59e0b;
    background: #fffbeb;
    padding: 8px;
    border-radius: 14px;
    font-size: 1.5rem;
}

.header-content .subtitle {
    color: #64748b;
    font-size: 1rem;
    margin-left: 52px;
}

.btn-create {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 12px 28px;
    border-radius: 40px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 24px;
    color: white;
}

.stat-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.2;
}

.stat-content p {
    color: #64748b;
    font-size: 0.85rem;
    margin: 0;
}

.filters-section {
    background: white;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    padding: 20px;
    margin-bottom: 24px;
}

.filter-form {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: center;
}

.search-box {
    flex: 2;
    min-width: 250px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.9rem;
}

.search-box input {
    width: 100%;
    padding: 12px 16px 12px 42px;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.search-box input:focus {
    border-color: #f59e0b;
    outline: none;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.filter-group {
    flex: 3;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-select {
    flex: 1;
    min-width: 140px;
    padding: 12px 16px;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
}

.filter-select:focus {
    border-color: #f59e0b;
    outline: none;
}

.btn-filter {
    padding: 12px 24px;
    background: #f59e0b;
    color: white;
    border: none;
    border-radius: 16px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-filter:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.btn-clear {
    padding: 12px 24px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 16px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-clear:hover {
    background: #e2e8f0;
    color: #334155;
}

.resources-table-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.resources-table {
    width: 100%;
    border-collapse: collapse;
}

.resources-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #f1f5f9;
}

.resources-table th {
    padding: 16px 20px;
    text-align: left;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.resources-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}

.resources-table tr:last-child td {
    border-bottom: none;
}

.resources-table tr:hover {
    background: #f8fafc;
}

.type-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    font-size: 18px;
}

.type-badge.pdf {
    background: #fee2e2;
    color: #dc2626;
}

.type-badge.video {
    background: #dbeafe;
    color: #2563eb;
}

.type-badge.link {
    background: #fef3c7;
    color: #d97706;
}

.type-badge.document {
    background: #e0f2fe;
    color: #0284c7;
}

.resource-title-link {
    color: #0f172a;
    text-decoration: none;
    font-weight: 500;
    display: block;
    margin-bottom: 4px;
}

.resource-title-link:hover {
    color: #f59e0b;
}

.resource-description-preview {
    color: #64748b;
    font-size: 0.8rem;
    display: block;
}

.unit-code {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 10px;
    border-radius: 40px;
    font-size: 0.8rem;
    font-weight: 600;
}

.topic-cell {
    min-width: 120px;
}

.upload-date {
    color: #64748b;
    font-size: 0.9rem;
}

.download-count {
    color: #64748b;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.download-count i {
    color: #f59e0b;
    font-size: 0.8rem;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    color: white;
}

.btn-view {
    background: #f59e0b;
}

.btn-view:hover {
    background: #d97706;
    transform: scale(1.05);
}

.btn-download {
    background: #10b981;
}

.btn-download:hover {
    background: #059669;
    transform: scale(1.05);
}

.btn-edit {
    background: #3b82f6;
}

.btn-edit:hover {
    background: #2563eb;
    transform: scale(1.05);
}

.btn-delete {
    background: #ef4444;
    border: none;
    padding: 0;
}

.btn-delete:hover {
    background: #dc2626;
    transform: scale(1.05);
}

.pagination-container {
    padding: 20px;
    display: flex;
    justify-content: center;
    border-top: 1px solid #f1f5f9;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon i {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 24px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.btn-create-empty {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: #f59e0b;
    color: white;
    text-decoration: none;
    border-radius: 40px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-create-empty:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.text-muted {
    color: #94a3b8;
}

.tips-section {
    margin-top: 32px;
}

.tip-card {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-radius: 20px;
    padding: 20px;
    display: flex;
    gap: 16px;
}

.tip-card i {
    font-size: 24px;
    color: #f59e0b;
}

.tip-content h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
}

.tip-content ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tip-content li {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tip-content li i {
    font-size: 0.8rem;
    color: #10b981;
}

@media (max-width: 768px) {
    .resources-container {
        padding: 16px;
    }
    
    .resources-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .btn-create {
        width: 100%;
        justify-content: center;
    }
    
    .filter-form {
        flex-direction: column;
    }
    
    .filter-group {
        flex-direction: column;
        width: 100%;
    }
    
    .resources-table {
        display: block;
        overflow-x: auto;
    }
    
    .action-buttons {
        flex-wrap: wrap;
    }
    
    .tip-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush