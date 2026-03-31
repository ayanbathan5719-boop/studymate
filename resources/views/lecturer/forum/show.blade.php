@extends('lecturer.layouts.master')

@section('title', $post->title)
@section('page-icon', 'fa-comment')
@section('page-title', $post->title)

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 30) }}</li>
        </ol>
    </nav>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forum/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lecturer/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lecturer/forum-show.css') }}">
@endpush

@section('content')
<div class="post-container">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Main Post -->
    <div class="post-card @if($post->is_pinned) pinned @endif @if($post->is_announcement) announcement @endif">
        @if($post->is_pinned)
            <span class="post-badge badge-pinned"><i class="fas fa-thumbtack"></i> Pinned Post</span>
        @endif
        @if($post->is_announcement)
            <span class="post-badge badge-announcement"><i class="fas fa-bullhorn"></i> Announcement</span>
        @endif

        <div class="post-header">
            <div class="post-author">
                <div class="author-avatar">{{ substr($post->user->name ?? 'U', 0, 1) }}</div>
                <div class="author-info">
                    <span class="author-name">
                        {{ $post->user->name ?? 'Unknown' }}
                        @if($post->user->hasRole('admin'))
                            <span class="role-badge admin">Admin</span>
                        @elseif($post->user->hasRole('lecturer'))
                            <span class="role-badge lecturer">Lecturer</span>
                        @else
                            <span class="role-badge student">Student</span>
                        @endif
                    </span>
                    <span class="author-role {{ $post->user->hasRole('lecturer') ? 'lecturer' : 'student' }}">
                        {{ $post->user->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                    </span>
                </div>
            </div>
            <div class="post-meta">
                <span class="unit-badge">{{ $post->unit_code }}</span>
                <div class="post-date">
                    <i class="far fa-clock"></i> {{ $post->created_at->format('M d, Y \a\t h:i A') }}
                </div>
                <div class="post-stats">
                    <span><i class="fas fa-eye"></i> {{ number_format($post->views) }} views</span>
                    <span><i class="fas fa-comment"></i> {{ $replies->count() }} replies</span>
                </div>
            </div>
        </div>

        <div class="post-content">
            {!! nl2br(e($post->content)) !!}
        </div>

        <div class="post-actions">
            <button class="btn-action btn-pin @if($post->is_pinned) active @endif" onclick="togglePin({{ $post->id }}, this)">
                <i class="fas fa-thumbtack"></i> {{ $post->is_pinned ? 'Unpin' : 'Pin' }}
            </button>
            
            <button class="btn-action btn-announce @if($post->is_announcement) active @endif" onclick="toggleAnnouncement({{ $post->id }}, this)">
                <i class="fas fa-bullhorn"></i> {{ $post->is_announcement ? 'Remove Announcement' : 'Mark as Announcement' }}
            </button>
            
            @if($post->user_id == Auth::id())
                <a href="{{ route('lecturer.forum.edit', $post) }}" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i> Edit Post
                </a>
            @endif
            
            <form method="POST" action="{{ route('lecturer.forum.flag', $post) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-action btn-flag" onclick="return confirm('Flag this post as inappropriate?')">
                    <i class="fas fa-flag"></i> Flag Post
                </button>
            </form>
        </div>
    </div>

    <!-- Replies Section -->
    <div class="replies-section">
        <div class="section-header">
            <h2><i class="fas fa-comments"></i> Replies</h2>
            <span class="reply-count">{{ $replies->count() }} {{ Str::plural('Reply', $replies->count()) }}</span>
        </div>

        @if($replies->count() > 0)
            <div class="replies-list">
                @foreach($replies as $reply)
                    <div class="reply-card" id="reply-{{ $reply->id }}">
                        <div class="reply-header">
                            <div class="reply-author">
                                <div class="reply-avatar">{{ substr($reply->user->name ?? 'U', 0, 1) }}</div>
                                <div class="reply-author-info">
                                    <span class="reply-author-name">
                                        {{ $reply->user->name ?? 'Unknown' }}
                                        @if($reply->user->hasRole('admin'))
                                            <span class="role-badge admin">Admin</span>
                                        @elseif($reply->user->hasRole('lecturer'))
                                            <span class="role-badge lecturer">Lecturer</span>
                                        @else
                                            <span class="role-badge student">Student</span>
                                        @endif
                                    </span>
                                    <span class="reply-author-role {{ $reply->user->hasRole('lecturer') ? 'lecturer' : '' }}">
                                        {{ $reply->user->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                                    </span>
                                </div>
                            </div>
                            <div class="reply-date">
                                <i class="far fa-clock"></i> {{ $reply->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="reply-content">
                            {{ $reply->content }}
                        </div>
                        @if(Auth::user()->hasRole('lecturer'))
                            <div class="reply-actions">
                                <form method="POST" action="{{ route('lecturer.forum.delete-reply', $reply) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete this reply?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-reply-action btn-reply-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                <i class="fas fa-comment-slash" style="font-size: 48px; margin-bottom: 15px;"></i>
                <p>No replies yet. Be the first to respond!</p>
            </div>
        @endif
    </div>

    <!-- Reply Form -->
    <div class="reply-form-card">
        <h3><i class="fas fa-reply"></i> Post a Reply</h3>
        <form method="POST" action="{{ route('lecturer.forum.reply', $post) }}">
            @csrf
            <div class="form-group">
                <textarea name="content" class="form-control" placeholder="Write your reply..." required></textarea>
            </div>
            <button type="submit" class="btn-reply">
                <i class="fas fa-paper-plane"></i> Post Reply
            </button>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        window.togglePinUrl = '{{ route('lecturer.forum.toggle-pin', ['post' => '__POST_ID__']) }}';
        window.toggleAnnouncementUrl = '{{ route('lecturer.forum.toggle-announcement', ['post' => '__POST_ID__']) }}';
        window.csrfToken = '{{ csrf_token() }}';
        window.postId = {{ $post->id }};
    </script>
    <script src="{{ asset('js/forum/common.js') }}"></script>
    <script src="{{ asset('js/lecturer/forum.js') }}"></script>
    <script src="{{ asset('js/lecturer/forum-show.js') }}"></script>
@endpush
@endsection