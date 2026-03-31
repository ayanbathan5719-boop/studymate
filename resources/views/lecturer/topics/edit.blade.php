@extends('lecturer.layouts.master')

@section('title', 'Edit Topic')
@section('page-icon', 'fa-edit')
@section('page-title', 'Edit Topic - ' . $unit->name)

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.units') }}">My Units</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.topics.index', $unit->code) }}">Topics</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Topic</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .edit-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .unit-info-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .unit-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .unit-details h3 {
        color: #1e293b;
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .unit-details p {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0;
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

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 5px;
        display: block;
    }

    .help-text {
        color: #64748b;
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .help-text i {
        color: #f59e0b;
    }

    /* Status Toggle */
    .status-toggle {
        display: flex;
        gap: 20px;
        padding: 10px 0;
    }

    .status-option {
        flex: 1;
    }

    .status-option input[type="radio"] {
        display: none;
    }

    .status-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 15px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 0;
    }

    .status-option input[type="radio"]:checked + label {
        border-color: #f59e0b;
        background: #fef3c7;
    }

    .status-option label i {
        font-size: 1.2rem;
    }

    .status-option label i.fa-eye { color: #10b981; }
    .status-option label i.fa-eye-slash { color: #64748b; }

    /* Time Input */
    .time-input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .time-input-group input {
        flex: 1;
    }

    .time-unit {
        color: #64748b;
        font-size: 0.9rem;
        min-width: 60px;
    }

    /* Video Preview */
    .video-preview {
        margin-top: 15px;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        display: none;
    }

    .video-preview.active {
        display: block;
    }

    .video-preview iframe {
        width: 100%;
        height: 250px;
        border-radius: 8px;
    }

    .video-preview-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
        color: #64748b;
        font-size: 0.9rem;
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

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    /* Delete Section */
    .delete-section {
        margin-top: 30px;
        padding: 20px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
    }

    .delete-section h3 {
        color: #b91c1c;
        font-size: 1.1rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .delete-section p {
        color: #7f1d1d;
        font-size: 0.95rem;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<div class="edit-container">
    <!-- Unit Info -->
    <div class="unit-info-card">
        <div class="unit-icon">
            <i class="fas fa-list-ul"></i>
        </div>
        <div class="unit-details">
            <h3>{{ $unit->name }}</h3>
            <p><i class="fas fa-code"></i> {{ $unit->code }}</p>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-edit"></i>
            <h4>Edit Topic: {{ $topic->title }}</h4>
        </div>

        @if($errors->any())
            <div class="alert-danger" style="margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i> Please fix the errors below.
            </div>
        @endif

        <form method="POST" action="{{ route('lecturer.topics.update', [$unit->code, $topic->id]) }}">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading label-icon"></i> Topic Title
                    <span class="required">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('title') is-invalid @enderror" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $topic->title) }}" 
                       placeholder="e.g., Introduction to Variables"
                       required>
                @error('title')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left label-icon"></i> Description
                </label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          placeholder="Brief overview of what this topic covers...">{{ old('description', $topic->description) }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Video URL -->
            <div class="form-group">
                <label for="video_url">
                    <i class="fab fa-youtube label-icon"></i> Video URL
                </label>
                <input type="url" 
                       class="form-control @error('video_url') is-invalid @enderror" 
                       id="video_url" 
                       name="video_url" 
                       value="{{ old('video_url', $topic->video_url) }}" 
                       placeholder="https://www.youtube.com/watch?v=...">
                @error('video_url')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> YouTube videos will be embedded automatically
                </div>

                <!-- Video Preview -->
                <div id="videoPreview" class="video-preview {{ $topic->video_url ? 'active' : '' }}">
                    <div id="videoPreviewContent">
                        @if($topic->video_embed)
                            <iframe src="{{ $topic->video_embed }}" frameborder="0" allowfullscreen></iframe>
                        @endif
                    </div>
                    <div id="videoPreviewInfo" class="video-preview-info">
                        @if($topic->video_embed)
                            <i class="fab fa-youtube" style="color: #FF0000;"></i> YouTube video attached
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="form-group">
                <label for="content">
                    <i class="fas fa-file-alt label-icon"></i> Topic Content
                </label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" 
                          name="content" 
                          rows="8"
                          placeholder="Write the main content for this topic...">{{ old('content', $topic->content) }}</textarea>
                @error('content')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> You can use Markdown formatting
                </div>
            </div>

            <!-- Estimated Time -->
            <div class="form-group">
                <label for="estimated_minutes">
                    <i class="far fa-clock label-icon"></i> Estimated Time to Complete
                </label>
                <div class="time-input-group">
                    <input type="number" 
                           class="form-control @error('estimated_minutes') is-invalid @enderror" 
                           id="estimated_minutes" 
                           name="estimated_minutes" 
                           value="{{ old('estimated_minutes', $topic->estimated_minutes) }}" 
                           placeholder="30"
                           min="1">
                    <span class="time-unit">minutes</span>
                </div>
                @error('estimated_minutes')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> How long students should spend on this topic
                </div>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label>
                    <i class="fas fa-eye label-icon"></i> Topic Status
                </label>
                <div class="status-toggle">
                    <div class="status-option">
                        <input type="radio" name="status" id="status_draft" value="draft" {{ old('status', $topic->status) == 'draft' ? 'checked' : '' }}>
                        <label for="status_draft">
                            <i class="fas fa-eye-slash"></i>
                            <span>Draft</span>
                        </label>
                    </div>
                    <div class="status-option">
                        <input type="radio" name="status" id="status_published" value="published" {{ old('status', $topic->status) == 'published' ? 'checked' : '' }}>
                        <label for="status_published">
                            <i class="fas fa-eye"></i>
                            <span>Published</span>
                        </label>
                    </div>
                </div>
                @error('status')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('lecturer.topics.index', $unit->code) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Topic
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Section -->
    <div class="delete-section">
        <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
        <p>Once you delete a topic, all associated content and resources will be permanently removed.</p>
        <form method="POST" action="{{ route('lecturer.topics.destroy', [$unit->code, $topic->id]) }}" 
              onsubmit="return confirm('Are you sure you want to delete this topic? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete Topic
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Video URL preview
    const videoInput = document.getElementById('video_url');
    const videoPreview = document.getElementById('videoPreview');
    const videoPreviewContent = document.getElementById('videoPreviewContent');
    const videoPreviewInfo = document.getElementById('videoPreviewInfo');

    videoInput.addEventListener('input', function() {
        const url = this.value;
        
        if (!url) {
            videoPreview.classList.remove('active');
            return;
        }

        // Check if it's a YouTube URL
        const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
        const match = url.match(youtubeRegex);

        if (match) {
            const videoId = match[1];
            videoPreviewContent.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`;
            videoPreviewInfo.innerHTML = '<i class="fab fa-youtube" style="color: #FF0000;"></i> YouTube video detected';
            videoPreview.classList.add('active');
        } else {
            videoPreview.classList.remove('active');
        }
    });
</script>
@endpush
@endsection