@extends('lecturer.layouts.master')

@section('title', 'Forum')
@section('page-icon', 'fa-comments')
@section('page-title', 'Forum')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Forum</li>
        </ol>
    </nav>
@endsection

@section('content')
<style>
    .btn-submit-reply:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .btn-delete-reply:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Toast notification animation */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .toast-notification {
        animation: slideIn 0.3s ease-out;
    }
</style>

<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px;">
        <h1 style="font-size: 1.5rem; margin: 0;"><i class="fas fa-comments"></i> Forum Discussions</h1>
        <a href="{{ route('lecturer.forum.create') }}" style="background: #f59e0b; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
            <i class="fas fa-plus-circle"></i> New Post
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div style="background: #f9fafb; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('lecturer.forum.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.7rem; color: #6b7280;">Unit</label>
                <select name="unit" style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ ($filters['unit'] ?? '') == $unit->code ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.7rem; color: #6b7280;">Search</label>
                <input type="text" name="search" placeholder="Search posts..." value="{{ $filters['search'] ?? '' }}" style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 8px;">
            </div>
            <button type="submit" style="background: #111827; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; margin-top: 18px;">
                <i class="fas fa-filter"></i> Apply
            </button>
        </form>
    </div>

    <!-- Posts List -->
    @forelse($posts as $post)
    <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e5e7eb; margin-bottom: 20px; position: relative;">
        @if($post->is_pinned)
            <span style="position: absolute; top: 12px; right: 15px; background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 12px; font-size: 0.6rem;"><i class="fas fa-thumbtack"></i> Pinned</span>
        @endif
        @if($post->is_announcement)
            <span style="position: absolute; top: 12px; right: 15px; background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 12px; font-size: 0.6rem;"><i class="fas fa-bullhorn"></i> Announcement</span>
        @endif

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="width: 40px; height: 40px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    {{ substr($post->user->name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <div style="font-weight: 600;">
                        {{ $post->user->name ?? 'Unknown' }}
                        @if($post->user->hasRole('lecturer'))
                            <span style="font-size: 0.6rem; background: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 10px; margin-left: 6px;">Lecturer</span>
                        @endif
                    </div>
                    <div style="font-size: 0.7rem; color: #6b7280;">{{ $post->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div style="background: #f3f4f6; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem;">{{ $post->unit_code }}</div>
        </div>

        <div style="margin: 12px 0; line-height: 1.5; color: #374151;">
            {!! nl2br($post->content) !!}
        </div>

        <!-- Display Post Attachments -->
        @if($post->resources && $post->resources->count() > 0)
        <div style="margin: 10px 0; display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($post->resources as $resource)
            <div style="background: #f3f4f6; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-{{ $resource->file_type == 'link' ? 'link' : 'file-alt' }}"></i>
                @if($resource->file_type == 'link')
                    <a href="{{ $resource->url }}" target="_blank" style="color: #f59e0b; text-decoration: none;">{{ $resource->title }}</a>
                @else
                    <a href="{{ route('lecturer.resources.download', $resource->id) }}" style="color: #f59e0b; text-decoration: none;">{{ $resource->title }}</a>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #f3f4f6;">
            <div style="display: flex; gap: 15px; font-size: 0.75rem; color: #6b7280;">
                <span><i class="fas fa-eye"></i> {{ number_format($post->views) }}</span>
                <span><i class="fas fa-comment"></i> {{ number_format($post->replies_count) }}</span>
            </div>
            <button onclick="toggleReplyForm({{ $post->id }})" style="background: none; border: none; color: #f59e0b; cursor: pointer;">
                <i class="fas fa-reply"></i> Reply
            </button>
        </div>

        <!-- Replies Section -->
        <div style="margin-top: 15px; padding-left: 40px;">
            @foreach($post->replies as $reply)
            <div style="background: #f9fafb; border-radius: 12px; padding: 12px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div style="width: 28px; height: 28px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px;">
                            {{ substr($reply->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.8rem;">{{ $reply->user->name ?? 'Unknown' }}</div>
                            <div style="font-size: 0.65rem; color: #6b7280;">{{ $reply->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @if(Auth::id() == $reply->user_id)
                        <button onclick="deleteReply({{ $reply->id }}, {{ $reply->user_id }})" class="btn-delete-reply" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>
                <div style="margin-left: 38px; font-size: 0.85rem;">
                    {!! nl2br($reply->content) !!}
                </div>
                
                <!-- Display Reply Attachments -->
                @if($reply->resources && $reply->resources->count() > 0)
                <div style="margin: 8px 0 0 38px; display: flex; flex-wrap: wrap; gap: 6px;">
                    @foreach($reply->resources as $resource)
                    <div style="background: #e5e7eb; padding: 2px 10px; border-radius: 16px; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fas fa-{{ $resource->file_type == 'link' ? 'link' : 'file-alt' }}"></i>
                        @if($resource->file_type == 'link')
                            <a href="{{ $resource->url }}" target="_blank" style="color: #f59e0b; text-decoration: none;">{{ $resource->title }}</a>
                        @else
                            <a href="{{ route('lecturer.resources.download', $resource->id) }}" style="color: #f59e0b; text-decoration: none;">{{ $resource->title }}</a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach

            <!-- Reply Form with Attachment Options -->
            <div id="reply-form-{{ $post->id }}" style="display: none; margin-top: 12px; padding: 15px; background: white; border-radius: 12px; border: 1px solid #e5e7eb;">
                <textarea id="reply-content-{{ $post->id }}" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; resize: vertical; margin-bottom: 10px;" placeholder="Write a reply..."></textarea>
                
                <!-- Attachment Options -->
                <div style="display: flex; gap: 12px; margin-bottom: 12px; padding: 8px 0; border-top: 1px solid #f3f4f6; border-bottom: 1px solid #f3f4f6;">
                    <button type="button" onclick="showFileUpload({{ $post->id }})" style="background: none; border: none; color: #6b7280; cursor: pointer; font-size: 0.8rem;">
                        <i class="fas fa-paperclip"></i> File Explorer
                    </button>
                    <button type="button" onclick="showMyResources({{ $post->id }}, '{{ $post->unit_code }}')" style="background: none; border: none; color: #6b7280; cursor: pointer; font-size: 0.8rem;">
                        <i class="fas fa-folder-open"></i> My Resources
                    </button>
                </div>

                <!-- File Upload Area -->
                <div id="file-upload-{{ $post->id }}" style="display: none; margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 8px; border: 1px dashed #d1d5db;">
                    <input type="file" id="reply-file-{{ $post->id }}" style="width: 100%;">
                    <div id="file-preview-{{ $post->id }}" style="display: none; margin-top: 8px; padding: 6px; background: #f3f4f6; border-radius: 6px; font-size: 0.75rem;">
                        <i class="fas fa-file"></i> <span id="file-name-{{ $post->id }}"></span>
                        <button onclick="clearFile({{ $post->id }})" style="float: right; background: none; border: none; color: #ef4444;">×</button>
                    </div>
                </div>

                <!-- My Resources Area -->
                <div id="my-resources-{{ $post->id }}" style="display: none; margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div id="resources-list-{{ $post->id }}" style="max-height: 150px; overflow-y: auto;">
                        <div style="text-align: center; padding: 10px; color: #6b7280;">Loading resources...</div>
                    </div>
                    <button onclick="closeMyResources({{ $post->id }})" style="margin-top: 8px; background: #f3f4f6; border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer;">Cancel</button>
                </div>

                <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 10px;">
                    <button onclick="toggleReplyForm({{ $post->id }})" style="background: #f3f4f6; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button onclick="submitReply({{ $post->id }})" class="btn-submit-reply" style="background: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Post Reply</button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 50px; background: #f9fafb; border-radius: 16px;">
        <i class="fas fa-comments" style="font-size: 3rem; color: #9ca3af;"></i>
        <h3>No Posts Found</h3>
        <p>Be the first to start a discussion in your units!</p>
        <a href="{{ route('lecturer.forum.create') }}" style="background: #f59e0b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-block; margin-top: 10px;">
            Create New Post
        </a>
    </div>
    @endforelse

    <div style="margin-top: 20px; text-align: center;">
        {{ $posts->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
    window.currentUserId = {{ Auth::id() }};
    window.csrfToken = '{{ csrf_token() }}';
    
    let selectedFile = {};
    let selectedResource = {};
    
    // Helper function to show toast notifications
    function showNotification(message, type = 'success') {
        const existingNotifications = document.querySelectorAll('.toast-notification');
        existingNotifications.forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = 'toast-notification';
        
        const colors = {
            success: { bg: '#10b981', icon: 'fa-check-circle' },
            error: { bg: '#ef4444', icon: 'fa-exclamation-circle' },
            info: { bg: '#3b82f6', icon: 'fa-info-circle' }
        };
        
        const color = colors[type] || colors.success;
        
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${color.bg};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        `;
        
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
    
    function toggleReplyForm(postId) {
        const form = document.getElementById(`reply-form-${postId}`);
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    }
    
    function showFileUpload(postId) {
        document.getElementById(`file-upload-${postId}`).style.display = 'block';
        document.getElementById(`my-resources-${postId}`).style.display = 'none';
        document.getElementById(`reply-file-${postId}`).click();
    }
    
    function showMyResources(postId, unitCode) {
        document.getElementById(`my-resources-${postId}`).style.display = 'block';
        document.getElementById(`file-upload-${postId}`).style.display = 'none';
        
        fetch(`/lecturer/resources/unit/${unitCode}/resources`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById(`resources-list-${postId}`);
                if (data && data.length > 0) {
                    let html = '<div style="margin-bottom: 8px; font-size: 0.7rem; color: #6b7280;">Select a resource to attach:</div>';
                    data.forEach(resource => {
                        html += `<div onclick="selectResource(${postId}, ${resource.id}, '${resource.title}')" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb; cursor: pointer; hover:background:#f3f4f6;">
                            <i class="fas fa-file-alt"></i> ${resource.title}
                        </div>`;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div style="text-align: center; padding: 20px; color: #6b7280;">No resources found in this unit.</div>';
                }
            })
            .catch(error => {
                console.error('Error loading resources:', error);
                document.getElementById(`resources-list-${postId}`).innerHTML = '<div style="text-align: center; padding: 20px; color: #ef4444;">Error loading resources.</div>';
            });
    }
    
    function selectResource(postId, resourceId, name) {
        selectedResource[postId] = { id: resourceId, name: name };
        document.getElementById(`my-resources-${postId}`).style.display = 'none';
        showNotification(`Resource "${name}" selected.`, 'info');
    }
    
    function closeMyResources(postId) {
        document.getElementById(`my-resources-${postId}`).style.display = 'none';
    }
    
    function clearFile(postId) {
        selectedFile[postId] = null;
        document.getElementById(`reply-file-${postId}`).value = '';
        document.getElementById(`file-preview-${postId}`).style.display = 'none';
        document.getElementById(`file-upload-${postId}`).style.display = 'none';
    }
    
    // MAIN FIXED submitReply FUNCTION
    function submitReply(postId) {
        const content = document.getElementById(`reply-content-${postId}`).value.trim();
        
        if (!content) {
            showNotification('Please enter a reply.', 'error');
            return;
        }
        
        const submitBtn = document.querySelector(`#reply-form-${postId} .btn-submit-reply`);
        const originalText = submitBtn ? submitBtn.innerHTML : 'Post Reply';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        }
        
        const formData = new FormData();
        formData.append('content', content);
        formData.append('_token', window.csrfToken);
        
        const fileInput = document.getElementById(`reply-file-${postId}`);
        if (fileInput && fileInput.files[0]) {
            formData.append('attachment', fileInput.files[0]);
            formData.append('upload_source', 'file_explorer');
        }
        
        if (selectedResource[postId]) {
            formData.append('resource_id', selectedResource[postId].id);
            formData.append('upload_source', 'my_resources');
        }
        
        // FIXED: Use direct URL construction instead of route helper
        const url = `/lecturer/forum/${postId}/reply`;
        
        console.log('Submitting reply to:', url);
        console.log('Content:', content);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const text = await response.text();
                console.error('Error response:', text.substring(0, 500));
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                document.getElementById(`reply-content-${postId}`).value = '';
                delete selectedResource[postId];
                if (fileInput) fileInput.value = '';
                const filePreview = document.getElementById(`file-preview-${postId}`);
                if (filePreview) filePreview.style.display = 'none';
                const fileUpload = document.getElementById(`file-upload-${postId}`);
                if (fileUpload) fileUpload.style.display = 'none';
                toggleReplyForm(postId);
                showNotification('Reply sent successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showNotification(data.message || 'Failed to post reply.', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please check your connection and try again.', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    function deleteReply(replyId, userId) {
        if (userId != window.currentUserId) {
            showNotification('You can only delete your own replies.', 'error');
            return;
        }
        if (!confirm('Are you sure you want to delete this reply?')) return;
        
        const deleteBtn = document.querySelector(`button[onclick="deleteReply(${replyId}, ${userId})"]`);
        const originalIcon = deleteBtn ? deleteBtn.innerHTML : '';
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        const url = `/lecturer/forum/reply/${replyId}`;
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Reply deleted successfully!', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification(data.message || 'Error deleting reply.', 'error');
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = originalIcon;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalIcon;
            }
        });
    }
    
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id && e.target.id.startsWith('reply-file-')) {
            const postId = e.target.id.replace('reply-file-', '');
            const file = e.target.files[0];
            if (file) {
                selectedFile[postId] = file;
                document.getElementById(`file-name-${postId}`).textContent = file.name;
                document.getElementById(`file-preview-${postId}`).style.display = 'block';
            }
        }
    });
</script>
@endpush
@endsection