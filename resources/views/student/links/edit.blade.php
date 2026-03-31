@extends('student.layouts.master')

@section('title', 'Edit Link')
@section('page-icon', 'fa-edit')
@section('page-title', 'Edit Study Link')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('links.index', $unit->code) }}">{{ $unit->code }} Links</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Link</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .form-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .form-header h2 {
        color: #1e293b;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-header h2 i {
        color: #f59e0b;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #475569;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .form-group label i {
        color: #f59e0b;
        margin-right: 8px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .form-input.is-invalid {
        border-color: #ef4444;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
    }

    .help-text {
        color: #64748b;
        font-size: 0.8rem;
        margin-top: 5px;
    }

    .radio-group {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: #f59e0b;
    }

    .radio-option label {
        margin-bottom: 0;
        cursor: pointer;
    }

    .url-preview {
        margin-top: 15px;
        padding: 15px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .url-preview.hidden {
        display: none;
    }

    .preview-title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 5px;
    }

    .preview-url {
        color: #f59e0b;
        word-break: break-all;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
    }

    .btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    @if($errors->any())
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul style="margin-top: 8px; margin-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="form-card">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Edit Study Link</h2>
            <p style="color: #64748b; margin-top: 5px;">Update your saved resource for {{ $unit->code }}</p>
        </div>

        <form method="POST" action="{{ route('links.update', [$unit->code, $link->id]) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title"><i class="fas fa-heading"></i> Title *</label>
                <input type="text" 
                       class="form-input @error('title') is-invalid @enderror" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $link->title) }}" 
                       required>
                @error('title')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="url"><i class="fas fa-link"></i> URL *</label>
                <input type="url" 
                       class="form-input @error('url') is-invalid @enderror" 
                       id="url" 
                       name="url" 
                       value="{{ old('url', $link->url) }}" 
                       required
                       oninput="updateUrlPreview()">
                @error('url')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Paste the full URL including https://
                </div>

                <div id="urlPreview" class="url-preview {{ old('url', $link->url) ? '' : 'hidden' }}">
                    <div class="preview-title">Preview:</div>
                    <div class="preview-url" id="previewUrl">{{ old('url', $link->url) }}</div>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag"></i> Link Type</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" 
                               id="type_youtube" 
                               name="type" 
                               value="youtube" 
                               {{ old('type', $link->type) == 'youtube' ? 'checked' : '' }}>
                        <label for="type_youtube"><i class="fab fa-youtube" style="color: #ef4444;"></i> YouTube</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" 
                               id="type_article" 
                               name="type" 
                               value="article" 
                               {{ old('type', $link->type) == 'article' ? 'checked' : '' }}>
                        <label for="type_article"><i class="fas fa-file-alt" style="color: #3b82f6;"></i> Article</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" 
                               id="type_other" 
                               name="type" 
                               value="other" 
                               {{ old('type', $link->type) == 'other' ? 'checked' : '' }}>
                        <label for="type_other"><i class="fas fa-link" style="color: #64748b;"></i> Other</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Description (Optional)</label>
                <textarea class="form-textarea @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="4">{{ old('description', $link->description) }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('links.index', $unit->code) }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Update Link
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateUrlPreview() {
        const urlInput = document.getElementById('url');
        const urlPreview = document.getElementById('urlPreview');
        const previewUrl = document.getElementById('previewUrl');
        
        if (urlInput.value.trim() !== '') {
            previewUrl.textContent = urlInput.value;
            urlPreview.classList.remove('hidden');
            
            // Auto-detect YouTube and select appropriate radio
            if (urlInput.value.includes('youtube.com') || urlInput.value.includes('youtu.be')) {
                document.getElementById('type_youtube').checked = true;
            }
        } else {
            urlPreview.classList.add('hidden');
        }
    }

    // Run on page load to show preview
    document.addEventListener('DOMContentLoaded', function() {
        updateUrlPreview();
    });
</script>
@endsection