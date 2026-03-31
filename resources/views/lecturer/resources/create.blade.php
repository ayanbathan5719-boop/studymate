@extends('lecturer.layouts.master')

@section('title', 'Upload Resource')
@section('page-icon', 'fa-upload')
@section('page-title', 'Upload New Resource')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.resources.index') }}"><i class="fas fa-folder-open"></i> Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Upload</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="upload-container">
    {{-- Header --}}
    <div class="upload-header">
        <h1><i class="fas fa-upload"></i> Upload New Resource</h1>
        <p class="subtitle">Share learning materials with your students</p>
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

    {{-- Upload Form Card --}}
    <div class="upload-form-card">
        <form method="POST" 
              action="{{ route('lecturer.resources.store') }}" 
              enctype="multipart/form-data"
              id="uploadForm">
            @csrf

            {{-- Resource Type Selection --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-tag"></i>
                    <h3>Resource Type</h3>
                </div>

                <div class="type-selector">
                    <div class="type-option">
                        <input type="radio" 
                               name="type" 
                               id="type_pdf" 
                               value="pdf" 
                               {{ old('type') == 'pdf' ? 'checked' : '' }}
                               required>
                        <label for="type_pdf" class="type-label pdf">
                            <i class="fas fa-file-pdf"></i>
                            <span>PDF Document</span>
                        </label>
                    </div>

                    <div class="type-option">
                        <input type="radio" 
                               name="type" 
                               id="type_video" 
                               value="video"
                               {{ old('type') == 'video' ? 'checked' : '' }}>
                        <label for="type_video" class="type-label video">
                            <i class="fas fa-video"></i>
                            <span>Video</span>
                        </label>
                    </div>

                    <div class="type-option">
                        <input type="radio" 
                               name="type" 
                               id="type_link" 
                               value="link"
                               {{ old('type') == 'link' ? 'checked' : '' }}>
                        <label for="type_link" class="type-label link">
                            <i class="fas fa-link"></i>
                            <span>External Link</span>
                        </label>
                    </div>

                    <div class="type-option">
                        <input type="radio" 
                               name="type" 
                               id="type_document" 
                               value="document"
                               {{ old('type') == 'document' ? 'checked' : '' }}>
                        <label for="type_document" class="type-label document">
                            <i class="fas fa-file-word"></i>
                            <span>Document</span>
                        </label>
                    </div>
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
                           value="{{ old('title') }}" 
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
                              placeholder="Provide a detailed description of this resource...">{{ old('description') }}</textarea>
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
                                {{ old('unit_code', $selectedUnit->code ?? '') == $unit->code ? 'selected' : '' }}>
                                {{ $unit->code }} - {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Topic Selection (Dynamic) --}}
                <div class="form-group" id="topic-group">
                    <label for="topic_id">
                        <i class="fas fa-chart-line"></i>
                        Topic (Optional)
                    </label>
                    <select id="topic_id" name="topic_id" class="form-control">
                        <option value="">-- No specific topic --</option>
                        @if(isset($topics) && $topics->count() > 0)
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" 
                                    {{ old('topic_id') == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- File/Link Upload Section --}}
            <div class="form-section" id="file-upload-section">
                <div class="section-title">
                    <i class="fas fa-file"></i>
                    <h3 id="upload-section-title">Upload File</h3>
                </div>

                {{-- File Upload (for non-link types) --}}
                <div id="file-input-group">
                    <div class="form-group">
                        <label for="file" class="required" id="file-label">
                            <i class="fas fa-upload"></i>
                            Select File
                        </label>
                        
                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop your file here or <span>browse</span></p>
                            <span class="file-types" id="file-types-text">
                                Supported: PDF files only (Max: 50MB)
                            </span>
                            <input type="file" 
                                   id="file" 
                                   name="file" 
                                   class="file-input @error('file') is-invalid @enderror">
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
                </div>

                {{-- URL Input (for link type) --}}
                <div id="url-input-group" style="display: none;">
                    <div class="form-group">
                        <label for="url" class="required">
                            <i class="fas fa-globe"></i>
                            URL
                        </label>
                        <input type="url" 
                               id="url" 
                               name="url" 
                               class="form-control @error('url') is-invalid @enderror" 
                               value="{{ old('url') }}"
                               placeholder="https://example.com/resource">
                        <small class="form-text">
                            <i class="fas fa-info-circle"></i>
                            YouTube and Vimeo links will be automatically embedded.
                        </small>
                        @error('url')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Tags (Optional) --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-tags"></i>
                    <h3>Tags (Optional)</h3>
                </div>

                <div class="form-group">
                    <label for="tags">
                        <i class="fas fa-hashtag"></i>
                        Tags (comma separated)
                    </label>
                    <input type="text" 
                           id="tags" 
                           name="tags" 
                           class="form-control" 
                           value="{{ old('tags') }}"
                           placeholder="e.g., database, sql, beginner">
                    <small class="form-text">
                        <i class="fas fa-info-circle"></i>
                        Tags help students find related resources.
                    </small>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-upload"></i>
                    Upload Resource
                </button>
                
                <a href="{{ route('lecturer.resources.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Upload Guidelines --}}
    <div class="guidelines-card">
        <h3><i class="fas fa-info-circle"></i> Upload Guidelines</h3>
        <div class="guidelines-grid">
            <div class="guideline">
                <i class="fas fa-file-pdf" style="color: #dc2626;"></i>
                <div>
                    <strong>PDF Documents</strong>
                    <p>Max size: 50MB. Best for lecture notes, slides, and readings.</p>
                </div>
            </div>
            <div class="guideline">
                <i class="fas fa-video" style="color: #2563eb;"></i>
                <div>
                    <strong>Videos</strong>
                    <p>Max size: 200MB. Supports MP4, MOV, AVI formats.</p>
                </div>
            </div>
            <div class="guideline">
                <i class="fas fa-link" style="color: #d97706;"></i>
                <div>
                    <strong>External Links</strong>
                    <p>YouTube, Vimeo, or any educational website. Will be embedded.</p>
                </div>
            </div>
            <div class="guideline">
                <i class="fas fa-file-word" style="color: #0284c7;"></i>
                <div>
                    <strong>Documents</strong>
                    <p>Max size: 50MB. Supports DOC, DOCX, PPT, PPTX, TXT.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.upload-container {
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

.upload-header {
    margin-bottom: 32px;
}

.upload-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.upload-header h1 i {
    color: #f59e0b;
    background: #fffbeb;
    padding: 8px;
    border-radius: 14px;
    font-size: 1.5rem;
}

.upload-header .subtitle {
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

.upload-form-card {
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

.type-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
}

.type-option {
    position: relative;
}

.type-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.type-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px;
    background: #f8fafc;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

.type-label i {
    font-size: 32px;
}

.type-label.pdf i { color: #dc2626; }
.type-label.video i { color: #2563eb; }
.type-label.link i { color: #d97706; }
.type-label.document i { color: #0284c7; }

.type-label span {
    font-weight: 500;
    color: #334155;
}

.type-option input[type="radio"]:checked + .type-label {
    border-color: #f59e0b;
    background: #fffbeb;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.1);
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

.btn-submit,
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

.btn-submit {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.btn-submit:hover {
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

.guidelines-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    padding: 24px;
}

.guidelines-card h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.guidelines-card h3 i {
    color: #f59e0b;
}

.guidelines-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.guideline {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 16px;
}

.guideline i {
    font-size: 24px;
}

.guideline strong {
    display: block;
    color: #0f172a;
    margin-bottom: 4px;
    font-size: 0.9rem;
}

.guideline p {
    color: #64748b;
    font-size: 0.8rem;
    margin: 0;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .upload-container {
        padding: 16px;
    }
    
    .type-selector {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-submit,
    .btn-cancel {
        width: 100%;
        justify-content: center;
    }
    
    .guidelines-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Resource type change handler
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        updateFormForType(this.value);
    });
});

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
const topicSelect = document.getElementById('topic_id');

if (unitSelect) {
    unitSelect.addEventListener('change', function() {
        const unitCode = this.value;
        
        if (unitCode && topicSelect) {
            // Clear current options and show loading
            topicSelect.innerHTML = '<option value="">Loading topics...</option>';
            topicSelect.disabled = true;
            
            // Fetch topics
            fetch(`/lecturer/resources/topics/${unitCode}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
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
                    topicSelect.innerHTML = '<option value="">-- No topics available --</option>';
                    topicSelect.disabled = false;
                });
        } else {
            topicSelect.innerHTML = '<option value="">-- No specific topic --</option>';
            topicSelect.disabled = false;
        }
    });
}

// Update form based on selected type
function updateFormForType(type) {
    const fileInputGroup = document.getElementById('file-input-group');
    const urlInputGroup = document.getElementById('url-input-group');
    const fileLabel = document.getElementById('file-label');
    const fileTypesText = document.getElementById('file-types-text');
    const uploadTitle = document.getElementById('upload-section-title');
    const fileField = document.getElementById('file');
    const urlField = document.getElementById('url');
    
    // Remove required attributes
    if (fileField) fileField.removeAttribute('required');
    if (urlField) urlField.removeAttribute('required');
    
    if (type === 'link') {
        // Show URL input, hide file input
        if (fileInputGroup) fileInputGroup.style.display = 'none';
        if (urlInputGroup) urlInputGroup.style.display = 'block';
        if (uploadTitle) uploadTitle.textContent = 'Link Details';
        
        // Add required to URL
        if (urlField) urlField.setAttribute('required', 'required');
        
    } else {
        // Show file input, hide URL input
        if (fileInputGroup) fileInputGroup.style.display = 'block';
        if (urlInputGroup) urlInputGroup.style.display = 'none';
        if (uploadTitle) uploadTitle.textContent = 'Upload File';
        
        // Update file restrictions based on type
        if (fileField) {
            switch(type) {
                case 'pdf':
                    if (fileLabel) fileLabel.innerHTML = '<i class="fas fa-upload"></i> Select PDF File';
                    if (fileTypesText) fileTypesText.textContent = 'Supported: PDF files only (Max: 50MB)';
                    fileField.accept = '.pdf';
                    break;
                case 'video':
                    if (fileLabel) fileLabel.innerHTML = '<i class="fas fa-upload"></i> Select Video File';
                    if (fileTypesText) fileTypesText.textContent = 'Supported: MP4, MOV, AVI (Max: 200MB)';
                    fileField.accept = '.mp4,.mov,.avi';
                    break;
                case 'document':
                    if (fileLabel) fileLabel.innerHTML = '<i class="fas fa-upload"></i> Select Document';
                    if (fileTypesText) fileTypesText.textContent = 'Supported: DOC, DOCX, PPT, PPTX, TXT (Max: 50MB)';
                    fileField.accept = '.doc,.docx,.ppt,.pptx,.txt';
                    break;
            }
        }
        
        // Add required to file
        if (fileField) fileField.setAttribute('required', 'required');
    }
}

// Trigger change for pre-selected type
document.addEventListener('DOMContentLoaded', function() {
    const selectedType = document.querySelector('input[name="type"]:checked');
    if (selectedType) {
        updateFormForType(selectedType.value);
    }
    
    // Trigger unit change if unit is pre-selected
    if (unitSelect && unitSelect.value) {
        unitSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush