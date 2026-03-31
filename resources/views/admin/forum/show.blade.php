@extends('admin.layouts.master')

@section('title', $post->title)
@section('page-icon', 'fa-comment')
@section('page-title', 'Post Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 30) }}</li>
        </ol>
    </nav>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forum/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/forum-show.css') }}">
@endpush

@section('content')
<div class="post-container" id="postApp">
    <!-- Action Bar -->
    <div class="action-bar">
        <a href="{{ route('admin.forum.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Forum
        </a>
        <div class="action-buttons">
            <button class="btn-action btn-pin" :class="{ active: post.is_pinned }" @click="togglePin">
                <i class="fas fa-thumbtack"></i> {{ post.is_pinned ? 'Unpin' : 'Pin' }}
            </button>
            <button class="btn-action btn-announce" :class="{ active: post.is_announcement }" @click="toggleAnnouncement">
                <i class="fas fa-bullhorn"></i> {{ post.is_announcement ? 'Remove Announcement' : 'Make Announcement' }}
            </button>
            <button class="btn-action btn-delete" @click="confirmDelete">
                <i class="fas fa-trash"></i> Delete Post
            </button>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div v-if="successMessage" class="alert-success">
        <i class="fas fa-check-circle"></i> @{{ successMessage }}
    </div>
    <div v-if="errorMessage" class="alert-error">
        <i class="fas fa-exclamation-triangle"></i> @{{ errorMessage }}
    </div>

    <div class="post-grid">
        <!-- Main Post Column -->
        <div class="main-column">
            <!-- Post Card -->
            <div class="post-card" :class="{ pinned: post.is_pinned, announcement: post.is_announcement }">
                <div class="post-badges">
                    <span v-if="post.is_pinned" class="badge-pinned">
                        <i class="fas fa-thumbtack"></i> Pinned
                    </span>
                    <span v-if="post.is_announcement" class="badge-announcement">
                        <i class="fas fa-bullhorn"></i> Announcement
                    </span>
                </div>

                <div class="post-header">
                    <div class="post-author">
                        <div class="author-avatar">{{ substr($post->user->name ?? 'U', 0, 1) }}</div>
                        <div class="author-info">
                            <span class="author-name">{{ $post->user->name ?? 'Unknown' }}</span>
                            <span class="author-role {{ $post->user->hasRole('lecturer') ? 'lecturer' : 'student' }}">
                                {{ $post->user->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                            </span>
                        </div>
                    </div>
                    <div class="post-meta">
                        <span class="unit-badge">{{ $post->unit_code }}</span>
                        <span class="post-date">
                            <i class="far fa-clock"></i> {{ $post->created_at->format('M d, Y \a\t h:i A') }}
                        </span>
                    </div>
                </div>

                <h1 class="post-title">{{ $post->title }}</h1>

                <div class="post-content">
                    {!! nl2br(e($post->content)) !!}
                </div>

                <div class="post-stats">
                    <span><i class="fas fa-eye"></i> {{ number_format($post->views) }} views</span>
                    <span><i class="fas fa-comment"></i> {{ $post->replies->count() }} replies</span>
                </div>
            </div>

            <!-- Replies Section -->
            <div class="replies-section">
                <div class="section-header">
                    <h2><i class="fas fa-comments"></i> Replies ({{ $post->replies->count() }})</h2>
                </div>

                <div class="replies-list">
                    @forelse($post->replies as $reply)
                        <div class="reply-card" id="reply-{{ $reply->id }}">
                            <div class="reply-header">
                                <div class="reply-author">
                                    <div class="reply-avatar">{{ substr($reply->user->name ?? 'U', 0, 1) }}</div>
                                    <div class="reply-author-info">
                                        <span class="reply-author-name">{{ $reply->user->name ?? 'Unknown' }}</span>
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
                            <div class="reply-actions">
                                <button class="btn-reply-delete" @click="deleteReply({{ $reply->id }})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-comment-slash"></i>
                            <p>No replies yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="sidebar-column">
            <!-- Flags Card -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h3><i class="fas fa-flag"></i> Flags (@{{ flags.length }})</h3>
                </div>
                <div class="card-body">
                    <div v-for="flag in flags" :key="flag.id" class="flag-item">
                        <div class="flag-content">
                            <div class="flag-header">
                                <span class="flag-reporter">
                                    <i class="fas fa-user"></i> @{{ flag.reporter_name }}
                                </span>
                                <span class="flag-status" :class="flag.status">@{{ flag.status.charAt(0).toUpperCase() + flag.status.slice(1) }}</span>
                            </div>
                            <div class="flag-details">
                                <span class="flag-reason">@{{ flag.reason }}</span>
                                <span class="flag-date">@{{ flag.created_at_diff }}</span>
                            </div>
                        </div>
                        <div v-if="flag.status === 'pending'" class="flag-actions">
                            <button class="btn-resolve" @click="updateFlag(flag.id, 'resolved')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-dismiss" @click="updateFlag(flag.id, 'dismissed')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div v-if="flags.length === 0" class="empty-state small">
                        <i class="fas fa-flag"></i>
                        <p>No flags reported</p>
                    </div>
                </div>
            </div>

            <!-- Author Info Card -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle"></i> Author Details</h3>
                </div>
                <div class="card-body">
                    <div class="author-details">
                        <div class="detail-item">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value">{{ $post->user->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">{{ $post->user->email ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Role:</span>
                            <span class="detail-value role-badge {{ $post->user->hasRole('lecturer') ? 'lecturer' : 'student' }}">
                                {{ $post->user->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Joined:</span>
                            <span class="detail-value">{{ $post->user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    @if($post->user->hasRole('student'))
                        <div class="restriction-controls">
                            <h4>Forum Access</h4>
                            <template v-if="post.user.forum_restricted_until">
                                <p class="restricted-info">
                                    <i class="fas fa-ban"></i> Restricted until @{{ formatDate(post.user.forum_restricted_until) }}
                                </p>
                                <button class="btn-unrestrict" @click="unrestrictUser({{ $post->user->id }})">
                                    <i class="fas fa-unlock"></i> Remove Restriction
                                </button>
                            </template>
                            <template v-else>
                                <div class="restrict-form">
                                    <input type="number" v-model="restrictionDays" placeholder="Days" min="1" max="365">
                                    <button class="btn-restrict" @click="restrictUser({{ $post->user->id }})">
                                        <i class="fas fa-ban"></i> Restrict
                                    </button>
                                </div>
                            </template>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Card -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Post Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="stats-list">
                        <div class="stat-item">
                            <span class="stat-label">Created:</span>
                            <span class="stat-value">{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Last Activity:</span>
                            <span class="stat-value">{{ $post->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Views:</span>
                            <span class="stat-value">{{ number_format($post->views) }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Replies:</span>
                            <span class="stat-value">{{ $post->replies->count() }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Flags:</span>
                            <span class="stat-value">{{ $flags->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i></div>
        <h3 class="modal-title">Delete Post</h3>
        <p class="modal-message">Are you sure you want to delete this post? This action cannot be undone and will also delete all replies and flags.</p>
        <div class="modal-actions">
            <button id="cancelDelete" class="modal-btn modal-btn-secondary">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn modal-btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        window.postData = @json($post);
        window.flagsData = @json($flags);
        window.csrfToken = '{{ csrf_token() }}';
        window.togglePinUrl = '{{ route('admin.forum.toggle-pin', ['post' => '__POST_ID__']) }}';
        window.toggleAnnouncementUrl = '{{ route('admin.forum.toggle-announcement', ['post' => '__POST_ID__']) }}';
        window.deletePostUrl = '{{ route('admin.forum.destroy', ['post' => '__POST_ID__']) }}';
        window.updateFlagUrl = '{{ route('admin.flags.update', ['flag' => '__FLAG_ID__']) }}';
        window.deleteReplyUrl = '{{ route('admin.forum.delete-reply', ['reply' => '__REPLY_ID__']) }}';
        window.restrictUserUrl = '{{ route('admin.users.restrict', ['user' => '__USER_ID__']) }}';
        window.unrestrictUserUrl = '{{ route('admin.users.unrestrict', ['user' => '__USER_ID__']) }}';
    </script>
    <script src="{{ asset('js/forum/common.js') }}"></script>
    <script src="{{ asset('js/admin/forum.js') }}"></script>
    <script src="{{ asset('js/admin/forum-show.js') }}"></script>
@endpush
@endsection