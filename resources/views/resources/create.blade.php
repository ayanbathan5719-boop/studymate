@extends(Auth::user()->hasRole('admin') ? 'admin.layouts.master' : (Auth::user()->hasRole('lecturer') ? 'lecturer.layouts.master' : 'student.layouts.master'))

@section('title', 'Upload Resource')
@section('page-icon', 'fa-upload')
@section('page-title', 'Upload New Resource')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/{{ Auth::user()->hasRole('admin') ? 'admin' : (Auth::user()->hasRole('lecturer') ? 'lecturer' : 'student') }}/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('resources.index') }}">Resources</a></li>
            @if($selectedUnit)
                <li class="breadcrumb-item"><a href="{{ route('resources.index', ['unit' => $selectedUnit->code]) }}">{{ $selectedUnit->code }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">Upload</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
/* ===== PROFESSIONAL CARD STYLING ===== */
.upload-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 24px;
}

/* Main Card */
.upload-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

/* Card Header */
.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 32px 24px;
    text-align: center;
}

.card-header h2 {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    letter-spacing: -0.02em;
}

.card-header h2 i {
    background: rgba(255,255,255,0.2);
    padding: 8px;
    border-radius: 14px;
}

.card-header p {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
    font-weight: 400;
}

/* Form */
.upload-form {
    padding: 32px;
}

/* Form Groups */
.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #0f172a;
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: -0.01em;
}

.form-group label i {
    color: #f59e0b;
    margin-right: 8px;
}

.form-group label .required {
    color: #ef4444;
    margin-left: 4px;
}

