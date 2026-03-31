@extends('student.layouts.master')

@section('title', $post->title)
@section('page-icon', 'fa-comment')
@section('page-title', 'Forum Post')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 30) }}</li>
        </ol>
    </nav>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forum/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/forum-show.css') }}">
@endpush

@section('content')
<div class="post-container">
    <!-- Main Post -->
    <div class="post-card">
        <div class="post-header">
            @if($post->is_pinned)
                <span class="post-badge badge-pinned"><i class="fas fa-thumbtack"></i> Pinned</span>
            @endif
            @if($post->is_announcement)
                <span class="post-badge badge-announcement"><i class="fas fa-bullhorn"></i> Announcement</span>
            @endif
            
            <h1 class="post-title">{{ $post->title }}</h1>
            
            <div class="post-meta">
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
                            @if($post->user->hasRole('admin'))
                                <span class="role-badge admin">Admin</span>
                            @elseif($post->user->hasRole('lecturer'))
                                <span class="role-badge lecturer">Lecturer</span>
                            @else
                                <span class="role-badge student">Student</span>
                            @endif
                        </div>
                        <span class="author-role {{ $post->user->hasRole('lecturer') ? 'lecturer' : '' }}">
                            {{ $post->user->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                        </span>
                    </div>
                </div>
                
                <div class="post-unit">
                    <i class="fas fa-layer-group"></i> {{ $post->unit_code }}
                </div>
                
                <div class="post-stats">
                    <span><i class="fas fa-eye"></i> {{ number_format($post->views) }}</span>
                    <span><i class="fas fa-comment"></i> {{ number_format($replies->count()) }}</span>
                </div>
            </div>
        </div>

        <!-- Post Content -->
        <div class="post-content">
            {!! nl2br(e($post->content)) !!}
        </div>

        <!-- Attached Resources Section -->
        @if(isset($post->resources) && $post->resources->count() > 0)
            <div class="attachments-section">
                <h4><i class="fas fa-paperclip"></i> Attached Resources</h4>
                <div class="attachments-list">
                    @foreach($post->resources as $resource)
                        <div class="attachment-item">
                            <div class="attachment-icon">
                                @if(($resource->file_type ?? $resource->type) === 'pdf')
                                    <i class="fas fa-file-pdf"></i>
                                @elseif(($resource->file_type ?? $resource->type) === 'video')
                                    <i class="fas fa-video"></i>
                                @elseif(($resource->file_type ?? $resource->type) === 'link')
                                    <i class="fas fa-link"></i>
                                @elseif(($resource->file_type ?? $resource->type) === 'document')
                                    <i class="fas fa-file-alt"></i>
                                @else
                                    <i class="fas fa-paperclip"></i>
                                @endif
                            </div>
                            <div class="attachment-info">
                                <strong>{{ $resource->title ?? $resource->name ?? 'Resource' }}</strong>
                                @if($resource->file_path)
                                    <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="attachment-link" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @elseif($resource->url)
                                    <a href="{{ $resource->url }}" target="_blank" class="attachment-link">
                                        <i class="fas fa-external-link-alt"></i> Visit Link
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="post-actions">
            <form method="POST" action="{{ route('forum.flag', $post) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-flag" onclick="return confirm('Flag this post as inappropriate?')">
                    <i class="fas fa-flag"></i> Flag as Inappropriate
                </button>
            </form>
        </div>
    </div>

    <!-- Replies Section -->
    <div class="replies-section">
        <div class="replies-header">
            <i class="fas fa-comments"></i> Replies ({{ $replies->count() }})
        </div>

        <div class="replies-list">
            @forelse($replies as $reply)
                <div class="reply-card nested-level-{{ min($reply->level ?? 0, 3) }}">
                    <div class="reply-header">
                        <div class="reply-author">
                            <div class="reply-avatar">
                                @if($reply->user->avatar)
                                    <img src="{{ Storage::url($reply->user->avatar) }}" alt="{{ $reply->user->name }}">
                                @else
                                    {{ substr($reply->user->name ?? 'U', 0, 1) }}
                                @endif
                            </div>
                            <div class="reply-info">
                                <div class="reply-name">
                                    {{ $reply->user->name ?? 'Unknown' }}
                                    @if($reply->user->hasRole('admin'))
                                        <span class="role-badge admin">Admin</span>
                                    @elseif($reply->user->hasRole('lecturer'))
                                        <span class="role-badge lecturer">Lecturer</span>
                                    @else
                                        <span class="role-badge student">Student</span>
                                    @endif
                                </div>
                                <span class="reply-role {{ $reply->user->hasRole('lecturer') ? 'lecturer' : '' }}">
                                    {{ $reply->user->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                                </span>
                            </div>
                        </div>
                        <div class="reply-time">
                            <i class="far fa-clock"></i> {{ $reply->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    @if($reply->parent_id)
                        <div class="reply-to-indicator">
                            <i class="fas fa-reply"></i> Replying to {{ $reply->parent->user->name ?? 'previous comment' }}
                        </div>
                    @endif
                    
                    <div class="reply-content">
                        {!! nl2br(e($reply->content)) !!}
                    </div>
                    
                    <div class="reply-actions">
                        <button class="btn-quote" onclick="quoteReply({{ $reply->id }}, '{{ addslashes($reply->user->name) }}', '{{ addslashes(str_replace(["\n", "\r"], ' ', $reply->content)) }}')">
                            <i class="fas fa-quote-left"></i> Quote
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-replies">
                    <i class="fas fa-comment-slash"></i>
                    <p>No replies yet. Be the first to respond!</p>
                </div>
            @endforelse
        </div>

        <!-- Reply Form -->
        <div class="reply-form">
            <h3><i class="fas fa-reply"></i> Add a Reply</h3>
            <form method="POST" action="{{ route('forum.reply', $post) }}" id="replyForm">
                @csrf
                <div class="form-group">
                    <textarea name="content" id="replyContent" class="form-control" rows="4" placeholder="Write your reply..." required></textarea>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Post Reply
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.forumPostId = {{ $post->id }};
        window.replyUrl = '{{ route('forum.reply', $post) }}';
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/forum/common.js') }}"></script>
    <script src="{{ asset('js/student/forum.js') }}"></script>
    <script src="{{ asset('js/student/forum-show.js') }}"></script>
@endpush
@endsection