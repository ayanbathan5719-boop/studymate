@extends('student.layouts.master')

@section('title', 'Forum')
@section('page-icon', 'fa-comments')
@section('page-title', 'Forum Discussions')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Forum</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    /* Clean Forum Styles */
    .forum-container { max-width: 900px; margin: 0 auto; padding: 20px; }
    .forum-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; }
    .forum-header h1 { font-size: 1.5rem; color: #111827; }
    .btn-primary { background: #1e40af; color: white; padding: 8px 18px; border-radius: 9999px; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; transition: all 0.2s; }
    .btn-primary:hover { background: #1e3a8a; transform: translateY(-1px); }
    .alert-success { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px; }
    
    /* Filters */
    .filters-section { background: #f9fafb; padding: 12px 16px; border-radius: 16px; margin-bottom: 20px; }
    .filters-form { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { flex: 1; min-width: 140px; }
    .filter-group label { font-size: 0.7rem; color: #6b7280; display: block; margin-bottom: 4px; }
    .filter-select, .filter-input { width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 0.8rem; }
    .btn-filter { background: #111827; color: white; border: none; padding: 8px 18px; border-radius: 9999px; cursor: pointer; transition: all 0.2s; }
    .btn-filter:hover { background: #1f2937; }
    
    /* Create Post */
    .create-post-card { background: white; border-radius: 20px; padding: 16px; margin-bottom: 25px; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .unit-select { width: 100%; max-width: 280px; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 12px; font-size: 0.85rem; }
    .create-post-header { display: flex; gap: 12px; }
    .author-avatar { width: 44px; height: 44px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0; }
    .author-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .create-post-input-area { flex: 1; }
    .create-post-input { width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 24px; font-size: 0.9rem; background: #f9fafb; font-family: inherit; transition: all 0.2s; }
    .create-post-input:focus { outline: none; border-color: #1e40af; background: white; box-shadow: 0 0 0 3px rgba(30,64,175,0.1); }
    .create-post-actions { margin-top: 12px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .attachment-options { display: flex; gap: 12px; }
    .attach-file-btn, .attach-link-btn { background: #f3f4f6; border: none; color: #4b5563; cursor: pointer; font-size: 0.75rem; padding: 6px 14px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
    .attach-file-btn:hover, .attach-link-btn:hover { background: #e5e7eb; color: #1e40af; }
    .post-options { display: flex; gap: 15px; }
    .option-checkbox { display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #6b7280; cursor: pointer; }
    .post-buttons { display: flex; gap: 8px; }
    .btn-cancel-post { background: #f3f4f6; border: none; padding: 6px 16px; border-radius: 9999px; cursor: pointer; font-size: 0.75rem; transition: all 0.2s; }
    .btn-cancel-post:hover { background: #e5e7eb; }
    .btn-post { background: #1e40af; color: white; border: none; padding: 6px 20px; border-radius: 9999px; cursor: pointer; font-size: 0.75rem; transition: all 0.2s; }
    .btn-post:hover { background: #1e3a8a; }
    .file-upload-area, .link-upload-area { margin-top: 12px; padding: 12px; background: #f9fafb; border-radius: 16px; border: 1px dashed #d1d5db; }
    .selected-file-preview { margin-top: 8px; padding: 6px 10px; background: #f3f4f6; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 0.75rem; }
    
    /* My Resources Area - BEAUTIFIED */
    .my-resources-area {
        margin-top: 12px;
        padding: 16px;
        background: #f9fafb;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
    }
    .resources-list {
        max-height: 200px;
        overflow-y: auto;
        margin-bottom: 12px;
    }
    .resource-item {
        padding: 10px 12px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .resource-item:hover {
        background: #e5e7eb;
    }
    .resource-item.selected {
        background: #dbeafe;
    }
    .btn-cancel-resources {
        background: #f3f4f6;
        border: none;
        padding: 6px 16px;
        border-radius: 9999px;
        cursor: pointer;
        font-size: 0.7rem;
        transition: all 0.2s;
    }
    .btn-cancel-resources:hover { background: #e5e7eb; }
    .loading-resources {
        color: #6b7280;
        padding: 20px;
        text-align: center;
    }
    
    /* Link Input Area - BEAUTIFIED */
    .link-input-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }
    .link-input-field {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 4px 12px;
        transition: all 0.2s;
    }
    .link-input-field:focus-within {
        border-color: #1e40af;
        box-shadow: 0 0 0 2px rgba(30,64,175,0.1);
    }
    .link-input-field i {
        color: #9ca3af;
        font-size: 0.9rem;
    }
    .link-input-field input {
        flex: 1;
        border: none;
        padding: 10px 0;
        font-size: 0.85rem;
        outline: none;
        background: transparent;
    }
    .btn-add-link {
        background: #1e40af;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 9999px;
        cursor: pointer;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        width: fit-content;
    }
    .btn-add-link:hover { background: #1e3a8a; }
    
    /* Post Card */
    .posts-container { display: flex; flex-direction: column; gap: 16px; }
    .post-card { background: white; border-radius: 20px; padding: 18px; border: 1px solid #e5e7eb; transition: box-shadow 0.2s; }
    .post-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .post-card.pinned { border-left: 4px solid #f59e0b; }
    .post-card.announcement { border-left: 4px solid #1e40af; background: #f8fafc; }
    .post-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.6rem; margin-bottom: 10px; }
    .badge-pinned { background: #fef3c7; color: #b45309; }
    .badge-announcement { background: #dbeafe; color: #1e40af; }
    .post-header { display: flex; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 10px; }
    .post-author { display: flex; gap: 12px; align-items: center; }
    .author-info { display: flex; flex-direction: column; }
    .author-name { font-weight: 600; color: #111827; font-size: 0.9rem; display: flex; align-items: center; gap: 6px; }
    .role-badge { font-size: 0.6rem; padding: 2px 8px; border-radius: 12px; }
    .role-badge.lecturer { background: #dbeafe; color: #1e40af; }
    .post-date { font-size: 0.65rem; color: #6b7280; }
    .unit-badge { background: #f3f4f6; padding: 2px 8px; border-radius: 12px; font-size: 0.65rem; }
    .post-content { margin: 12px 0; line-height: 1.5; color: #374151; font-size: 0.9rem; }
    .post-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #f3f4f6; margin-top: 12px; }
    .post-stats { display: flex; gap: 15px; font-size: 0.7rem; color: #6b7280; }
    .post-actions { display: flex; gap: 8px; }
    .btn-edit, .btn-delete, .btn-flag { background: none; border: none; font-size: 0.7rem; cursor: pointer; padding: 4px 8px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; }
    .btn-edit { color: #059669; }
    .btn-edit:hover { background: #ecfdf5; }
    .btn-delete { color: #dc2626; }
    .btn-delete:hover { background: #fef2f2; }
    .btn-flag { color: #6b7280; }
    .btn-flag:hover { background: #f3f4f6; }
    
    /* Replies */
    .replies-section { margin-top: 15px; padding-left: 44px; }
    .reply-card { background: #f9fafb; border-radius: 16px; padding: 12px; margin-bottom: 12px; transition: background 0.2s; }
    .reply-card:hover { background: #f3f4f6; }
    .reply-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .reply-author { display: flex; gap: 10px; align-items: center; }
    .reply-avatar { width: 32px; height: 32px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }
    .reply-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .reply-name { font-weight: 600; font-size: 0.8rem; color: #111827; }
    .reply-time { font-size: 0.6rem; color: #6b7280; }
    .reply-content { margin-left: 42px; font-size: 0.85rem; color: #374151; }
    .reply-actions { display: flex; gap: 8px; }
    .reply-actions button { background: none; border: none; font-size: 0.65rem; cursor: pointer; color: #6b7280; padding: 4px 8px; border-radius: 6px; transition: all 0.2s; }
    .reply-actions button:hover { background: #e5e7eb; color: #dc2626; }
    .btn-reply-trigger { background: none; border: none; color: #1e40af; font-size: 0.75rem; padding: 6px 0; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; margin-top: 8px; transition: all 0.2s; }
    .btn-reply-trigger:hover { color: #3b82f6; }
    .reply-form { display: none; margin-top: 12px; padding: 16px; background: white; border-radius: 16px; border: 1px solid #e5e7eb; }
    .reply-form-header { display: flex; gap: 10px; margin-bottom: 10px; }
    .reply-avatar-small { width: 28px; height: 28px; background: #1e40af; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 11px; }
    .reply-input { width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 20px; resize: vertical; font-family: inherit; font-size: 0.85rem; transition: all 0.2s; }
    .reply-input:focus { outline: none; border-color: #1e40af; box-shadow: 0 0 0 2px rgba(30,64,175,0.1); }
    
    /* Reply Attachment Options - BEAUTIFIED */
    .reply-attachment-options {
        display: flex;
        gap: 10px;
        margin: 12px 0;
        flex-wrap: wrap;
    }
    .reply-attachment-options button {
        background: #f3f4f6;
        border: none;
        color: #4b5563;
        cursor: pointer;
        font-size: 0.7rem;
        padding: 6px 14px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .reply-attachment-options button:hover {
        background: #e5e7eb;
        color: #1e40af;
    }
    
    .reply-form-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px; }
    .btn-cancel-reply { background: #f3f4f6; border: none; padding: 6px 16px; border-radius: 9999px; cursor: pointer; font-size: 0.7rem; transition: all 0.2s; }
    .btn-cancel-reply:hover { background: #e5e7eb; }
    .btn-submit-reply { background: #1e40af; color: white; border: none; padding: 6px 20px; border-radius: 9999px; cursor: pointer; font-size: 0.7rem; transition: all 0.2s; }
    .btn-submit-reply:hover { background: #1e3a8a; }
    .btn-submit-reply:disabled { opacity: 0.6; cursor: not-allowed; }
    
    /* Attachments */
    .post-attachments, .reply-attachments { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px; }
    .attachment-item { background: #f3f4f6; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
    .attachment-item:hover { background: #e5e7eb; }
    .attachment-item a { color: #1e40af; text-decoration: none; }
    .attachment-item a:hover { text-decoration: underline; }
    
    .empty-state { text-align: center; padding: 50px; background: #f9fafb; border-radius: 20px; }
    .pagination-wrapper { margin-top: 30px; text-align: center; }
    
    .edit-form { margin-top: 10px; }
    
    /* Toast notification */
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .toast-notification {
        animation: slideIn 0.3s ease-out;
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        padding: 12px 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="forum-container">
    <div class="forum-header">
        <h1>Forum Discussions</h1>
        <button class="btn-primary" onclick="document.getElementById('createPostCard').scrollIntoView({behavior:'smooth'})">
            <i class="fas fa-plus-circle"></i> New Post
        </button>
    </div>

    @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('forum.index') }}" class="filters-form">
            <div class="filter-group">
                <label>Unit</label>
                <select name="unit" class="filter-select">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->code }}" {{ ($filters['unit'] ?? '') == $unit->code ? 'selected' : '' }}>
                        {{ $unit->code }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Search..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <button type="submit" class="btn-filter">Apply</button>
        </form>
    </div>

    <!-- Create Post -->
    <div id="createPostCard" class="create-post-card">
        <select id="postUnit" class="unit-select">
            <option value="">Select a unit</option>
            @foreach($units as $unit)
            <option value="{{ $unit->code }}">{{ $unit->code }} - {{ $unit->name }}</option>
            @endforeach
        </select>
        <div class="create-post-header">
            <div class="author-avatar">
                @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}">
                @else
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                @endif
            </div>
            <div class="create-post-input-area">
                <textarea id="postContent" class="create-post-input" placeholder="What's on your mind?" rows="2"></textarea>
                <div class="create-post-actions" id="postActions" style="display: none;">
                    <div class="attachment-options">
                        <button type="button" class="attach-file-btn" onclick="showFileUpload()"><i class="fas fa-paperclip"></i> Attach File</button>
                        <button type="button" class="attach-link-btn" onclick="showLinkUpload()"><i class="fas fa-link"></i> Add Link</button>
                    </div>
                    <div class="post-options">
                        <label class="option-checkbox"><input type="checkbox" id="isAnnouncement"> Announcement</label>
                        <label class="option-checkbox"><input type="checkbox" id="isPinned"> Pin</label>
                    </div>
                    <div class="post-buttons">
                        <button class="btn-cancel-post" onclick="cancelPost()">Cancel</button>
                        <button class="btn-post" onclick="submitPost()">Post</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="fileUploadArea" class="file-upload-area" style="display: none;">
            <input type="file" id="postFile" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.png,.mp4">
            <div id="selectedFilePreview" class="selected-file-preview" style="display: none;">
                <i class="fas fa-file"></i> <span id="selectedFileName"></span>
                <button onclick="clearSelectedFile()"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div id="linkUploadArea" class="link-upload-area" style="display: none;">
            <div class="link-input-group">
                <div class="link-input-field">
                    <i class="fas fa-link"></i>
                    <input type="url" id="postLink" placeholder="Enter URL (YouTube, article, etc.)">
                </div>
                <div class="link-input-field">
                    <i class="fas fa-heading"></i>
                    <input type="text" id="linkTitle" placeholder="Link title (optional)">
                </div>
                <button class="btn-add-link" onclick="addLink()"><i class="fas fa-plus"></i> Add Link</button>
            </div>
        </div>
    </div>

    <!-- Posts List -->
    <div class="posts-container">
        @forelse($posts as $post)
        <div class="post-card @if($post->is_pinned) pinned @endif @if($post->is_announcement) announcement @endif" data-post-id="{{ $post->id }}">
            @if($post->is_pinned)<span class="post-badge badge-pinned"><i class="fas fa-thumbtack"></i> Pinned</span>@endif
            @if($post->is_announcement)<span class="post-badge badge-announcement"><i class="fas fa-bullhorn"></i> Announcement</span>@endif

            <div class="post-header">
                <div class="post-author">
                    <div class="author-avatar">
                        @if($post->user->avatar)<img src="{{ asset('storage/' . $post->user->avatar) }}">
                        @else{{ substr($post->user->name ?? 'U', 0, 1) }}@endif
                    </div>
                    <div class="author-info">
                        <div class="author-name">
                            {{ $post->user->name ?? 'Unknown' }}
                            @if($post->user->hasRole('lecturer'))<span class="role-badge lecturer">Lecturer</span>@endif
                        </div>
                        <div class="post-date">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <div class="unit-badge">{{ $post->unit_code }}</div>
            </div>

            <div class="post-content" id="post-content-{{ $post->id }}">
                {!! nl2br($post->content) !!}
                
                @if($post->resources && $post->resources->count() > 0)
                <div class="post-attachments">
                    @foreach($post->resources as $resource)
                    <div class="attachment-item">
                        <i class="fas fa-{{ $resource->file_type == 'link' ? 'link' : 'file-alt' }}"></i>
                        @if($resource->file_type == 'link')
                            <a href="{{ $resource->url }}" target="_blank">{{ $resource->title }}</a>
                        @else
                            <a href="{{ route('student.resources.viewer', $resource) }}" target="_blank">{{ $resource->title }}</a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="post-footer">
                <div class="post-stats">
                    <span><i class="far fa-eye"></i> {{ number_format($post->views) }}</span>
                    <span><i class="far fa-comment"></i> {{ number_format($post->replies_count) }}</span>
                </div>
                <div class="post-actions">
                    @if(Auth::id() == $post->user_id)
                    <button class="btn-edit" onclick="editPost({{ $post->id }})"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn-delete" onclick="deletePost({{ $post->id }})"><i class="fas fa-trash"></i> Delete</button>
                    @endif
                    @if(Auth::id() != $post->user_id)
                    <button class="btn-flag" onclick="flagPost({{ $post->id }})"><i class="fas fa-flag"></i> Flag</button>
                    @endif
                </div>
            </div>

            <!-- Replies Section -->
            <div class="replies-section">
                @foreach($post->replies as $reply)
                <div class="reply-card" id="reply-{{ $reply->id }}">
                    <div class="reply-header">
                        <div class="reply-author">
                            <div class="reply-avatar">
                                @if($reply->user->avatar)<img src="{{ asset('storage/' . $reply->user->avatar) }}">
                                @else{{ substr($reply->user->name ?? 'U', 0, 1) }}@endif
                            </div>
                            <div>
                                <div class="reply-name">{{ $reply->user->name ?? 'Unknown' }}</div>
                                <div class="reply-time">{{ $reply->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="reply-actions">
                            @if(Auth::id() == $reply->user_id)
                            <button onclick="editReply({{ $reply->id }})"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteReply({{ $reply->id }}, {{ $reply->user_id }})"><i class="fas fa-trash"></i></button>
                            @endif
                        </div>
                    </div>
                    <div class="reply-content" id="reply-content-{{ $reply->id }}">
                        {!! nl2br($reply->content) !!}
                        
                        @if($reply->resources && $reply->resources->count() > 0)
                        <div class="reply-attachments">
                            @foreach($reply->resources as $resource)
                            <div class="attachment-item">
                                <i class="fas fa-{{ $resource->file_type == 'link' ? 'link' : 'file-alt' }}"></i>
                                @if($resource->file_type == 'link')
                                    <a href="{{ $resource->url }}" target="_blank">{{ $resource->title }}</a>
                                @else
                                    <a href="{{ route('student.resources.viewer', $resource) }}" target="_blank">{{ $resource->title }}</a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                <button class="btn-reply-trigger" onclick="toggleReplyForm({{ $post->id }})">
                    <i class="fas fa-reply"></i> Reply
                </button>

                <div class="reply-form" id="reply-form-{{ $post->id }}">
                    <div class="reply-form-header">
                        <div class="reply-avatar-small">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                        <textarea id="reply-content-{{ $post->id }}" class="reply-input" placeholder="Write a reply..." rows="2"></textarea>
                    </div>
                    <div class="reply-attachment-options">
                        <button type="button" onclick="showReplyFileUpload({{ $post->id }})"><i class="fas fa-paperclip"></i> Upload File</button>
                        <button type="button" onclick="showMyResources({{ $post->id }}, '{{ $post->unit_code }}')"><i class="fas fa-folder-open"></i> My Resources</button>
                        <button type="button" onclick="showReplyLinkUpload({{ $post->id }})"><i class="fas fa-link"></i> Add Link</button>
                    </div>
                    
                    <!-- My Resources Selection Area -->
                    <div id="my-resources-{{ $post->id }}" class="my-resources-area" style="display: none;">
                        <div class="resources-list" id="resources-list-{{ $post->id }}">
                            <div class="loading-resources"><i class="fas fa-spinner fa-spin"></i> Loading your resources...</div>
                        </div>
                        <button class="btn-cancel-resources" onclick="cancelMyResources({{ $post->id }})">Cancel</button>
                    </div>
                    
                    <div id="reply-file-upload-{{ $post->id }}" class="file-upload-area" style="display: none;">
                        <input type="file" id="reply-file-{{ $post->id }}" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.png">
                        <div id="reply-file-preview-{{ $post->id }}" class="selected-file-preview" style="display: none;">
                            <i class="fas fa-file"></i> <span id="reply-file-name-{{ $post->id }}"></span>
                            <button onclick="clearReplyFile({{ $post->id }})"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    
                    <!-- BEAUTIFIED Link Upload Area -->
                    <div id="reply-link-upload-{{ $post->id }}" class="link-upload-area" style="display: none;">
                        <div class="link-input-group">
                            <div class="link-input-field">
                                <i class="fas fa-link"></i>
                                <input type="url" id="reply-link-url-{{ $post->id }}" placeholder="Enter URL (YouTube, article, etc.)">
                            </div>
                            <div class="link-input-field">
                                <i class="fas fa-heading"></i>
                                <input type="text" id="reply-link-title-{{ $post->id }}" placeholder="Link title (optional)">
                            </div>
                            <button class="btn-add-link" onclick="addReplyLink({{ $post->id }})"><i class="fas fa-plus"></i> Add Link</button>
                        </div>
                    </div>
                    
                    <div class="reply-form-actions">
                        <button class="btn-cancel-reply" onclick="toggleReplyForm({{ $post->id }})">Cancel</button>
                        <button class="btn-submit-reply" onclick="submitReply({{ $post->id }})">Post Reply</button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state"><i class="fas fa-comments"></i><h3>No posts yet</h3><p>Be the first to start a discussion!</p></div>
        @endforelse
    </div>

    <div class="pagination-wrapper">{{ $posts->withQueryString()->links() }}</div>
</div>

@push('scripts')
<script>
    window.currentUserId = {{ Auth::id() }};
    window.csrfToken = '{{ csrf_token() }}';
    window.forumStoreUrl = '{{ route('forum.store') }}';
    window.deletePostUrl = '{{ route('forum.delete', ['post' => '__POST_ID__']) }}';
    window.editPostUrl = '{{ route('forum.edit', ['post' => '__POST_ID__']) }}';
    window.editReplyUrl = '{{ route('forum.edit-reply', ['reply' => '__REPLY_ID__']) }}';
    
    let selectedFile = null;
    let selectedLink = null;
    let replySelectedFile = {};
    let replySelectedLink = {};
    let selectedResourceId = null;
    
    // Toast notification function
    function showNotification(message, type = 'success') {
        const existingNotifications = document.querySelectorAll('.toast-notification');
        existingNotifications.forEach(notif => notif.remove());
        
        const colors = {
            success: { bg: '#10b981', icon: 'fa-check-circle' },
            error: { bg: '#ef4444', icon: 'fa-exclamation-circle' },
            info: { bg: '#3b82f6', icon: 'fa-info-circle' }
        };
        const color = colors[type] || colors.success;
        
        const notification = document.createElement('div');
        notification.className = 'toast-notification';
        notification.style.cssText = `background: ${color.bg}; color: white;`;
        notification.innerHTML = `<i class="fas ${color.icon}"></i><span>${message}</span>`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification && notification.remove) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                notification.style.transition = 'all 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }
        }, 3000);
    }
    
    // Post functions
    function showFileUpload() { $('#fileUploadArea').toggle(); $('#linkUploadArea').hide(); if($('#fileUploadArea').is(':visible')) $('#postFile').click(); }
    function showLinkUpload() { $('#linkUploadArea').toggle(); $('#fileUploadArea').hide(); }
    function clearSelectedFile() { selectedFile = null; $('#postFile').val(''); $('#selectedFilePreview').hide(); }
    function addLink() {
        let url = $('#postLink').val(), title = $('#linkTitle').val();
        if(url) { selectedLink = { url, title: title || url }; $('#linkUploadArea').hide(); showNotification('Link added', 'info'); $('#postLink, #linkTitle').val(''); }
        else showNotification('Enter a URL', 'error');
    }
    function cancelPost() { $('#postContent').val(''); $('#postActions').hide(); selectedFile = null; selectedLink = null; clearSelectedFile(); $('#linkUploadArea, #fileUploadArea').hide(); }
    
    function submitPost() {
        let content = $('#postContent').val().trim();
        let unitCode = $('#postUnit').val();
        if(!unitCode) { showNotification('Select a unit', 'error'); return; }
        if(!content) { showNotification('Write a message', 'error'); return; }
        
        let formData = new FormData();
        formData.append('content', content);
        formData.append('unit_code', unitCode);
        formData.append('is_announcement', $('#isAnnouncement').is(':checked') ? 1 : 0);
        formData.append('is_pinned', $('#isPinned').is(':checked') ? 1 : 0);
        if(selectedFile) formData.append('attachment', selectedFile);
        if(selectedLink) { formData.append('link_url', selectedLink.url); formData.append('link_title', selectedLink.title); }
        
        $.ajax({ url: window.forumStoreUrl, method: 'POST', headers: { 'X-CSRF-TOKEN': window.csrfToken }, data: formData, processData: false, contentType: false,
            success: function(res) { if(res.success) location.reload(); else showNotification(res.message || 'Error', 'error'); },
            error: () => showNotification('Error creating post', 'error')
        });
    }
    
    $('#postFile').on('change', function(e) {
        let file = e.target.files[0];
        if(file) { selectedFile = file; $('#selectedFileName').text(file.name); $('#selectedFilePreview').show(); }
    });
    
    // Reply functions
    function toggleReplyForm(postId) { $('#reply-form-' + postId).toggle(); }
    
    function showReplyFileUpload(postId) { 
        $('#reply-file-upload-' + postId).toggle(); 
        $('#reply-link-upload-' + postId).hide();
        $('#my-resources-' + postId).hide();
        if($('#reply-file-upload-' + postId).is(':visible')) $('#reply-file-' + postId).click();
    }
    
    function showReplyLinkUpload(postId) {
        $('#reply-link-upload-' + postId).toggle();
        $('#reply-file-upload-' + postId).hide();
        $('#my-resources-' + postId).hide();
    }
    
    // FIXED showMyResources function - CORRECT URL based on routes/web.php line 342
    function showMyResources(postId, unitCode) {
        $('#my-resources-' + postId).show();
        $('#reply-file-upload-' + postId).hide();
        $('#reply-link-upload-' + postId).hide();
        
        // Show loading state
        $('#resources-list-' + postId).html('<div class="loading-resources"><i class="fas fa-spinner fa-spin"></i> Loading your resources...</div>');
        
        // CORRECT URL from routes/web.php line 342
        // Route: Route::get('/unit-resources/{unitCode}', [ForumController::class, 'getUnitResources'])
        let url = `/forum/unit-resources/${unitCode}`;
        
        console.log('Fetching resources from:', url);
        console.log('Unit Code:', unitCode);
        
        $.ajax({
            url: url,
            method: 'GET',
            headers: { 
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            success: function(data) {
                console.log('Resources response:', data);
                if (data.success && data.resources && data.resources.length > 0) {
                    let html = '<div style="font-size:0.75rem; color:#6b7280; margin-bottom:8px;"><i class="fas fa-database"></i> Select a resource to attach:</div>';
                    data.resources.forEach(resource => {
                        let icon = resource.file_type == 'link' ? 'fa-link' : 'fa-file-alt';
                        let fileInfo = resource.file_type == 'link' ? (resource.url || 'link') : (resource.file_name || resource.title);
                        html += `<div class="resource-item" onclick="selectResource(${postId}, ${resource.id}, '${escapeResourceTitle(resource.title)}')">
                            <i class="fas ${icon}"></i>
                            <span><strong>${escapeHtml(resource.title)}</strong></span>
                            <small style="margin-left:auto; color:#9ca3af; font-size:0.65rem;">${escapeHtml(fileInfo.substring(0, 30))}</small>
                        </div>`;
                    });
                    $('#resources-list-' + postId).html(html);
                } else {
                    $('#resources-list-' + postId).html('<div style="color:#6b7280; text-align:center; padding:20px;"><i class="fas fa-folder-open fa-2x mb-2" style="display:block; margin-bottom:10px;"></i>No resources found.<br>Upload a file or add a link first in your Resources section.</div>');
                }
            },
            error: function(xhr) {
                console.error('Error loading resources:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                let errorMsg = 'Error loading resources.';
                if (xhr.status === 404) {
                    errorMsg = 'Resource endpoint not found. Please contact support.';
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error. Please try again.';
                } else if (xhr.status === 403) {
                    errorMsg = 'You don\'t have permission to access these resources.';
                }
                $('#resources-list-' + postId).html(`<div style="color:#ef4444; text-align:center; padding:20px;"><i class="fas fa-exclamation-circle fa-2x" style="display:block; margin-bottom:10px;"></i>${errorMsg}<br>Please try again later.</div>`);
            }
        });
    }
    
    function selectResource(postId, resourceId, title) {
        selectedResourceId = resourceId;
        $('#my-resources-' + postId).hide();
        showNotification('✓ Selected: ' + title.substring(0, 50), 'info');
    }
    
    function cancelMyResources(postId) {
        $('#my-resources-' + postId).hide();
        selectedResourceId = null;
    }
    
    function clearReplyFile(postId) { delete replySelectedFile[postId]; $('#reply-file-' + postId).val(''); $('#reply-file-preview-' + postId).hide(); }
    
    function addReplyLink(postId) {
        let url = $('#reply-link-url-' + postId).val();
        let title = $('#reply-link-title-' + postId).val();
        if(url) { 
            replySelectedLink[postId] = { url, title: title || url }; 
            $('#reply-link-upload-' + postId).hide(); 
            showNotification('✓ Link added', 'info'); 
            $('#reply-link-url-' + postId).val(''); 
            $('#reply-link-title-' + postId).val(''); 
        } else {
            showNotification('Please enter a URL', 'error');
        }
    }
    
    function submitReply(postId) {
        let content = $('#reply-content-' + postId).val().trim();
        if(!content) { showNotification('Please enter a reply', 'error'); return; }
        
        const submitBtn = $(`#reply-form-${postId} .btn-submit-reply`);
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        let formData = new FormData();
        formData.append('content', content);
        formData.append('_token', window.csrfToken);
        
        if(selectedResourceId) {
            formData.append('resource_id', selectedResourceId);
            formData.append('upload_source', 'my_resources');
        }
        
        if(replySelectedFile[postId]) {
            formData.append('attachment', replySelectedFile[postId]);
            formData.append('upload_source', 'file_explorer');
        }
        
        if(replySelectedLink[postId]) {
            formData.append('link_url', replySelectedLink[postId].url);
            formData.append('link_title', replySelectedLink[postId].title);
        }
        
        const url = `/forum/${postId}/reply`;
        
        console.log('Submitting reply to:', url);
        
        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken },
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) { 
                console.log('Reply response:', res);
                if(res.success) { 
                    showNotification('✓ Reply sent successfully!', 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showNotification(res.message || 'Error posting reply', 'error');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                showNotification('An error occurred. Please try again.', 'error');
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    function deleteReply(replyId, userId) {
        if(userId != window.currentUserId) {
            showNotification('You can only delete your own replies.', 'error');
            return;
        }
        if(!confirm('Delete this reply?')) return;
        
        const deleteBtn = $(`button[onclick="deleteReply(${replyId}, ${userId})"]`);
        const originalIcon = deleteBtn.html();
        deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        const url = `/forum/reply/${replyId}`;
        
        console.log('Deleting reply at:', url);
        
        $.ajax({
            url: url,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Content-Type': 'application/json' },
            success: function(res) { 
                console.log('Delete response:', res);
                if(res.success) { 
                    showNotification('✓ Reply deleted successfully!', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showNotification(res.message || 'Error', 'error');
                    deleteBtn.prop('disabled', false).html(originalIcon);
                }
            },
            error: function(xhr) {
                console.error('Delete error:', xhr);
                showNotification('Error deleting reply', 'error');
                deleteBtn.prop('disabled', false).html(originalIcon);
            }
        });
    }
    
    // Edit functions
    function editPost(postId) {
        let contentDiv = $('#post-content-' + postId);
        let original = contentDiv.clone().children('.post-attachments').remove().end().text().trim();
        let editHtml = `<div class="edit-form"><textarea id="edit-textarea-${postId}" class="reply-input" rows="4">${escapeHtml(original)}</textarea><div class="reply-form-actions"><button class="btn-cancel-reply" onclick="cancelEditPost(${postId})">Cancel</button><button class="btn-submit-reply" onclick="saveEditPost(${postId})">Save</button></div></div>`;
        contentDiv.hide().after(editHtml);
    }
    
    function cancelEditPost(postId) { $('#post-content-' + postId).show(); $('.edit-form').remove(); }
    
    function saveEditPost(postId) {
        let newContent = $('#edit-textarea-' + postId).val().trim();
        if(!newContent) { showNotification('Content cannot be empty', 'error'); return; }
        $.ajax({ url: window.editPostUrl.replace('__POST_ID__', postId), method: 'PUT', data: { content: newContent, _token: window.csrfToken }, success: function(res) { if(res.success) location.reload(); else showNotification(res.message || 'Error', 'error'); } });
    }
    
    function editReply(replyId) {
        let contentDiv = $('#reply-content-' + replyId);
        let original = contentDiv.clone().children('.reply-attachments').remove().end().text().trim();
        let editHtml = `<div class="edit-form"><textarea id="edit-textarea-${replyId}" class="reply-input" rows="3">${escapeHtml(original)}</textarea><div class="reply-form-actions"><button class="btn-cancel-reply" onclick="cancelEditReply(${replyId})">Cancel</button><button class="btn-submit-reply" onclick="saveEditReply(${replyId})">Save</button></div></div>`;
        contentDiv.hide().after(editHtml);
    }
    
    function cancelEditReply(replyId) { $('#reply-content-' + replyId).show(); $('.edit-form').remove(); }
    
    function saveEditReply(replyId) {
        let newContent = $('#edit-textarea-' + replyId).val().trim();
        if(!newContent) { showNotification('Content cannot be empty', 'error'); return; }
        $.ajax({ url: window.editReplyUrl.replace('__REPLY_ID__', replyId), method: 'PUT', data: { content: newContent, _token: window.csrfToken }, success: function(res) { if(res.success) location.reload(); else showNotification(res.message || 'Error', 'error'); } });
    }
    
    function deletePost(postId) { 
        if(confirm('Delete this post? All replies will be deleted.')) {
            $.ajax({ 
                url: window.deletePostUrl.replace('__POST_ID__', postId), 
                method: 'DELETE', 
                data: { _token: window.csrfToken }, 
                success: function(res) { 
                    if(res.success) {
                        showNotification('Post deleted successfully', 'success');
                        setTimeout(() => location.reload(), 500);
                    }
                } 
            });
        }
    }
    
    function flagPost(postId) { 
        if(confirm('Flag this post for inappropriate content?')) {
            $.post(`/forum/${postId}/flag`, { _token: window.csrfToken }, function(res) { 
                if(res.success) showNotification('Post flagged for review', 'success'); 
            });
        }
    }
    
    function escapeHtml(text) { 
        if(!text) return ''; 
        return text.replace(/[&<>]/g, function(m) { 
            if(m === '&') return '&amp;'; 
            if(m === '<') return '&lt;'; 
            if(m === '>') return '&gt;'; 
            return m; 
        }); 
    }
    
    function escapeResourceTitle(title) {
        if (!title) return '';
        return title.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }
    
    $('#postContent').on('focus', function() { $('#postActions').show(); }).on('input', function() { $('#postActions').toggle($(this).val().trim() !== ''); });
    
    $(document).on('change', '[id^="reply-file-"]', function(e) {
        let id = $(this).attr('id');
        let postId = id.replace('reply-file-', '');
        let file = e.target.files[0];
        if(file) { replySelectedFile[postId] = file; $('#reply-file-name-' + postId).text(file.name); $('#reply-file-preview-' + postId).show(); }
    });
</script>
@endpush
@endsection