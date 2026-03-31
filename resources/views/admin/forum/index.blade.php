@extends('admin.layouts.master')

@section('title', 'Forum Management')
@section('page-icon', 'fa-comments')
@section('page-title', 'Forum Management')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Forum</li>
        </ol>
    </nav>
@endsection

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-icon.posts { background: #dbeafe; color: #2563eb; }
        .stat-icon.replies { background: #dcfce7; color: #16a34a; }
        .stat-icon.flagged { background: #fee2e2; color: #dc2626; }
        .stat-icon.pinned { background: #fef3c7; color: #d97706; }
        .stat-info h4 { font-size: 0.8rem; color: #6b7280; margin-bottom: 5px; }
        .stat-value { font-size: 1.8rem; font-weight: bold; color: #1f2937; }
        .quick-actions { display: flex; gap: 12px; margin-bottom: 25px; flex-wrap: wrap; }
        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-moderation { background: #6366f1; color: white; }
        .btn-flags { background: #ef4444; color: white; }
        .btn-export { background: #10b981; color: white; }
        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filters-section {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            align-items: end;
        }
        .filter-group label {
            font-size: 0.7rem;
            color: #6b7280;
            display: block;
            margin-bottom: 5px;
        }
        .filter-select, .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
        }
        .btn-filter, .btn-clear {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-filter { background: #1f2937; color: white; }
        .btn-clear { background: #e5e7eb; color: #374151; }
        .posts-container { display: flex; flex-direction: column; gap: 20px; }
        .post-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }
        .post-card.pinned { border-left: 4px solid #f59e0b; }
        .post-card.announcement { border-left: 4px solid #3b82f6; background: #f8fafc; }
        .post-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.6rem;
            margin-bottom: 10px;
        }
        .badge-pinned { background: #fef3c7; color: #b45309; }
        .badge-announcement { background: #dbeafe; color: #1e40af; }
        .badge-flagged { background: #fee2e2; color: #dc2626; }
        .post-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .post-author { display: flex; gap: 12px; align-items: center; }
        .author-avatar {
            width: 44px;
            height: 44px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .author-name { font-weight: 600; color: #111827; }
        .role-badge { font-size: 0.6rem; padding: 2px 8px; border-radius: 12px; margin-left: 6px; }
        .role-badge.lecturer { background: #dbeafe; color: #1e40af; }
        .role-badge.admin { background: #fee2e2; color: #991b1b; }
        .role-badge.student { background: #dcfce7; color: #166534; }
        .unit-badge { background: #f3f4f6; padding: 2px 8px; border-radius: 12px; font-size: 0.65rem; }
        .post-date { font-size: 0.65rem; color: #6b7280; margin-left: 10px; }
        .post-content-full { margin: 15px 0; line-height: 1.5; color: #374151; }
        .link-preview-subtle {
            margin-top: 8px;
            padding: 6px 12px;
            background: #f3f4f6;
            border-radius: 8px;
            display: inline-block;
        }
        .replies-section { margin-top: 20px; padding-left: 30px; }
        .reply-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
        }
        .reply-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .reply-author { display: flex; gap: 10px; align-items: center; }
        .reply-avatar {
            width: 32px;
            height: 32px;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reply-name { font-weight: 600; font-size: 0.8rem; }
        .reply-time { font-size: 0.6rem; color: #6b7280; margin-left: 8px; }
        .reply-content { margin-left: 42px; font-size: 0.85rem; }
        .btn-reply-trigger {
            background: none;
            border: none;
            color: #3b82f6;
            font-size: 0.75rem;
            cursor: pointer;
            margin-top: 10px;
        }
        .reply-form {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
            border: 1px solid #e5e7eb;
        }
        .reply-input { width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .reply-form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px; }
        .btn-submit-reply { background: #3b82f6; color: white; border: none; padding: 6px 16px; border-radius: 6px; cursor: pointer; }
        .btn-cancel-reply { background: #e5e7eb; border: none; padding: 6px 16px; border-radius: 6px; cursor: pointer; }
        .post-footer { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e7eb; }
        .post-stats { display: flex; gap: 15px; font-size: 0.7rem; color: #6b7280; }
        .post-actions { display: flex; gap: 10px; }
        .empty-state { text-align: center; padding: 60px; background: #f9fafb; border-radius: 16px; }
        .pagination-wrapper { margin-top: 30px; text-align: center; }
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
        }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .modal-btn { padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; }
        .modal-btn-danger { background: #dc2626; color: white; }
        .modal-btn-secondary { background: #e5e7eb; color: #374151; }
    </style>
@endpush

@section('content')
<div class="forum-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon posts"><i class="fas fa-comments"></i></div>
            <div class="stat-info">
                <h4>Total Posts</h4>
                <div class="stat-value">{{ \App\Models\ForumPost::count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon replies"><i class="fas fa-reply"></i></div>
            <div class="stat-info">
                <h4>Total Replies</h4>
                <div class="stat-value">{{ \App\Models\ForumReply::count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon flagged"><i class="fas fa-flag"></i></div>
            <div class="stat-info">
                <h4>Pending Flags</h4>
                <div class="stat-value">{{ \App\Models\Flag::where('status', 'pending')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pinned"><i class="fas fa-thumbtack"></i></div>
            <div class="stat-info">
                <h4>Pinned Posts</h4>
                <div class="stat-value">{{ \App\Models\ForumPost::where('is_pinned', true)->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('admin.flags.index') }}" class="btn-action btn-moderation"><i class="fas fa-shield-alt"></i> Moderation Dashboard</a>
        <a href="{{ route('admin.flags.index') }}" class="btn-action btn-flags"><i class="fas fa-flag"></i> Manage Flags</a>
        <a href="{{ route('admin.forum.index') }}?status=flagged" class="btn-action btn-flags"><i class="fas fa-exclamation-triangle"></i> View Flagged Posts</a>
    </div>

    @if(session('success'))
        <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.forum.index') }}" class="filters-form">
            <div class="filter-group">
                <label>Unit</label>
                <select name="unit" class="filter-select">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ ($filters['unit'] ?? '') == $unit->code ? 'selected' : '' }}>{{ $unit->code }} - {{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Author</label>
                <select name="user" class="filter-select">
                    <option value="">All Authors</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ ($filters['user'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Posts</option>
                    <option value="flagged" {{ ($filters['status'] ?? '') == 'flagged' ? 'selected' : '' }}>Flagged</option>
                    <option value="pinned" {{ ($filters['status'] ?? '') == 'pinned' ? 'selected' : '' }}>Pinned</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Search posts..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Apply</button>
                <a href="{{ route('admin.forum.index') }}" class="btn-clear">Clear</a>
            </div>
        </form>
    </div>

    <!-- Posts List -->
    <div class="posts-container">
        @forelse($posts as $post)
            <div class="post-card @if($post->is_pinned) pinned @endif @if($post->is_announcement) announcement @endif">
                @if($post->is_pinned)<span class="post-badge badge-pinned"><i class="fas fa-thumbtack"></i> Pinned</span>@endif
                @if($post->is_announcement)<span class="post-badge badge-announcement"><i class="fas fa-bullhorn"></i> Announcement</span>@endif

                <div class="post-header">
                    <div class="post-author">
                        <div class="author-avatar">{{ substr($post->user->name ?? 'U', 0, 1) }}</div>
                        <div>
                            <div class="author-name">
                                {{ $post->user->name ?? 'Unknown' }}
                                @if($post->user->hasRole('admin'))<span class="role-badge admin">Admin</span>@endif
                                @if($post->user->hasRole('lecturer'))<span class="role-badge lecturer">Lecturer</span>@endif
                            </div>
                            <div><span class="unit-badge">{{ $post->unit_code }}</span><span class="post-date">{{ $post->created_at->diffForHumans() }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="post-content-full">
                    {{ Str::limit(strip_tags($post->content), 300) }}
                </div>

                <div class="post-footer">
                    <div class="post-stats">
                        <span><i class="fas fa-eye"></i> {{ number_format($post->views) }} views</span>
                        <span><i class="fas fa-comment"></i> {{ $post->replies_count }} replies</span>
                    </div>
                    <div class="post-actions">
                        <button class="btn-action btn-delete" onclick="confirmDelete({{ $post->id }})"><i class="fas fa-trash"></i> Delete</button>
                    </div>
                </div>

                <!-- Replies Section -->
                <div class="replies-section">
                    @foreach($post->replies as $reply)
                        <div class="reply-card">
                            <div class="reply-header">
                                <div class="reply-author">
                                    <div class="reply-avatar">{{ substr($reply->user->name ?? 'U', 0, 1) }}</div>
                                    <div>
                                        <div class="reply-name">{{ $reply->user->name ?? 'Unknown' }}</div>
                                        <div class="reply-time">{{ $reply->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="reply-content">{{ Str::limit(strip_tags($reply->content), 150) }}</div>
                        </div>
                    @endforeach

                    <button class="btn-reply-trigger" onclick="toggleReplyForm({{ $post->id }})">
                        <i class="fas fa-reply"></i> Add Reply
                    </button>

                    <div class="reply-form" id="reply-form-{{ $post->id }}" style="display: none;">
                        <textarea id="reply-content-{{ $post->id }}" class="reply-input" placeholder="Write a reply..." rows="3"></textarea>
                        <div class="reply-form-actions">
                            <button class="btn-cancel-reply" onclick="toggleReplyForm({{ $post->id }})">Cancel</button>
                            <button class="btn-submit-reply" onclick="submitReply({{ $post->id }})">Post Reply</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state"><i class="fas fa-comments"></i><h3>No Posts Found</h3><p>There are no forum posts matching your criteria.</p></div>
        @endforelse
    </div>

    @if($posts->hasPages())
    <div class="pagination-wrapper">{{ $posts->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Delete Post</h3>
        <p>Are you sure you want to delete this post? This action cannot be undone.</p>
        <div class="modal-actions">
            <button id="cancelDelete" class="modal-btn modal-btn-secondary">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn modal-btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.csrfToken = '{{ csrf_token() }}';
    window.deleteUrl = '{{ route('admin.forum.destroy', ['post' => '__POST_ID__']) }}';
    window.replyUrl = '{{ route('admin.forum.reply', ['post' => '__POST_ID__']) }}';

    function toggleReplyForm(postId) {
        const form = document.getElementById(`reply-form-${postId}`);
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    }

    function submitReply(postId) {
        const content = document.getElementById(`reply-content-${postId}`).value.trim();
        if (!content) {
            alert('Please enter a reply.');
            return;
        }

        const url = window.replyUrl.replace('__POST_ID__', postId);
        const formData = new FormData();
        formData.append('content', content);

        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to post reply.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    function confirmDelete(postId) {
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        if (modal && deleteForm) {
            deleteForm.action = window.deleteUrl.replace('__POST_ID__', postId);
            modal.style.display = 'flex';
        }
    }

    document.getElementById('cancelDelete')?.addEventListener('click', function() {
        document.getElementById('deleteModal').style.display = 'none';
    });

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('deleteModal');
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>
@endpush
@endsection