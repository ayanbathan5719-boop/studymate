@extends('layouts.app')

@section('title', $resource->title . ' - Resource Details')
@section('page-icon', 'fa-file-alt')
@section('page-title', 'Resource Details')

@section('content')
<div class="resource-show-container">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('resources.index') }}"><i class="fas fa-file-alt"></i> Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($resource->title, 30) }}</li>
        </ol>
    </nav>

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
                        <i class="fas fa-file-pdf"></i> PDF
                    @elseif($resource->type === 'video')
                        <i class="fas fa-video"></i> Video
                    @elseif($resource->type === 'link')
                        <i class="fas fa-link"></i> Link
                    @elseif($resource->type === 'document')
                        <i class="fas fa-file-word"></i> Document
                    @else
                        <i class="fas fa-file"></i> {{ ucfirst($resource->type) }}
                    @endif
                </div>
                
                <h1 class="resource-title">{{ $resource->title }}</h1>
                
                <div class="resource-meta">
                    <div class="meta-item">
                        <i class="fas fa-user-circle"></i>
                        <span>Uploaded by {{ $resource->user->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $resource->created_at->format('F j, Y') }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $resource->created_at->diffForHumans() }}</span>
                    </div>
                    @if($resource->file_size)
                    <div class="meta-item">
                        <i class="fas fa-weight-hanging"></i>
                        <span>{{ number_format($resource->file_size / 1024, 2) }} KB</span>
                    </div>
                    @endif
                    <div class="meta-item">
                        <i class="fas fa-download"></i>
                        <span>{{ $resource->downloads_count ?? 0 }} downloads</span>
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
            </div>

            {{-- Resource Preview/Content Card --}}
            <div class="resource-content-card">
                <h3><i class="fas fa-eye"></i> Resource Preview</h3>
                
                <div class="resource-preview">
                    @if($resource->type === 'pdf')
                        {{-- PDF Preview --}}
                        @if($resource->file_path)
                            <div class="pdf-preview">
                                <iframe src="{{ asset('storage/' . $resource->file_path) }}" 
                                        class="pdf-viewer" 
                                        frameborder="0">
                                </iframe>
                                <div class="preview-fallback">
                                    <i class="fas fa-file-pdf fa-3x"></i>
                                    <p>PDF preview not available. Please download to view.</p>
                                </div>
                            </div>
                        @else
                            <div class="no-preview">
                                <i class="fas fa-file-pdf fa-4x"></i>
                                <p>No preview available for this PDF</p>
                            </div>
                        @endif
                    
                    @elseif($resource->type === 'video' && $resource->video_url)
                        {{-- Video Embed --}}
                        <div class="video-preview">
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
                            @else
                                <video controls class="video-player">
                                    <source src="{{ asset('storage/' . $resource->file_path) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    
                    @elseif($resource->type === 'link')
                        {{-- Link Preview --}}
                        <div class="link-preview">
                            <div class="link-card">
                                <div class="link-icon">
                                    <i class="fas fa-globe fa-3x"></i>
                                </div>
                                <div class="link-details">
                                    <h4>{{ $resource->title }}</h4>
                                    <p class="link-url">{{ $resource->link_url ?? $resource->url }}</p>
                                    <a href="{{ $resource->link_url ?? $resource->url }}" 
                                       target="_blank" 
                                       class="btn-link-external">
                                        <i class="fas fa-external-link-alt"></i> Visit Link
                                    </a>
                                </div>
                            </div>
                        </div>
                    
                    @elseif($resource->type === 'document')
                        {{-- Document Preview --}}
                        @if($resource->file_path)
                            <div class="document-preview">
                                @if(pathinfo($resource->file_path, PATHINFO_EXTENSION) === 'txt')
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
                                <i class="fas fa-file-word fa-4x"></i>
                                <p>No preview available</p>
                            </div>
                        @endif
                    
                    @else
                        {{-- Generic File Preview --}}
                        <div class="generic-preview">
                            <i class="fas fa-file fa-4x"></i>
                            <p>Preview not available for this file type</p>
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="resource-actions">
                    @if($resource->file_path)
                        <a href="{{ route('resources.download', $resource->id) }}" 
                           class="btn-action btn-download"
                           onclick="trackDownload({{ $resource->id }})">
                            <i class="fas fa-download"></i> Download Resource
                        </a>
                    @endif
                    
                    @if($resource->type === 'link' && ($resource->link_url ?? $resource->url))
                        <a href="{{ $resource->link_url ?? $resource->url }}" 
                           target="_blank" 
                           class="btn-action btn-external">
                            <i class="fas fa-external-link-alt"></i> Open Link
                        </a>
                    @endif

                    @can('update', $resource)
                        <a href="{{ route('resources.edit', $resource->id) }}" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan

                    @can('delete', $resource)
                        <form action="{{ route('resources.destroy', $resource->id) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this resource?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            {{-- Comments/Discussion Section --}}
            <div class="resource-comments-card">
                <h3><i class="fas fa-comments"></i> Discussion</h3>
                
                {{-- Comment Form --}}
                <form action="{{ route('resources.comment', $resource->id) }}" method="POST" class="comment-form">
                    @csrf
                    <div class="comment-input-group">
                        <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" 
                             alt="Avatar" 
                             class="comment-avatar">
                        <textarea name="comment" 
                                  rows="2" 
                                  placeholder="Share your thoughts about this resource..." 
                                  required></textarea>
                    </div>
                    <button type="submit" class="btn-submit-comment">
                        <i class="fas fa-paper-plane"></i> Post Comment
                    </button>
                </form>

                {{-- Comments List --}}
                <div class="comments-list">
                    @forelse($resource->comments ?? [] as $comment)
                        <div class="comment-item">
                            <img src="{{ $comment->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                 alt="Avatar" 
                                 class="comment-avatar">
                            <div class="comment-content">
                                <div class="comment-header">
                                    <span class="comment-author">{{ $comment->user->name }}</span>
                                    <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="comment-text">{{ $comment->content }}</p>
                                
                                @can('delete', $comment)
                                    <form action="{{ route('resources.comment.destroy', $comment->id) }}" 
                                          method="POST" 
                                          class="delete-comment-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete-comment">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="no-comments">
                            <i class="fas fa-comment-slash"></i>
                            <p>No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar - Related Information --}}
        <div class="resource-sidebar">
            {{-- Related Resources Card --}}
            <div class="sidebar-card">
                <h4><i class="fas fa-layer-group"></i> Related Resources</h4>
                
                @if(isset($relatedResources) && $relatedResources->count() > 0)
                    <div class="related-resources-list">
                        @foreach($relatedResources as $related)
                            <a href="{{ route('resources.show', $related->id) }}" class="related-item">
                                <div class="related-icon">
                                    @if($related->type === 'pdf')
                                        <i class="fas fa-file-pdf"></i>
                                    @elseif($related->type === 'video')
                                        <i class="fas fa-video"></i>
                                    @elseif($related->type === 'link')
                                        <i class="fas fa-link"></i>
                                    @else
                                        <i class="fas fa-file"></i>
                                    @endif
                                </div>
                                <div class="related-details">
                                    <span class="related-title">{{ Str::limit($related->title, 30) }}</span>
                                    <span class="related-meta">{{ $related->downloads_count ?? 0 }} downloads</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="no-related">No related resources found</p>
                @endif
            </div>

            {{-- Tags Card --}}
            @if($resource->tags && $resource->tags->count() > 0)
                <div class="sidebar-card">
                    <h4><i class="fas fa-tags"></i> Tags</h4>
                    <div class="tags-cloud">
                        @foreach($resource->tags as $tag)
                            <a href="{{ route('resources.index', ['tag' => $tag->name]) }}" class="tag-badge">
                                <i class="fas fa-tag"></i> {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Unit Information Card --}}
            @if($resource->unit)
                <div class="sidebar-card">
                    <h4><i class="fas fa-book"></i> Unit Information</h4>
                    <div class="unit-info">
                        <span class="unit-code">{{ $resource->unit->code }}</span>
                        <span class="unit-name">{{ $resource->unit->name }}</span>
                        <a href="{{ route('units.show', $resource->unit->id) }}" class="btn-view-unit">
                            <i class="fas fa-arrow-right"></i> View Unit
                        </a>
                    </div>
                </div>
            @endif

            {{-- Download History (for admins/owners) --}}
            @can('view-download-history', $resource)
                <div class="sidebar-card">
                    <h4><i class="fas fa-history"></i> Recent Downloads</h4>
                    <div class="downloads-list">
                        @forelse($resource->recentDownloads ?? [] as $download)
                            <div class="download-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $download->user->name ?? 'Unknown' }}</span>
                                <span class="download-time">{{ $download->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="no-downloads">No downloads yet</p>
                        @endforelse
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>

{{-- Hidden form for tracking downloads --}}
<form id="track-download-form" method="POST" action="{{ route('resources.track-download') }}" style="display: none;">
    @csrf
    <input type="hidden" name="resource_id" id="track-resource-id">
</form>
@endsection

@push('styles')
<style>
/* ===== RESOURCE SHOW PAGE - PROFESSIONAL CARD PLAY STYLING ===== */

.resource-show-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
}

/* Breadcrumb styling */
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

/* Alert styling */
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

/* Main grid layout */
.resource-show-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
}