/* Form Controls */
.form-control, .form-select {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

.form-control:focus, .form-select:focus {
    border-color: #f59e0b;
    outline: none;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #ef4444;
}

textarea.form-control {
    min-height: 100px;
    resize: vertical;
    line-height: 1.6;
}

/* Resource Type Tabs */
.type-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.type-tab {
    flex: 1;
    min-width: 120px;
    padding: 16px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.type-tab:hover {
    border-color: #f59e0b;
    background: #fffbeb;
}

.type-tab.active {
    border-color: #f59e0b;
    background: #fffbeb;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
}

.type-tab i {
    font-size: 2rem;
    display: block;
    margin-bottom: 8px;
}

.type-tab i.fa-file { color: #3b82f6; }
.type-tab i.fa-link { color: #10b981; }
.type-tab i.fa-youtube { color: #ef4444; }

.type-tab span {
    font-weight: 500;
    color: #1e293b;
}

.type-tab small {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 4px;
}

/* Conditional Fields */
.conditional-field {
    display: none;
    padding: 20px;
    background: #f8fafc;
    border-radius: 16px;
    margin-top: 16px;
    border: 1px solid #e2e8f0;
}

.conditional-field.active {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* File Upload Area */
.file-upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.file-upload-area:hover {
    border-color: #f59e0b;
    background: #fffbeb;
}

.file-upload-area i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 12px;
}

.file-upload-area .upload-text {
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 4px;
}

.file-upload-area .upload-hint {
    font-size: 0.85rem;
    color: #64748b;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-top: 12px;
}

.file-info i {
    font-size: 1.5rem;
    color: #f59e0b;
}

.file-details {
    flex: 1;
}

.file-name {
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 2px;
}

.file-meta {
    font-size: 0.8rem;
    color: #64748b;
}

.file-remove {
    color: #ef4444;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.2s;
}

.file-remove:hover {
    background: #fee2e2;
}

/* URL Preview */
.url-preview {
    margin-top: 16px;
    padding: 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.url-preview img {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
}

.url-preview .url-domain {
    font-size: 0.85rem;
    color: #64748b;
}

/* Checkbox Styling */
.checkbox-group {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.checkbox-group input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #f59e0b;
}

.checkbox-group label {
    margin: 0;
    font-weight: 500;
}

.checkbox-group small {
    color: #64748b;
    font-size: 0.8rem;
    margin-left: auto;
}

/* Error Messages */
.error-message {
    color: #ef4444;
    font-size: 0.85rem;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.error-summary {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #b91c1c;
    border-radius: 16px;
    padding: 20px 24px;
    margin: 0 32px 24px 32px;
}

.error-summary strong {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-weight: 600;
}

.error-summary ul {
    margin-left: 28px;
    list-style-type: disc;
}

.error-summary li {
    margin-bottom: 4px;
    font-size: 0.9rem;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #f1f5f9;
}

/* Buttons */
.btn {
    padding: 12px 32px;
    border: none;
    border-radius: 40px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 28px rgba(245, 158, 11, 0.35);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .upload-container {
        padding: 16px;
    }
    
    .upload-form {
        padding: 20px;
    }
    
    .error-summary {
        margin: 0 20px 20px 20px;
    }
    
    .type-tabs {
        flex-direction: column;
    }
    
    .type-tab {
        width: 100%;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .checkbox-group {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .checkbox-group small {
        margin-left: 0;
    }
}
</style>
@endpush

@section('content')
<div class="upload-container">
    <div class="upload-card">
        <div class="card-header">
            <h2><i class="fas fa-upload"></i> Upload Resource</h2>
            <p>Share learning materials with your classmates</p>
        </div>

        @if($errors->any())
            <div class="error-summary">
                <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('resources.store') }}" class="upload-form" enctype="multipart/form-data">
            @csrf

            <!-- Unit Selection -->
            <div class="form-group">
                <label for="unit_code">
                    <i class="fas fa-layer-group"></i> Unit <span class="required">*</span>
                </label>
                <select name="unit_code" id="unit_code" class="form-select @error('unit_code') is-invalid @enderror" required>
                    <option value="">Select a unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ old('unit_code', $selectedUnit?->code) == $unit->code ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit_code')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Topic Selection (Optional) -->
            <div class="form-group" id="topic-group" style="{{ $topics->isEmpty() ? 'display: none;' : '' }}">
                <label for="topic_id">
                    <i class="fas fa-list-ul"></i> Topic (Optional)
                </label>
                <select name="topic_id" id="topic_id" class="form-select @error('topic_id') is-invalid @enderror">
                    <option value="">Select a topic (optional)</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>
                            {{ $topic->title }}
                        </option>
                    @endforeach
                </select>
                @error('topic_id')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i> Title <span class="required">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title') }}" 
                       placeholder="e.g., Lecture Notes - Topic 3, Assignment Solutions"
                       required>
                @error('title')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i> Description (Optional)
                </label>
                <textarea name="description" 
                          id="description" 
                          class="form-control @error('description') is-invalid @enderror" 
                          placeholder="Brief description of the resource...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Resource Type Tabs -->
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Resource Type <span class="required">*</span></label>
                <div class="type-tabs">
                    <div class="type-tab {{ old('type', 'file') == 'file' ? 'active' : '' }}" data-type="file">
                        <i class="fas fa-file"></i>
                        <span>File</span>
                        <small>PDF, DOC, PPT, etc.</small>
                    </div>
                    <div class="type-tab {{ old('type') == 'link' ? 'active' : '' }}" data-type="link">
                        <i class="fas fa-link"></i>
                        <span>Link</span>
                        <small>Article, Website</small>
                    </div>
                    <div class="type-tab {{ old('type') == 'youtube' ? 'active' : '' }}" data-type="youtube">
                        <i class="fab fa-youtube"></i>
                        <span>YouTube</span>
                        <small>Video Tutorial</small>
                    </div>
                </div>
                <input type="hidden" name="type" id="resource_type" value="{{ old('type', 'file') }}">
            </div>

            <!-- File Upload Field -->
            <div id="file-field" class="conditional-field {{ old('type', 'file') == 'file' ? 'active' : '' }}">
                <div class="form-group">
                    <label for="file">
                        <i class="fas fa-paperclip"></i> Choose File <span class="required">*</span>
                    </label>
                    <div class="file-upload-area" id="file-upload-area">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div class="upload-text">Click to browse or drag and drop</div>
                        <div class="upload-hint">PDF, Word, Excel, PowerPoint, Images, ZIP (Max: 10MB)</div>
                        <input type="file" 
                               name="file" 
                               id="file" 
                               class="hidden" 
                               style="display: none;"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip">
                    </div>
                    <div id="file-info" class="file-info" style="display: none;">
                        <i class="fas fa-file"></i>
                        <div class="file-details">
                            <div class="file-name" id="file-name"></div>
                            <div class="file-meta" id="file-meta"></div>
                        </div>
                        <div class="file-remove" id="file-remove">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                    @error('file')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Link Field -->
            <div id="link-field" class="conditional-field {{ in_array(old('type'), ['link', 'youtube']) ? 'active' : '' }}">
                <div class="form-group">
                    <label for="url">
                        <i class="fas fa-link"></i> URL <span class="required">*</span>
                    </label>
                    <input type="url" 
                           name="url" 
                           id="url" 
                           class="form-control @error('url') is-invalid @enderror" 
                           value="{{ old('url') }}" 
                           placeholder="https://example.com or https://youtube.com/watch?v=...">
                    <div id="url-preview" class="url-preview" style="display: none;">
                        <img id="preview-image" src="" alt="">
                        <div>
                            <div id="preview-title"></div>
                            <div class="url-domain" id="preview-domain"></div>
                        </div>
                    </div>
                    @error('url')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Link to Forum Post (Optional) -->
            @if($forumPosts->count() > 0)
            <div class="form-group">
                <label for="forum_post_id">
                    <i class="fas fa-comments"></i> Link to Forum Discussion (Optional)
                </label>
                <select name="forum_post_id" id="forum_post_id" class="form-select">
                    <option value="">Select a forum post</option>
                    @foreach($forumPosts as $post)
                        <option value="{{ $post->id }}" {{ old('forum_post_id') == $post->id ? 'selected' : '' }}>
                            {{ $post->title }}
                        </option>
                    @endforeach
                </select>
                <div class="help-text" style="margin-top: 8px; color: #64748b; font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i> Link this resource to an existing forum discussion
                </div>
            </div>
            @endif

            <!-- Create Forum Post Option -->
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" name="create_forum_post" id="create_forum_post" value="1" {{ old('create_forum_post') ? 'checked' : '' }}>
                    <label for="create_forum_post">
                        <i class="fas fa-comment"></i> Create a forum post for this resource
                    </label>
                    <small>This will create a discussion thread where students can ask questions</small>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('resources.index', ['unit' => $selectedUnit?->code]) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Resource
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Resource Type Tabs
        const typeTabs = document.querySelectorAll('.type-tab');
        const typeInput = document.getElementById('resource_type');
        const fileField = document.getElementById('file-field');
        const linkField = document.getElementById('link-field');

        typeTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                typeTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const type = this.dataset.type;
                typeInput.value = type;
                
                // Show/hide conditional fields
                fileField.classList.remove('active');
                linkField.classList.remove('active');
                
                if (type === 'file') {
                    fileField.classList.add('active');
                } else {
                    linkField.classList.add('active');
                }
                
                // Clear URL preview
                document.getElementById('url-preview').style.display = 'none';
            });
        });

        // Load topics when unit changes
        const unitSelect = document.getElementById('unit_code');
        const topicGroup = document.getElementById('topic-group');
        const topicSelect = document.getElementById('topic_id');

        unitSelect.addEventListener('change', function() {
            const unitCode = this.value;
            
            if (unitCode) {
                fetch(`/api/units/${unitCode}/topics`)
                    .then(response => response.json())
                    .then(topics => {
                        topicSelect.innerHTML = '<option value="">Select a topic (optional)</option>';
                        
                        if (topics.length > 0) {
                            topics.forEach(topic => {
                                const option = document.createElement('option');
                                option.value = topic.id;
                                option.textContent = topic.title;
                                topicSelect.appendChild(option);
                            });
                            topicGroup.style.display = 'block';
                        } else {
                            topicGroup.style.display = 'none';
                        }
                    });
            } else {
                topicGroup.style.display = 'none';
            }
        });

        // File upload handling
        const fileInput = document.getElementById('file');
        const fileUploadArea = document.getElementById('file-upload-area');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileMeta = document.getElementById('file-meta');
        const fileRemove = document.getElementById('file-remove');

        fileUploadArea.addEventListener('click', function() {
            fileInput.click();
        });

        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#f59e0b';
            this.style.background = '#fffbeb';
        });

        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.background = 'white';
        });

        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.background = 'white';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileInfo(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateFileInfo(this.files[0]);
            }
        });

        fileRemove.addEventListener('click', function() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            fileUploadArea.style.display = 'block';
        });

        function updateFileInfo(file) {
            fileName.textContent = file.name;
            
            const size = file.size;
            let sizeStr;
            if (size < 1024) {
                sizeStr = size + ' B';
            } else if (size < 1024 * 1024) {
                sizeStr = (size / 1024).toFixed(2) + ' KB';
            } else {
                sizeStr = (size / (1024 * 1024)).toFixed(2) + ' MB';
            }
            
            fileMeta.textContent = sizeStr;
            fileInfo.style.display = 'flex';
            fileUploadArea.style.display = 'none';
        }

        // URL preview
        const urlInput = document.getElementById('url');
        const urlPreview = document.getElementById('url-preview');
        const previewImage = document.getElementById('preview-image');
        const previewTitle = document.getElementById('preview-title');
        const previewDomain = document.getElementById('preview-domain');

        urlInput.addEventListener('input', function() {
            const url = this.value;
            
            if (url) {
                try {
                    const urlObj = new URL(url);
                    previewDomain.textContent = urlObj.hostname;
                    
                    // Check if YouTube
                    if (url.includes('youtube.com') || url.includes('youtu.be')) {
                        const videoId = extractYoutubeId(url);
                        if (videoId) {
                            previewImage.src = `https://img.youtube.com/vi/${videoId}/default.jpg`;
                            previewTitle.textContent = 'YouTube Video';
                            urlPreview.style.display = 'flex';
                            return;
                        }
                    }
                    
                    // Generic link preview
                    previewImage.src = `https://www.google.com/s2/favicons?domain=${urlObj.hostname}&sz=64`;
                    previewTitle.textContent = urlObj.hostname;
                    urlPreview.style.display = 'flex';
                    
                } catch (e) {
                    urlPreview.style.display = 'none';
                }
            } else {
                urlPreview.style.display = 'none';
            }
        });

        function extractYoutubeId(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        }
    });
</script>
@endpush
@endsection