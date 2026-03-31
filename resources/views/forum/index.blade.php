@extends('layouts.app')

@section('title', 'Forum')
@section('page-title', 'Forum Discussions')

@section('styles')
<link rel="stylesheet" href="/css/forum/index.css">
<style>
/* Forum Card Hover Effects */
.post-card {
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    background: white;
}

.post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    border-color: #f59e0b;
}

.post-card:hover .post-title a {
    color: #f59e0b;
}

/* Filter Dropdown Styling */
.filter-select, .filter-input {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 15px;
    transition: all 0.2s ease;
}

.filter-select:hover, .filter-input:hover {
    border-color: #cbd5e1;
}

.filter-select:focus, .filter-input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    border: 2px dashed #e2e8f0;
    transition: all 0.3s ease;
}

.empty-state:hover {
    border-color: #f59e0b;
    background: #fef9f0;
}

.empty-icon {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.empty-state:hover .empty-icon {
    color: #f59e0b;
    transform: scale(1.05);
}

.empty-state h3 {
    color: #1e293b;
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 25px;
}
</style>
@endsection

@section('content')
<div class="forum-container">
    <!-- Header Section -->
    <div class="forum-header">
        <div class="header-left">
            <h1><i class="fas fa-comments"></i> Forum Discussions</h1>
            <p class="subtitle">Join the conversation with your peers and lecturers</p>
        </div>
        <div class="header-right">
            <a href="{{ route('forum.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> New Post
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <form method="GET" action="{{ route('forum.index') }}" class="filters-form">
            <div class="filter-group">
                <label for="unit"><i class="fas fa-layer-group"></i> Unit:</label>
                <select name="unit" id="unit" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ $currentUnit == $unit->code ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="tag"><i class="fas fa-tag"></i> Tag:</label>
                <input type="text" 
                       name="tag" 
                       id="tag" 
                       class="filter-input" 
                       placeholder="e.g., assignment, exam" 
                       value="{{ $currentTag }}">
            </div>

            <div class="filter-group search-group">
                <label for="search"><i class="fas fa-search"></i> Search:</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="filter-input" 
                       placeholder="Search posts..." 
                       value="{{ $search }}">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <div class="filter-actions">
                <a href="{{ route('forum.index') }}" class="btn-clear">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Posts List -->
    <div class="posts-container">
        @forelse($posts as $post)
            <div class="post-card {{ $post->is_pinned ? 'pinned' : '' }}">
                @if($post->is_pinned)
                    <div class="pinned-badge">
                        <i class="fas fa-thumbtack"></i> Pinned
                    </div>
                @endif
                
                @if($post->is_announcement)
                    <div class="announcement-badge">
                        <i class="fas fa-bullhorn"></i> Announcement
                    </div>
                @endif
                
                <div class="post-header">
                    <div class="post-author">
                        <div class="author-avatar">
                            {{ substr($post->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="author-info">
                            <span class="author-name">{{ $post->user->name ?? 'Unknown' }}</span>
                            <span class="author-role {{ $post->is_lecturer_post ? 'lecturer' : 'student' }}">
                                {{ $post->role_badge }}
                            </span>
                        </div>
                    </div>
                    <div class="post-meta">
                        <span class="unit-code">{{ $post->unit_code }}</span>
                        <span class="post-date">
                            <i class="far fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="post-content">
                    <h3 class="post-title">
                        <a href="{{ route('forum.show', $post->id) }}">{{ $post->title }}</a>
                    </h3>
                    <p class="post-excerpt">{{ Str::limit(strip_tags($post->content), 200) }}</p>
                </div>

                <div class="post-footer">
                    <div class="post-tags">
                        @if($post->tags)
                            @foreach($post->tags as $tag)
                                <a href="{{ route('forum.index', ['tag' => $tag]) }}" class="tag">
                                    <i class="fas fa-tag"></i> {{ $tag }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                    
                    <div class="post-stats">
                        <span class="stat">
                            <i class="fas fa-eye"></i> {{ number_format($post->views) }}
                        </span>
                        <span class="stat">
                            <i class="fas fa-comment"></i> {{ number_format($post->replies_count) }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>No posts found</h3>
                <p>Be the first to start a discussion in your units!</p>
                <a href="{{ route('forum.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Create New Post
                </a>
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $posts->withQueryString()->links() }}
        </div>
    </div>
</div>

<script src="/js/forum/index.js"></script>
@endsection