/* ===== MAIN CONTENT CARDS ===== */
.resource-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.resource-header-card,
.resource-content-card,
.resource-comments-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    padding: 24px;
    transition: all 0.2s ease;
}

.resource-header-card:hover,
.resource-content-card:hover,
.resource-comments-card:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
}

/* Resource type badge */
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

/* Resource title */
.resource-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 16px;
    line-height: 1.3;
    letter-spacing: -0.02em;
}

/* Resource meta information */
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
    font-size: 0.9rem;
}

/* Resource description */
.resource-description h3 {
    font-size: 1.1rem;
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

/* Resource preview section */
.resource-preview {
    margin: 20px 0;
    min-height: 200px;
    background: #f8fafc;
    border-radius: 16px;
    overflow: hidden;
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
    padding: 32px;
}

.link-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.link-icon i {
    color: #f59e0b;
}

.link-details h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 8px;
}

.link-url {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 12px;
    word-break: break-all;
}

.btn-link-external {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f59e0b;
    color: white;
    border-radius: 40px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-link-external:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
    color: white;
}

.text-preview {
    padding: 20px;
    background: white;
    border-radius: 12px;
    font-family: monospace;
    white-space: pre-wrap;
    margin: 0;
}

.no-preview,
.generic-preview {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}

.no-preview i,
.generic-preview i {
    margin-bottom: 16px;
}

