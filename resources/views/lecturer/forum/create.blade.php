@extends('lecturer.layouts.master')

@section('title', 'Create Forum Post')
@section('page-icon', 'fa-plus-circle')
@section('page-title', 'Create New Forum Post')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Post</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .create-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .form-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .form-header i {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .form-header h4 {
        color: #1e293b;
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: #475569;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .label-icon {
        color: #f59e0b;
        font-size: 0.9rem;
    }

    .required {
        color: #ef4444;
        margin-left: 4px;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: white;
    }

    .form-control:focus, .form-select:focus {
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    textarea.form-control {
        min-height: 200px;
        resize: vertical;
    }

    /* Resource Upload Section */
    .resources-section {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #e2e8f0;
    }

    .resources-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .resources-header h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .resources-header h4 i {
        color: #f59e0b;
    }

    .btn-add-resource {
        background: none;
        border: 1px solid #f59e0b;
        color: #f59e0b;
        padding: 6px 12px;
        border-radius: 40px;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-add-resource:hover {
        background: #f59e0b;
        color: white;
    }

    .resource-item {
        background: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .resource-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .resource-icon.file {
        background: #fee2e2;
        color: #dc2626;
    }

    .resource-icon.link {
        background: #dbeafe;
        color: #2563eb;
    }

    .resource-icon.video {
        background: #fef3c7;
        color: #d97706;
    }

    .resource-inputs {
        flex: 1;
    }

    .resource-title-input {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .resource-url-input {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
    }

    .resource-file-input {
        margin-top: 10px;
    }

    .resource-file-input input {
        padding: 8px;
    }

    .btn-remove-resource {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 5px;
        font-size: 1rem;
        transition: color 0.2s;
    }

    .btn-remove-resource:hover {
        color: #ef4444;
    }

    .resource-type-select {
        padding: 8px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.85rem;
        margin-bottom: 10px;
        width: 100%;
    }

    .empty-resources {
        text-align: center;
        padding: 30px;
        color: #94a3b8;
    }

    .empty-resources i {
        font-size: 40px;
        margin-bottom: 10px;
        display: block;
    }

    /* Options Grid */
    .options-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 20px 0;
    }

    .option-card {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .option-card:hover {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .option-card.selected {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .option-card input[type="checkbox"] {
        display: none;
    }

    .option-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .option-icon.pin {
        background: #fef3c7;
        color: #f59e0b;
    }

    .option-icon.announce {
        background: #fee2e2;
        color: #ef4444;
    }

    .option-title {
        font-weight: 600;
        color: #1e293b;
        font-size: 1rem;
    }

    .option-description {
        color: #64748b;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="create-container">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-plus-circle"></i>
            <h4>Create New Forum Post</h4>
        </div>

        @if($errors->any())
            <div class="alert-danger" style="background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 15px; border-radius: 10px; margin-bottom: 25px;">
                <i class="fas fa-exclamation-triangle"></i> Please fix the errors below.
            </div>
        @endif

        <form method="POST" action="{{ route('lecturer.forum.store') }}" id="postForm" enctype="multipart/form-data">
            @csrf

            <!-- Unit Selection -->
            <div class="form-group">
                <label for="unit_code">
                    <i class="fas fa-layer-group label-icon"></i> Unit
                    <span class="required">*</span>
                </label>
                <select name="unit_code" id="unit_code" class="form-select @error('unit_code') is-invalid @enderror" required>
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ old('unit_code') == $unit->code ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit_code')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading label-icon"></i> Post Title
                    <span class="required">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('title') is-invalid @enderror" 
                       id="title" 
                       name="title" 
                       value="{{ old('title') }}" 
                       placeholder="e.g., Important Announcement: Assignment Update"
                       required>
                @error('title')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Content -->
            <div class="form-group">
                <label for="content">
                    <i class="fas fa-align-left label-icon"></i> Post Content
                    <span class="required">*</span>
                </label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" 
                          name="content" 
                          placeholder="Write your post content here...">{{ old('content') }}</textarea>
                @error('content')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Resources Section -->
            <div class="resources-section">
                <div class="resources-header">
                    <h4><i class="fas fa-paperclip"></i> Attach Resources</h4>
                    <button type="button" class="btn-add-resource" onclick="addResource()">
                        <i class="fas fa-plus"></i> Add Resource
                    </button>
                </div>
                
                <div id="resources-container">
                    <div class="empty-resources" id="emptyResourcesMsg">
                        <i class="fas fa-paperclip"></i>
                        <p>No resources attached. Click "Add Resource" to attach files or links.</p>
                    </div>
                </div>
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Attach documents, videos, or external links to your post.
                </div>
            </div>

            <!-- Post Options -->
            <div class="options-grid">
                <!-- Pin Option -->
                <label class="option-card">
                    <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}>
                    <div class="option-header">
                        <div class="option-icon pin">
                            <i class="fas fa-thumbtack"></i>
                        </div>
                        <span class="option-title">Pin this post</span>
                    </div>
                    <p class="option-description">
                        Pinned posts will stay at the top of the forum. Use for important announcements or resources.
                    </p>
                </label>

                <!-- Announcement Option -->
                <label class="option-card">
                    <input type="checkbox" name="is_announcement" value="1" {{ old('is_announcement') ? 'checked' : '' }}>
                    <div class="option-header">
                        <div class="option-icon announce">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <span class="option-title">Mark as announcement</span>
                    </div>
                    <p class="option-description">
                        Announcements will be highlighted and may trigger notifications for students.
                    </p>
                </label>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('lecturer.forum.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Publish Post
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let resourceCount = 0;

    function addResource() {
        resourceCount++;
        const container = document.getElementById('resources-container');
        const emptyMsg = document.getElementById('emptyResourcesMsg');
        
        if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
        
        const resourceHtml = `
            <div class="resource-item" id="resource-${resourceCount}">
                <div class="resource-icon file">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="resource-inputs">
                    <input type="text" name="resources[${resourceCount}][title]" class="resource-title-input" placeholder="Resource Title (e.g., Lecture Notes, Video Tutorial)">
                    <select name="resources[${resourceCount}][type]" class="resource-type-select" onchange="updateResourceIcon(this, ${resourceCount})">
                        <option value="file">📄 File Upload</option>
                        <option value="link">🔗 External Link</option>
                        <option value="video">📺 Video Link</option>
                        <option value="document">📑 Document</option>
                    </select>
                    <div id="resource-input-${resourceCount}">
                        <input type="file" name="resources[${resourceCount}][file]" class="resource-file-input" style="display: none;">
                        <input type="url" name="resources[${resourceCount}][url]" class="resource-url-input" placeholder="Enter URL (for links/videos)">
                    </div>
                </div>
                <button type="button" class="btn-remove-resource" onclick="removeResource(${resourceCount})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', resourceHtml);
        
        // Add event listener for type change
        updateResourceInputType(resourceCount, 'file');
    }

    function updateResourceIcon(select, id) {
        const type = select.value;
        const resourceDiv = document.getElementById(`resource-${id}`);
        const iconDiv = resourceDiv.querySelector('.resource-icon');
        
        // Update icon
        if (type === 'file') {
            iconDiv.innerHTML = '<i class="fas fa-file-alt"></i>';
            iconDiv.className = 'resource-icon file';
        } else if (type === 'link') {
            iconDiv.innerHTML = '<i class="fas fa-link"></i>';
            iconDiv.className = 'resource-icon link';
        } else if (type === 'video') {
            iconDiv.innerHTML = '<i class="fas fa-video"></i>';
            iconDiv.className = 'resource-icon video';
        } else if (type === 'document') {
            iconDiv.innerHTML = '<i class="fas fa-file-word"></i>';
            iconDiv.className = 'resource-icon file';
        }
        
        updateResourceInputType(id, type);
    }
    
    function updateResourceInputType(id, type) {
        const inputContainer = document.getElementById(`resource-input-${id}`);
        if (!inputContainer) return;
        
        if (type === 'file' || type === 'document') {
            inputContainer.innerHTML = `<input type="file" name="resources[${id}][file]" class="resource-file-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.png,.mp4">`;
        } else {
            inputContainer.innerHTML = `<input type="url" name="resources[${id}][url]" class="resource-url-input" placeholder="Enter URL (e.g., https://youtube.com/watch?v=...)">`;
        }
    }

    function removeResource(id) {
        const element = document.getElementById(`resource-${id}`);
        if (element) {
            element.remove();
        }
        
        // Show empty message if no resources left
        const container = document.getElementById('resources-container');
        if (container.children.length === 0 || (container.children.length === 1 && container.children[0].id === 'emptyResourcesMsg')) {
            const emptyMsg = document.getElementById('emptyResourcesMsg');
            if (emptyMsg) {
                emptyMsg.style.display = 'block';
            }
        }
    }

    // Live preview functionality (simplified)
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');

    // Option card selection styling
    document.querySelectorAll('.option-card').forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        
        if (checkbox.checked) {
            card.classList.add('selected');
        }

        card.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                checkbox.checked = !checkbox.checked;
            }
            
            if (checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });

        checkbox.addEventListener('change', function() {
            if (this.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    });

    // Auto-save draft functionality
    let autoSaveTimer;
    const SAVE_DELAY = 3000;

    function saveDraft() {
        const resources = [];
        document.querySelectorAll('.resource-item').forEach((item, index) => {
            const titleInput = item.querySelector('.resource-title-input');
            const typeSelect = item.querySelector('.resource-type-select');
            const urlInput = item.querySelector('.resource-url-input');
            resources.push({
                title: titleInput ? titleInput.value : '',
                type: typeSelect ? typeSelect.value : '',
                url: urlInput ? urlInput.value : ''
            });
        });
        
        const draft = {
            title: titleInput.value,
            content: contentInput.value,
            unit_code: document.getElementById('unit_code').value,
            is_pinned: document.querySelector('input[name="is_pinned"]').checked,
            is_announcement: document.querySelector('input[name="is_announcement"]').checked,
            resources: resources,
            timestamp: new Date().getTime()
        };
        
        localStorage.setItem('forumPostDraft', JSON.stringify(draft));
    }

    function loadDraft() {
        const saved = localStorage.getItem('forumPostDraft');
        if (saved) {
            const draft = JSON.parse(saved);
            const age = new Date().getTime() - draft.timestamp;
            if (age < 24 * 60 * 60 * 1000) {
                if (confirm('You have an unsaved draft from earlier. Would you like to restore it?')) {
                    titleInput.value = draft.title || '';
                    contentInput.value = draft.content || '';
                    document.getElementById('unit_code').value = draft.unit_code || '';
                    document.querySelector('input[name="is_pinned"]').checked = draft.is_pinned || false;
                    document.querySelector('input[name="is_announcement"]').checked = draft.is_announcement || false;
                    
                    if (draft.resources && draft.resources.length > 0) {
                        draft.resources.forEach(res => {
                            addResource();
                            const lastResource = document.querySelector('.resource-item:last-child');
                            if (lastResource) {
                                const titleInput = lastResource.querySelector('.resource-title-input');
                                const typeSelect = lastResource.querySelector('.resource-type-select');
                                const urlInput = lastResource.querySelector('.resource-url-input');
                                if (titleInput) titleInput.value = res.title;
                                if (typeSelect) typeSelect.value = res.type;
                                if (urlInput && res.url) urlInput.value = res.url;
                                if (typeSelect) updateResourceIcon(typeSelect, lastResource.id.split('-')[1]);
                            }
                        });
                    }
                    
                    document.querySelectorAll('.option-card').forEach(card => {
                        const checkbox = card.querySelector('input[type="checkbox"]');
                        if (checkbox.checked) {
                            card.classList.add('selected');
                        } else {
                            card.classList.remove('selected');
                        }
                    });
                }
            }
            localStorage.removeItem('forumPostDraft');
        }
    }

    // Auto-save on input
    [titleInput, contentInput, document.getElementById('unit_code')].forEach(element => {
        if (element) {
            element.addEventListener('input', () => {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(saveDraft, SAVE_DELAY);
            });
        }
    });

    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(saveDraft, SAVE_DELAY);
        });
    });

    // Load draft on page load
    loadDraft();

    // Clear draft on successful form submission
    document.getElementById('postForm').addEventListener('submit', function() {
        localStorage.removeItem('forumPostDraft');
    });
</script>
@endpush
@endsection