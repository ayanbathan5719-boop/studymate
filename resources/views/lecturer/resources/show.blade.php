@extends('lecturer.layouts.master')

@section('title', $resource->title . ' - Resource Details')
@section('page-icon', 'fa-file-alt')
@section('page-title', 'Resource Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.resources.index') }}"><i class="fas fa-folder-open"></i> Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($resource->title, 30) }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="resource-show-container">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="resource-show-grid">
        {{-- Main Content - Resource Details --}}
        <div class="resource-main">
            {{-- Resource Header Card --}}
            <div class="resource-header-card">
                <div class="resource-type-badge {{ $resource->type }}">
                    @if($resource->type === 'pdf')
                        <i class="fas fa-file-pdf"></i> PDF Document
                    @elseif($resource->type === 'video')
                        <i class="fas fa-video"></i> Video
                    @elseif($resource->type === 'link')
                        <i class="fas fa-link"></i> External Link
                    @else
                        <i class="fas fa-file-alt"></i> Document
                    @endif
                </div>
                
                <h1 class="resource-title">{{ $resource->title }}</h1>
                
                <div class="resource-meta">
                    <div class="meta-item">
                        <i class="fas fa-book"></i>
                        <span>Unit: <strong>{{ $resource->unit_code }}</strong></span>
                    </div>
                    
                    @if($resource->topic)
                        <div class="meta-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Topic: <strong>{{ $resource->topic->name }}</strong></span>
                        </div>
                    @endif
                    
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Uploaded: <strong>{{ $resource->created_at->format('F j, Y') }}</strong></span>
                    </div>
                    
                    @if($resource->file_size)
                        <div class="meta-item">
                            <i class="fas fa-weight-hanging"></i>
                            <span>Size: <strong>{{ number_format($resource->file_size / 1024, 2) }} KB</strong></span>
                        </div>
                    @endif
                    
                    <div class="meta-item">
                        <i class="fas fa-download"></i>
                        <span>Downloads: <strong>{{ $totalDownloads ?? 0 }}</strong></span>
                    </div>
                    
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span>Unique Users: <strong>{{ $uniqueUsers ?? 0 }}</strong></span>
                    </div>
                </div>

                @if($resource->description)
                    <div class="resource-description">
                        <h3><i class="fas fa-align-left"></i> Description</h3>
                        <div class="description-content">
                            {{ $resource->description }}
                        </div>
                    </div>
                @endif

                <div class="action-buttons">
                    <a href="{{ route('lecturer.resources.edit', $resource->id) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit Resource
                    </a>
                    
                    @if($resource->type !== 'link' && $resource->file_path)
                        <a href="{{ route('lecturer.resources.download', $resource->id) }}" class="btn-download">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    @endif
                    
                    <form action="{{ route('lecturer.resources.destroy', $resource->id) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this resource? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            {{-- Resource Preview Card --}}
            <div class="resource-preview-card">
                <h3><i class="fas fa-eye"></i> Resource Preview</h3>
                
                <div class="preview-container">
                    @if($resource->type === 'pdf' && $resource->file_path)
                        <div class="pdf-preview">
                            <iframe src="{{ asset('storage/' . $resource->file_path) }}" 
                                    class="pdf-viewer" 
                                    frameborder="0"
                                    title="PDF Preview">
                            </iframe>
                        </div>
                    
                    @elseif($resource->type === 'video')
                        <div class="video-preview">
                            @if($resource->video_url)
                                @if(str_contains($resource->video_url, 'youtube.com') || str_contains($resource->video_url, 'youtu.be'))
                                    @php
                                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $resource->video_url, $matches);
                                        $videoId = $matches[1] ?? null;
                                    @endphp
                                    @if($videoId)
                                        <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                frameborder="0" 
                                                allowfullscreen>
                                        </iframe>
                                    @endif
                                @elseif(str_contains($resource->video_url, 'vimeo.com'))
                                    @php
                                        $videoId = substr(parse_url($resource->video_url, PHP_URL_PATH), 1);
                                    @endphp
                                    <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                            frameborder="0" 
                                            allowfullscreen>
                                    </iframe>
                                @endif
                            @elseif($resource->file_path)
                                <video controls class="video-player">
                                    <source src="{{ asset('storage/' . $resource->file_path) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    
                    @elseif($resource->type === 'link' && $resource->url)
                        <div class="link-preview">
                            <div class="link-card">
                                <i class="fas fa-globe fa-3x"></i>
                                <div class="link-details">
                                    <h4>{{ $resource->title }}</h4>
                                    <p class="link-url">{{ $resource->url }}</p>
                                    <a href="{{ $resource->url }}" target="_blank" class="btn-visit-link">
                                        <i class="fas fa-external-link-alt"></i> Visit Link
                                    </a>
                                </div>
                            </div>
                        </div>
                    
                    @elseif($resource->type === 'document' && $resource->file_path)
                        <div class="document-preview">
                            @php
                                $extension = pathinfo($resource->file_path, PATHINFO_EXTENSION);
                            @endphp
                            
                            @if(in_array($extension, ['txt', 'md']))
                                <pre class="text-preview">{{ file_get_contents(storage_path('app/public/' . $resource->file_path)) }}</pre>
                            @else
                                <div class="no-preview">
                                    <i class="fas fa-file-word fa-4x"></i>
                                    <p>Preview not available for this document type</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="no-preview">
                            <i class="fas fa-file fa-4x"></i>
                            <p>No preview available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="resource-sidebar">
            {{-- Download Statistics Card --}}
            <div class="sidebar-card">
                <h4><i class="fas fa-chart-bar"></i> Download Statistics</h4>
                
                <div class="stat-grid">
                    <div class="stat-box">
                        <span class="stat-value">{{ $totalDownloads ?? 0 }}</span>
                        <span class="stat-label">Total Downloads</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value">{{ $uniqueUsers ?? 0 }}</span>
                        <span class="stat-label">Unique Users</span>
                    </div>
                </div>
                
                @if($resource->created_at)
                    <div class="stat-timeline">
                        <p><i class="fas fa-clock"></i> Available for {{ $resource->created_at->diffForHumans(null, true) }}</p>
                        <p><i class="fas fa-chart-line"></i> Avg {{ $totalDownloads > 0 ? round($totalDownloads / max(1, $resource->created_at->diffInDays(now()))) : 0 }} downloads/day</p>
                    </div>
                @endif
            </div>

            {{-- Recent Downloads Card --}}
            <div class="sidebar-card">
                <h4><i class="fas fa-history"></i> Recent Downloads</h4>
                
                @if($downloads->isNotEmpty())
                    <div class="downloads-list">
                        @foreach($downloads->take(5) as $download)
                            <div class="download-item">
                                <i class="fas fa-user"></i>
                                <div class="download-info">
                                    <span class="user-name">{{ $download->user->name ?? 'Unknown' }}</span>
                                    <span class="download-time">{{ $download->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($downloads->count() > 5)
                        <a href="#" class="view-all-link" onclick="showAllDownloads()">
                            View All {{ $downloads->count() }} Downloads
                        </a>
                    @endif
                @else
                    <p class="empty-message">No downloads yet</p>
                @endif
            </div>

            {{-- File Information Card --}}
            @if($resource->file_name)
                <div class="sidebar-card">
                    <h4><i class="fas fa-info-circle"></i> File Information</h4>
                    
                    <div class="file-info">
                        <p><strong>Filename:</strong> {{ $resource->file_name }}</p>
                        @if($resource->file_size)
                            <p><strong>Size:</strong> {{ number_format($resource->file_size / 1024, 2) }} KB</p>
                        @endif
                        @if($resource->mime_type)
                            <p><strong>Type:</strong> {{ $resource->mime_type }}</p>
                        @endif
                        <p><strong>Path:</strong> <code>{{ $resource->file_path }}</code></p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- All Downloads Modal --}}
    <div class="modal" id="downloadsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-download"></i> All Downloads ({{ $downloads->total() }})</h5>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <table class="downloads-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Downloaded</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($downloads as $download)
                            <tr>
                                <td>{{ $download->user->name ?? 'Unknown' }}</td>
                                <td>{{ $download->user->email ?? '—' }}</td>
                                <td>{{ $download->created_at->format('M d, Y g:i A') }}</td>
                                <td><code>{{ $download->ip_address ?? '—' }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($downloads->hasPages())
                    <div class="pagination">
                        {{ $downloads->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.resource-show-container {
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

.alert-success,
.alert-error {
    padding: 16px 20px;
    border-radius: 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #dcfce7;
    color: #166534;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #991b1b;
}

.resource-show-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 24px;
}

.resource-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.resource-header-card,
.resource-preview-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 24px;
}

.resource-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 16px;
}

.resource-type-badge.pdf {
    background: #fee2e2;
    color: #dc2626;
}

.resource-type-badge.video {
    background: #dbeafe;
    color: #2563eb;
}

.resource-type-badge.link {
    background: #fef3c7;
    color: #d97706;
}

.resource-type-badge.document {
    background: #e0f2fe;
    color: #0284c7;
}

.resource-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
}