/* Resource action buttons */
.resource-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.btn-action {
    padding: 10px 24px;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-action:hover {
    transform: translateY(-1px);
    text-decoration: none;
}

.btn-download {
    background: #10b981;
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.btn-download:hover {
    background: #059669;
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
    color: white;
}

.btn-external {
    background: #3b82f6;
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.btn-external:hover {
    background: #2563eb;
    box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
    color: white;
}

.btn-edit {
    background: #f59e0b;
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.btn-edit:hover {
    background: #d97706;
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
    color: white;
}

.btn-delete {
    background: #ef4444;
    color: white;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    border: none;
}

.btn-delete:hover {
    background: #dc2626;
    box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
    color: white;
}

/* Comments section */
.comment-form {
    margin-bottom: 24px;
}

.comment-input-group {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
}

.comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 40px;
    object-fit: cover;
}

.comment-input-group textarea {
    flex: 1;
    padding: 12px;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    font-size: 0.9rem;
    resize: vertical;
    transition: all 0.2s ease;
}

.comment-input-group textarea:focus {
    border-color: #f59e0b;
    outline: none;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.btn-submit-comment {
    padding: 8px 24px;
    background: #f59e0b;
    color: white;
    border: none;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    float: right;
}

.btn-submit-comment:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.comments-list {
    clear: both;
    margin-top: 32px;
}

.comment-item {
    display: flex;
    gap: 12px;
    padding: 16px 0;
    border-bottom: 1px solid #f1f5f9;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-content {
    flex: 1;
    position: relative;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
}

.comment-author {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.9rem;
}

.comment-date {
    color: #94a3b8;
    font-size: 0.8rem;
}

.comment-text {
    color: #334155;
    font-size: 0.9rem;
    line-height: 1.5;
}

.delete-comment-form {
    position: absolute;
    top: 0;
    right: 0;
}

.btn-delete-comment {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s ease;
}

.btn-delete-comment:hover {
    color: #ef4444;
}

.no-comments {
    text-align: center;
    padding: 32px;
    color: #94a3b8;
}

.no-comments i {
    font-size: 32px;
    margin-bottom: 12px;
}

/* ===== SIDEBAR CARDS ===== */
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
    transition: all 0.2s ease;
}

.sidebar-card:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
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
    font-size: 0.9rem;
}

/* Related resources list */
.related-resources-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.related-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.related-item:hover {
    background: #f8fafc;
    text-decoration: none;
}

.related-icon {
    width: 36px;
    height: 36px;
    background: #f1f5f9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f59e0b;
}

.related-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.related-title {
    font-size: 0.9rem;
    font-weight: 500;
    color: #0f172a;
}

.related-meta {
    font-size: 0.75rem;
    color: #94a3b8;
}

.no-related {
    color: #94a3b8;
    font-size: 0.9rem;
    text-align: center;
    padding: 16px;
}

/* Tags cloud */
.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 6px 12px;
    border-radius: 40px;
    font-size: 0.8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}

.tag-badge:hover {
    background: #f59e0b;
    color: white;
    text-decoration: none;
}

/* Unit info */
.unit-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.unit-code {
    font-size: 0.75rem;
    font-weight: 600;
    color: #f59e0b;
    background: #fffbeb;
    padding: 4px 12px;
    border-radius: 40px;
    display: inline-block;
    align-self: flex-start;
}

.unit-name {
    font-size: 0.95rem;
    color: #0f172a;
    font-weight: 500;
}

.btn-view-unit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: transparent;
    color: #f59e0b;
    border: 1px solid #f59e0b;
    border-radius: 40px;
    font-size: 0.8rem;
    text-decoration: none;
    transition: all 0.2s ease;
    margin-top: 8px;
}

.btn-view-unit:hover {
    background: #f59e0b;
    color: white;
    text-decoration: none;
}

/* Downloads list */
.downloads-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.download-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    padding: 6px 0;
}

.download-item i {
    color: #f59e0b;
    font-size: 0.8rem;
    width: 20px;
}

.download-item span {
    color: #334155;
}

.download-time {
    margin-left: auto;
    font-size: 0.7rem;
    color: #94a3b8;
}

.no-downloads {
    color: #94a3b8;
    font-size: 0.85rem;
    text-align: center;
    padding: 12px;
}

/* Responsive design */
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
    
    .resource-actions {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
    
    .comment-input-group {
        flex-direction: column;
    }
    
    .comment-avatar {
        align-self: flex-start;
    }
    
    .link-card {
        flex-direction: column;
        text-align: center;
    }
    
    .btn-submit-comment {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
function trackDownload(resourceId) {
    document.getElementById('track-resource-id').value = resourceId;
    document.getElementById('track-download-form').submit();
}

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