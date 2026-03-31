@extends('student.layouts.master')

@section('title', 'Create Custom Unit')
@section('page-icon', 'fa-plus-circle')
@section('page-title', 'Create Custom Unit')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.custom.index') }}">Custom Units</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .create-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .create-card {
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

    .create-form {
        padding: 40px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #1e293b;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-group label i {
        color: #f59e0b;
        margin-right: 8px;
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

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .color-picker {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .color-input {
        width: 60px;
        height: 40px;
        padding: 5px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        cursor: pointer;
    }

    .color-value {
        flex: 1;
    }

    .icon-selector {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .icon-option {
        width: 50px;
        height: 50px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .icon-option:hover {
        border-color: #f59e0b;
        background: #fffbeb;
        transform: scale(1.05);
    }

    .icon-option.selected {
        border-color: #f59e0b;
        background: #fffbeb;
        color: #f59e0b;
    }

    .icon-option i {
        pointer-events: none;
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
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .icon-selector {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="create-container">
    <div class="create-card">
        <div class="card-header">
            <h2><i class="fas fa-plus-circle"></i> Create Custom Unit</h2>
            <p>Create your own personal study unit</p>
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

        <form method="POST" action="{{ route('student.custom.store') }}" class="create-form">
            @csrf

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-heading"></i> Unit Name <span class="required">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name') }}" 
                       placeholder="e.g., Personal Development, Coding Practice"
                       required>
                @error('name')
                    <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea name="description" 
                          id="description" 
                          class="form-control @error('description') is-invalid @enderror" 
                          placeholder="What do you want to learn?">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="color">
                        <i class="fas fa-palette"></i> Color
                    </label>
                    <div class="color-picker">
                        <input type="color" 
                               name="color" 
                               id="color" 
                               class="color-input" 
                               value="{{ old('color', '#f59e0b') }}">
                        <input type="text" 
                               class="form-control color-value" 
                               value="{{ old('color', '#f59e0b') }}" 
                               readonly>
                    </div>
                    @error('color')
                        <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="goal_minutes">
                        <i class="fas fa-bullseye"></i> Goal (minutes)
                    </label>
                    <input type="number" 
                           name="goal_minutes" 
                           id="goal_minutes" 
                           class="form-control @error('goal_minutes') is-invalid @enderror" 
                           value="{{ old('goal_minutes') }}" 
                           placeholder="e.g., 120"
                           min="0">
                    @error('goal_minutes')
                        <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-icons"></i> Icon</label>
                <input type="hidden" name="icon" id="icon" value="{{ old('icon', 'fa-book') }}">
                <div class="icon-selector">
                    <div class="icon-option {{ old('icon', 'fa-book') == 'fa-book' ? 'selected' : '' }}" data-icon="fa-book">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-code' ? 'selected' : '' }}" data-icon="fa-code">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-pencil-alt' ? 'selected' : '' }}" data-icon="fa-pencil-alt">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-calculator' ? 'selected' : '' }}" data-icon="fa-calculator">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-flask' ? 'selected' : '' }}" data-icon="fa-flask">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-music' ? 'selected' : '' }}" data-icon="fa-music">
                        <i class="fas fa-music"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-paint-brush' ? 'selected' : '' }}" data-icon="fa-paint-brush">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-globe' ? 'selected' : '' }}" data-icon="fa-globe">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-heart' ? 'selected' : '' }}" data-icon="fa-heart">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-star' ? 'selected' : '' }}" data-icon="fa-star">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-camera' ? 'selected' : '' }}" data-icon="fa-camera">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="icon-option {{ old('icon') == 'fa-microscope' ? 'selected' : '' }}" data-icon="fa-microscope">
                        <i class="fas fa-microscope"></i>
                    </div>
                </div>
                @error('icon')
                    <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('student.custom.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Create Custom Unit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Color picker sync
    const colorInput = document.getElementById('color');
    const colorValue = document.querySelector('.color-value');
    
    colorInput.addEventListener('input', function() {
        colorValue.value = this.value;
    });

    // Icon selector
    const iconInput = document.getElementById('icon');
    const iconOptions = document.querySelectorAll('.icon-option');
    
    iconOptions.forEach(option => {
        option.addEventListener('click', function() {
            iconOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            iconInput.value = this.dataset.icon;
        });
    });
</script>
@endsection