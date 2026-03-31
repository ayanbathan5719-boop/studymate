@extends('student.layouts.master')

@section('title', 'Create Post')
@section('page-icon', 'fa-pen')
@section('page-title', 'Create New Post')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Post</li>
        </ol>
    </nav>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forum/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/forum-create.css') }}">
@endpush

@section('content')
<div class="create-container">
    <div class="create-card">
        <div class="card-header">
            <h2><i class="fas fa-pen"></i> Create Forum Post</h2>
            <p>Start a discussion or ask a question</p>
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

        <form method="POST" action="{{ route('forum.store') }}" class="create-form" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i> Title <span class="required">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title') }}" 
                       placeholder="e.g., Question about Assignment 1, Need help with Topic 3"
                       required>
                @error('title')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="unit_code">
                    <i class="fas fa-layer-group"></i> Unit <span class="required">*</span>
                </label>
                <select name="unit_code" 
                        id="unit_code" 
                        class="form-select @error('unit_code') is-invalid @enderror" 
                        required>
                    <option value="">Select a unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ old('unit_code') == $unit->code ? 'selected' : '' }}>
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

            <div class="form-group">
                <label for="content">
                    <i class="fas fa-align-left"></i> Content <span class="required">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          class="form-control @error('content') is-invalid @enderror" 
                          placeholder="Write your post content here..."
                          required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- File Upload Section -->
            <div class="form-group">
                <label for="attachments">
                    <i class="fas fa-paperclip"></i> Attachments
                </label>
                <input type="file" 
                       name="attachments[]" 
                       id="attachments" 
                       class="form-control @error('attachments.*') is-invalid @enderror" 
                       multiple
                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip">
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Allowed: PDF, Word, Excel, PowerPoint, Images, ZIP (Max: 10MB per file)
                </div>
                <div id="file-preview" class="file-preview" style="display: none;">
                    <h4><i class="fas fa-paperclip"></i> Selected Files:</h4>
                    <ul id="file-list" class="file-list"></ul>
                </div>
                @error('attachments.*')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('forum.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Create Post
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        window.forumStoreUrl = '{{ route('forum.store') }}';
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/forum/common.js') }}"></script>
    <script src="{{ asset('js/student/forum.js') }}"></script>
    <script src="{{ asset('js/student/forum-create.js') }}"></script>
@endpush
@endsection