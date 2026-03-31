@extends('lecturer.layouts.master')

@section('title', 'Edit Post')
@section('page-title', 'Edit Forum Post')

@push('styles')
<style>
    .edit-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .edit-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .card-header h2 {
        font-size: 1.8rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .card-header p {
        color: rgba(255,255,255,0.9);
        font-size: 1rem;
    }

    .edit-form {
        padding: 40px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: #1e293b;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-group label i {
        color: #f59e0b;
    }

    .form-group label .required {
        color: #ef4444;
        margin-left: 4px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: white;
    }

    .form-control:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 200px;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .help-text {
        margin-top: 6px;
        color: #64748b;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .help-text i {
        color: #667eea;
    }

    .checkbox-group {
        display: flex;
        gap: 30px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #475569;
        cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #f59e0b;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        transform: translateY(-2px);
    }

    .error-summary {
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        color: #b91c1c;
    }

    .error-summary strong {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .error-summary ul {
        margin-left: 25px;
    }

    @media (max-width: 768px) {
        .edit-form {
            padding: 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="edit-container">
    <div class="edit-card">
        <div class="card-header">
            <h2><i class="fas fa-edit"></i> Edit Forum Post</h2>
            <p>Update your post content</p>
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

        <form method="POST" action="{{ route('lecturer.forum.update', $post) }}" class="edit-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i> Title <span class="required">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title', $post->title) }}" 
                       placeholder="e.g., Important Announcement, Discussion Topic"
                       required>
                @error('title')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="unit_id">
                    <i class="fas fa-layer-group"></i> Unit <span class="required">*</span>
                </label>
                <select name="unit_id" 
                        id="unit_id" 
                        class="form-control @error('unit_id') is-invalid @enderror" 
                        required>
                    <option value="">Select a unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ (old('unit_id', $post->unit_id) == $unit->id) ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit_id')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="content">
                    <i class="fas fa-align-left"></i> Content <span class="required">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          class="form-control @error('content') is-invalid @enderror" 
                          placeholder="Write your post content here..."
                          required>{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label><i class="fas fa-tags"></i> Post Options</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_announcement" value="1" {{ old('is_announcement', $post->is_announcement) ? 'checked' : '' }}>
                        <i class="fas fa-bullhorn"></i> Mark as Announcement
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $post->is_pinned) ? 'checked' : '' }}>
                        <i class="fas fa-thumbtack"></i> Pin this post
                    </label>
                </div>
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Announcements and pinned posts appear at the top of the forum.
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('lecturer.forum.show', $post) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection