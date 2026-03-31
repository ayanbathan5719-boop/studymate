@extends('lecturer.layouts.master')

@section('title', 'Edit Resource - ' . $resource->title)
@section('page-icon', 'fa-edit')
@section('page-title', 'Edit Resource')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.resources.index') }}"><i class="fas fa-folder-open"></i> Resources</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.resources.show', $resource->id) }}">{{ Str::limit($resource->title, 30) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="edit-container">
    {{-- Header --}}
    <div class="edit-header">
        <h1><i class="fas fa-edit"></i> Edit Resource</h1>
        <p class="subtitle">Update your resource details below</p>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div class="error-content">
                <strong>Please fix the following errors:</strong>
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Edit Form Card --}}
    <div class="edit-form-card">
        <form method="POST" 
              action="{{ route('lecturer.resources.update', $resource->id) }}" 
              enctype="multipart/form-data"
              id="editForm">
            @csrf
            @method('PUT')

            {{-- Resource Type Display (Read-only) --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-tag"></i>
                    <h3>Resource Type</h3>
                </div>

                <div class="type-display-card">
                    <div class="type-badge large {{ $resource->file_type }}">
                        @if($resource->file_type === 'pdf')
                            <i class="fas fa-file-pdf"></i> PDF Document
                        @elseif($resource->file_type === 'video')
                            <i class="fas fa-video"></i> Video
                        @elseif($resource->file_type === 'link')
                            <i class="fas fa-link"></i> External Link
                        @elseif($resource->file_type === 'document')
                            <i class="fas fa-file-word"></i> Document
                        @else
                            <i class="fas fa-file-alt"></i> Document
                        @endif
                    </div>
                    <input type="hidden" name="type" value="{{ $resource->file_type }}">
                    <p class="type-note">
                        <i class="fas fa-info-circle"></i>
                        Resource type cannot be changed. Create a new resource if you need a different type.
                    </p>
                </div>
            </div>

            {{-- Basic Information --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    <h3>Basic Information</h3>
                </div>

                {{-- Title --}}
                <div class="form-group">
                    <label for="title" class="required">
                        <i class="fas fa-heading"></i>
                        Resource Title
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title', $resource->title) }}" 
                           required
                           placeholder="e.g., Introduction to Database Systems - Chapter 1">
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="description">
                        <i class="fas fa-align-left"></i>
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="4"
                              placeholder="Provide a detailed description of this resource...">{{ old('description', $resource->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Unit and Topic --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-book"></i>
                    <h3>Unit & Topic</h3>
                </div>

                {{-- Unit Selection --}}
                <div class="form-group">
                    <label for="unit_code" class="required">
                        <i class="fas fa-code"></i>
                        Unit
                    </label>
                    <select id="unit_code" 
                            name="unit_code" 
                            class="form-control @error('unit_code') is-invalid @enderror" 
                            required>
                        <option value="">Select a unit</option>
                        @foreach($assignedUnits as $unit)
                            <option value="{{ $unit->code }}" 
                                {{ old('unit_code', $resource->unit_code) == $unit->code ? 'selected' : '' }}>
                                {{ $unit->code }} - {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Topic Selection --}}
                <div class="form-group">
                    <label for="topic_id">
                        <i class="fas fa-chart-line"></i>
                        Topic (Optional)
                    </label>
                    <select id="topic_id" name="topic_id" class="form-control">
                        <option value="">-- No specific topic --</option>
                        @if(isset($topics) && $topics->count() > 0)
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" 
                                    {{ old('topic_id', $resource->topic_id) == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <small class="form-text">
                        <i class="fas fa-info-circle"></i>
                        Assign this resource to a specific topic for better organization.
                    </small>
                </div>
            </div>

            {{-- File/Link Section --}}
            <div class="form-section" id="file-section">
                <div class="section-title">
                    <i class="fas {{ $resource->file_type === 'link' ? 'fa-link' : 'fa-file' }}"></i>
                    <h3>{{ $resource->file_type === 'link' ? 'Link Details' : 'File Details' }}</h3>
                </div>

                @if($resource->file_type === 'link')
                    {{-- URL Field for Links --}}
                    <div class="form-group">
                        <label for="url" class="required">
                            <i class="fas fa-globe"></i>
                            URL
                        </label>
                        <input type="url" 
                               id="url" 
                               name="url" 
                               class="form-control @error('url') is-invalid @enderror" 
                               value="{{ old('url', $resource->url) }}"
                               placeholder="https://example.com/resource"
                               required>
                        <small class="form-text">
                            <i class="fas fa-info-circle"></i>
                            YouTube and Vimeo links will be automatically embedded.
                        </small>
                        @error('url')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Current Link Preview --}}
                    @if($resource->url)
                        <div class="current-link-preview">
                            <i class="fas fa-link"></i>
                            <span>Current URL:</span>
                            <a href="{{ $resource->url }}" target="_blank">{{ $resource->url }}</a>
                        </div>
                    @endif

                @else
                    {{-- File Upload Field --}}
                    <div class="form-group">
                        <label for="file">
                            <i class="fas fa-upload"></i>
                            Replace File (Optional)
                        </label>
                        
                        {{-- Current File Display --}}
                        @if($resource->file_name)
                            <div class="current-file">
                                <i class="fas fa-check-circle"></i>
                                <span>Current file: <strong>{{ $resource->file_name }}</strong></span>
                                @if($resource->file_size)
                                    <span class="file-size">({{ number_format($resource->file_size / 1024, 2) }} KB)</span>
                                @endif
                            </div>
                        @endif

                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop a new file here or <span>browse</span></p>
                            <span class="file-types" id="file-types-text">
                                @if($resource->file_type === 'pdf')
                                    Supported: PDF files only (Max: 50MB)
                                @elseif($resource->file_type === 'video')
                                    Supported: MP4, MOV, AVI (Max: 200MB)
                                @elseif($resource->file_type === 'document')
                                    Supported: DOC, DOCX, PPT, PPTX, TXT (Max: 50MB)
                                @endif
                            </span>
                            <input type="file" 
                                   id="file" 
                                   name="file" 
                                   class="file-input @error('file') is-invalid @enderror"
                                   accept="@if($resource->file_type === 'pdf').pdf @elseif($resource->file_type === 'video').mp4,.mov,.avi @elseif($resource->file_type === 'document').doc,.docx,.ppt,.pptx,.txt @endif">
                        </div>

                        {{-- File Preview --}}
                        <div class="file-preview" id="filePreview" style="display: none;">
                            <i class="fas fa-file"></i>
                            <span id="fileName"></span>
                            <span id="fileSize" class="file-size"></span>
                            <button type="button" class="remove-file" onclick="clearFileSelection()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        @error('file')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <button type="submit" class="btn-update">
                    <i class="fas fa-save"></i>
                    Update Resource
                </button>
                
                <a href="{{ route('lecturer.resources.show', $resource->id) }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="danger-zone">
        <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
        <div class="danger-card">
            <div class="danger-info">
                <h4>Delete this resource</h4>
                <p>Once you delete this resource, it cannot be recovered. All download history will also be permanently removed.</p>
            </div>
            <form action="{{ route('lecturer.resources.destroy', $resource->id) }}" 
                  method="POST" 
                  onsubmit="return confirm('Are you absolutely sure you want to delete this resource? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash"></i> Delete Resource
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.edit-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 24px;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 24px;
}

.breadcrumb-item a {
    color: #64748b;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #f59e0b;
}

.breadcrumb-item.active {
    color: #0f172a;
    font-weight: 500;
}

.edit-header {
    margin-bottom: 32px;
}

.edit-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.edit-header h1 i {
    color: #f59e0b;
    background: #fffbeb;
    padding: 8px;
    border-radius: 14px;
    font-size: 1.5rem;
}

.edit-header .subtitle {
    color: #64748b;
    font-size: 1rem;
    margin-left: 52px;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #991b1b;
    padding: 16px 20px;
    border-radius: 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.error-content {
    flex: 1;
}

.error-content strong {
    display: block;
    margin-bottom: 8px;
}

.error-list {
    margin: 0;
    padding-left: 20px;
}

.error-list li {
    margin-bottom: 4px;
}

.edit-form-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    overflow: hidden;
    margin-bottom: 24px;
}

.form-section {
    padding: 24px;
    border-bottom: 1px solid #f1f5f9;
}

.form-section:last-child {
    border-bottom: none;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
}

.section-title i {
    color: #f59e0b;
    font-size: 1.1rem;
}

.section-title h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.type-display-card {
    background: #f8fafc;
    border-radius: 16px;
    padding: 16px;
}

.type-badge.large {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: 40px;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.type-badge.large.pdf {
    background: #fee2e2;
    color: #dc2626;
}

.type-badge.large.video {
    background: #dbeafe;
    color: #2563eb;
}

.type-badge.large.link {
    background: #fef3c7;
    color: #d97706;
}

.type-badge.large.document {
    background: #e0f2fe;
    color: #0284c7;
}

.type-note {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.type-note i {
    color: #f59e0b;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    font-weight: 500;
    color: #334155;
    font-size: 0.9rem;
}

.form-group label.required::after {
    content: '*';
    color: #ef4444;
    margin-left: 4px;
}

.form-group label i {
    color: #f59e0b;
    font-size: 0.85rem;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

.form-control:focus {
    border-color: #f59e0b;
    outline: none;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
    background-color: #fef2f2;
}

textarea.form-control {
    resize: vertical;
}

select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    padding-right: 40px;
}

.invalid-feedback {
    display: block;
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 4px;
}

.form-text {
    display: block;
    color: #64748b;
    font-size: 0.8rem;
    margin-top: 4px;
}

.current-file {
    background: #f0fdf4;
    border: 1px solid #dcfce7;
    color: #166534;
    padding: 12px 16px;
    border-radius: 16px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    flex-wrap: wrap;
}

.current-file i {
    color: #10b981;
}

.file-size {
    color: #64748b;
    font-size: 0.8rem;
}

.current-link-preview {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    padding: 12px 16px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    flex-wrap: wrap;
    margin-top: 12px;
}

.current-link-preview i {
    color: #f59e0b;
}

.current-link-preview a {
    color: #2563eb;
    text-decoration: none;
    word-break: break-all;
}

.current-link-preview a:hover {
    text-decoration: underline;
}

.file-upload-area {
    border: 2px dashed #f1f5f9;
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    position: relative;
    transition: all 0.2s ease;
    background: #f8fafc;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #f59e0b;
    background: #fffbeb;
}

.file-upload-area i {
    font-size: 32px;
    color: #f59e0b;
    margin-bottom: 12px;
}

.file-upload-area p {
    color: #334155;
    margin-bottom: 8px;
}

.file-upload-area p span {
    color: #f59e0b;
    font-weight: 600;
    text-decoration: underline;
    cursor: pointer;
}

.file-types {
    font-size: 0.8rem;
    color: #94a3b8;
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-preview {
    margin-top: 16px;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.file-preview i {
    font-size: 24px;
    color: #f59e0b;
}

.file-preview #fileName {
    flex: 1;
    font-weight: 500;
    color: #0f172a;
}

.remove-file {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px 8px;
    transition: color 0.2s ease;
}

.remove-file:hover {
    color: #ef4444;
}

.form-actions {
    padding: 24px;
    background: #f8fafc;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-update,
.btn-cancel {
    padding: 12px 28px;
    border-radius: 40px;
    font-size: 0.95rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
    text-decoration: none;
}

.btn-update {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.btn-cancel {
    background: #f1f5f9;
    color: #475569;
}

.btn-cancel:hover {
    background: #e2e8f0;
    transform: translateY(-1px);
    text-decoration: none;
    color: #334155;
}

.danger-zone {
    background: white;
    border-radius: 24px;
    border: 1px solid #fee2e2;
    overflow: hidden;
}

.danger-zone h3 {
    background: #fef2f2;
    padding: 16px 24px;
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #991b1b;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #fee2e2;
}

.danger-zone h3 i {
    color: #ef4444;
}

.danger-card {
    padding: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.danger-info h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
}

.danger-info p {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

.btn-danger {
    padding: 12px 24px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(239, 68, 68, 0.2);
}

@media (max-width: 768px) {
    .edit-container {
        padding: 16px;
    }
    
    .edit-header h1 {
        font-size: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-update,
    .btn-cancel {
        width: 100%;
        justify-content: center;
    }
    
    .danger-card {
        flex-direction: column;
        text-align: center;
    }
    
    .btn-danger {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// File upload preview
const fileInput = document.getElementById('file');
if (fileInput) {
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = `(${(file.size / 1024).toFixed(2)} KB)`;
            document.getElementById('filePreview').style.display = 'flex';
            const browseSpan = document.querySelector('.file-upload-area p span');
            if (browseSpan) browseSpan.textContent = 'change file';
        }
    });
}

function clearFileSelection() {
    const fileInput = document.getElementById('file');
    if (fileInput) fileInput.value = '';
    document.getElementById('filePreview').style.display = 'none';
    const browseSpan = document.querySelector('.file-upload-area p span');
    if (browseSpan) browseSpan.textContent = 'browse';
}

// Unit change handler - load topics
const unitSelect = document.getElementById('unit_code');
if (unitSelect) {
    unitSelect.addEventListener('change', function() {
        const unitCode = this.value;
        const topicSelect = document.getElementById('topic_id');
        
        if (unitCode && topicSelect) {
            // Show loading
            topicSelect.innerHTML = '<option value="">Loading topics...</option>';
            topicSelect.disabled = true;
            
            // Fetch topics
            fetch(`/lecturer/resources/topics/${unitCode}`)
                .then(response => response.json())
                .then(data => {
                    topicSelect.innerHTML = '<option value="">-- No specific topic --</option>';
                    
                    if (data && Array.isArray(data) && data.length > 0) {
                        data.forEach(topic => {
                            const option = document.createElement('option');
                            option.value = topic.id;
                            option.textContent = topic.name;
                            topicSelect.appendChild(option);
                        });
                    }
                    
                    topicSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading topics:', error);
                    topicSelect.innerHTML = '<option value="">-- No specific topic --</option>';
                    topicSelect.disabled = false;
                });
        }
    });
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.alert-error').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        });
    }, 5000);
});
</script>
@endpush