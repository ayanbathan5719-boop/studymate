@extends('lecturer.layouts.master')

@section('title', 'Flagged Posts')
@section('page-icon', 'fa-flag')
@section('page-title', 'Flagged Posts')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('lecturer.forum.index') }}">Forum</a></li>
        <li class="breadcrumb-item active" aria-current="page">Flagged Posts</li>
    </ol>
</nav>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forum/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/lecturer/forum.css') }}">
<style>
    .flag-badge {
        background: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .flag-count {
        background: #dc3545;
        color: white;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 0.7rem;
        font-weight: bold;
    }
    
    .post-card.flagged {
        border-left: 3px solid #dc3545;
    }
    
    .resolve-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 4px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
    }
    
    .resolve-btn:hover {
        background: #218838;
    }
    
    .flag-details {
        background: #fff3f3;
        border: 1px solid #ffcccc;
        border-radius: 8px;
        padding: 12px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="forum-container">
    <div class="forum-header">
        <h1><i class="fas fa-flag"></i> Flagged Posts</h1>
        <a href="{{ route('lecturer.forum.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Forum
        </a>
    </div>

    @if(session('success'))
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Posts List -->
    <div class="posts-container">
        @forelse($posts as $post)
        <div class="post-card flagged" data-post-id="{{ $post->id }}">
            <div class="post-header">
                <div class="post-author">
                    <div class="author-avatar">
                        @if($post->user->avatar)
                        <img src="{{ Storage::url($post->user->avatar) }}" alt="{{ $post->user->name }}">
                        @else
                        {{ substr($post->user->name ?? 'U', 0, 1) }}
                        @endif
                    </div>
                    <div class="author-info">
                        <div class="author-name">
                            {{ $post->user->name ?? 'Unknown' }}
                            @if($post->user->hasRole('lecturer'))
                            <span class="role-badge lecturer">Lecturer</span>
                            @endif
                        </div>
                        <div class="post-meta">
                            <span class="unit-badge">{{ $post->unit_code }}</span>
                            <span class="post-date"><i class="far fa-clock"></i> {{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                <div class="flag-badge">
                    <i class="fas fa-flag"></i> {{ $post->flags_count }} Flag{{ $post->flags_count > 1 ? 's' : '' }}
                </div>
            </div>

            <div class="post-content">
                <h3>{{ $post->title }}</h3>
                {!! nl2br(e($post->content)) !!}
            </div>

            <!-- Flag Details -->
            @if($post->flags && $post->flags->count() > 0)
            <div class="flag-details">
                <h5><i class="fas fa-flag"></i> Flag Reports</h5>
                @foreach($post->flags as $flag)
                <div class="flag-item" style="padding: 8px 0; border-bottom: 1px solid #ffcccc;">
                    <div><strong>{{ $flag->reporter->name ?? 'Unknown' }}</strong> flagged this post</div>
                    <div class="text-muted small">Reason: {{ $flag->reason }}</div>
                    <div class="text-muted small">Reported: {{ $flag->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
                
                <div class="mt-3">
                    <form action="{{ route('lecturer.forum.resolve-flag', $post) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="resolve-btn" onclick="return confirm('Mark these flags as resolved?')">
                            <i class="fas fa-check"></i> Mark as Resolved
                        </button>
                    </form>
                    
                    <form action="{{ route('lecturer.forum.delete-flagged-post', $post) }}" method="POST" style="display: inline; margin-left: 10px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="background: #dc3545; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer;" onclick="return confirm('Delete this post? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete Post
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="post-footer">
                <div class="post-stats">
                    <span><i class="fas fa-eye"></i> {{ number_format($post->views) }} views</span>
                    <span><i class="fas fa-comment"></i> {{ number_format($post->replies_count) }} replies</span>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-flag"></i>
            <h3>No Flagged Posts</h3>
            <p>There are no flagged posts in your units at this time.</p>
            <a href="{{ route('lecturer.forum.index') }}" class="btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Forum
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $posts->withQueryString()->links() }}
    </div>
</div>
@endsection