.resource-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 0.9rem;
}

.meta-item i {
    color: #f59e0b;
}

.meta-item strong {
    color: #0f172a;
}

.resource-description h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.resource-description h3 i {
    color: #f59e0b;
}

.description-content {
    color: #334155;
    line-height: 1.6;
    font-size: 0.95rem;
}

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn-edit,
.btn-download,
.btn-delete {
    padding: 10px 20px;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-edit {
    background: #f59e0b;
    color: white;
}

.btn-edit:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.btn-download {
    background: #10b981;
    color: white;
}

.btn-download:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.2);
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(239, 68, 68, 0.2);
}

.resource-preview-card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.resource-preview-card h3 i {
    color: #f59e0b;
}

.preview-container {
    background: #f8fafc;
    border-radius: 16px;
    overflow: hidden;
    min-height: 300px;
}

.pdf-viewer {
    width: 100%;
    height: 600px;
    border: none;
}

.video-preview iframe,
.video-player {
    width: 100%;
    height: 400px;
    border: none;
}

.link-preview {
    padding: 40px;
}

.link-card {
    text-align: center;
}

.link-card i {
    color: #f59e0b;
    margin-bottom: 20px;
}

.link-card h4 {
    font-size: 1.1rem;
    color: #0f172a;
    margin-bottom: 8px;
}

.link-url {
    color: #64748b;
    margin-bottom: 20px;
    word-break: break-all;
}

.btn-visit-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: #f59e0b;
    color: white;
    text-decoration: none;
    border-radius: 40px;
    transition: all 0.2s ease;
}

.btn-visit-link:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.text-preview {
    padding: 20px;
    background: white;
    font-family: monospace;
    white-space: pre-wrap;
    max-height: 400px;
    overflow-y: auto;
    margin: 0;
}

.no-preview {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}

/* Sidebar */
.resource-sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.sidebar-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 20px;
}

.sidebar-card h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.sidebar-card h4 i {
    color: #f59e0b;
}

.stat-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.stat-box {
    text-align: center;
    padding: 16px;
    background: #f8fafc;
    border-radius: 16px;
}

.stat-value {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    color: #f59e0b;
    line-height: 1;
}

.stat-label {
    font-size: 0.8rem;
    color: #64748b;
}

.stat-timeline {
    font-size: 0.85rem;
    color: #64748b;
}

.stat-timeline p {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}

.stat-timeline i {
    color: #f59e0b;
    width: 16px;
}

.downloads-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
}

.download-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px;
    background: #f8fafc;
    border-radius: 12px;
}

.download-item i {
    color: #f59e0b;
    width: 20px;
}

.download-info {
    flex: 1;
}

.user-name {
    display: block;
    font-weight: 500;
    color: #0f172a;
    font-size: 0.9rem;
}

.download-time {
    font-size: 0.75rem;
    color: #94a3b8;
}

.view-all-link {
    display: block;
    text-align: center;
    color: #f59e0b;
    text-decoration: none;
    font-size: 0.85rem;
    padding: 8px;
    border-radius: 40px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.view-all-link:hover {
    background: #fffbeb;
    color: #d97706;
}

.empty-message {
    color: #94a3b8;
    text-align: center;
    padding: 20px;
}

.file-info {
    background: #f8fafc;
    border-radius: 16px;
    padding: 12px;
    font-size: 0.85rem;
}

.file-info p {
    margin-bottom: 8px;
    color: #334155;
}

.file-info code {
    background: #e2e8f0;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal.show {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 24px;
    width: 90%;
    max-width: 800px;
    max-height: 80vh;
    overflow: hidden;
}

.modal-header {
    padding: 20px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h5 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.modal-header h5 i {
    color: #f59e0b;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #64748b;
    transition: color 0.2s ease;
}

.close-btn:hover {
    color: #ef4444;
}

.modal-body {
    padding: 24px;
    overflow-y: auto;
    max-height: calc(80vh - 80px);
}

.downloads-table {
    width: 100%;
    border-collapse: collapse;
}

.downloads-table th {
    text-align: left;
    padding: 12px;
    background: #f8fafc;
    color: #475569;
    font-size: 0.85rem;
    font-weight: 600;
}

.downloads-table td {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 0.9rem;
}

.downloads-table tr:last-child td {
    border-bottom: none;
}

.downloads-table code {
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
}

.pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

@media (max-width: 1024px) {
    .resource-show-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .resource-show-container {
        padding: 16px;
    }
    
    .resource-title {
        font-size: 1.5rem;
    }
    
    .resource-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-edit,
    .btn-download,
    .btn-delete {
        width: 100%;
        justify-content: center;
    }
    
    .stat-grid {
        grid-template-columns: 1fr;
    }
    
    .downloads-table {
        display: block;
        overflow-x: auto;
    }
}
</style>
@endpush

@push('scripts')
<script>
function showAllDownloads() {
    document.getElementById('downloadsModal').classList.add('show');
}

function closeModal() {
    document.getElementById('downloadsModal').classList.remove('show');
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('downloadsModal');
    if (e.target === modal) {
        closeModal();
    }
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.alert-success, .alert-error').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        });
    }, 5000);
});
</script>
@endpush