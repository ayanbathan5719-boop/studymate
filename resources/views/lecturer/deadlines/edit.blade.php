@extends('lecturer.layouts.master')

@section('title', 'Edit Deadline')
@section('page-icon', 'fa-edit')
@section('page-title', 'Edit Deadline')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lecturer.deadlines.index') }}">Deadlines</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Deadline</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .edit-container {
        max-width: 800px;
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

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    textarea.form-control {
        min-height: 100px;
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

    /* Type Selector */
    .type-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-top: 10px;
    }

    .type-option {
        position: relative;
    }

    .type-option input[type="radio"] {
        display: none;
    }

    .type-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        padding: 20px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 0;
    }

    .type-option input[type="radio"]:checked + label {
        border-color: #f59e0b;
        background: #fef3c7;
    }

    .type-option label i {
        font-size: 2rem;
    }

    .type-option label i.fa-clock { color: #64748b; }
    .type-option label i.fa-book-open { color: #f59e0b; }
    .type-option label i.fa-pencil-alt { color: #10b981; }

    .type-option label span {
        font-weight: 500;
        color: #1e293b;
    }

    /* Topic Selection */
    .topic-select-container {
        transition: all 0.3s ease;
    }

    .topic-select-container.hidden {
        opacity: 0.5;
        pointer-events: none;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #f59e0b;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-left: 10px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-edit"></i>
            <h4>Edit Deadline: {{ $deadline->title }}</h4>
        </div>

        @if($errors->any())
            <div class="alert-danger" style="margin-bottom: 20px; padding: 15px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 10px; color: #b91c1c;">
                <i class="fas fa-exclamation-triangle"></i> Please fix the errors below.
            </div>
        @endif

        <form method="POST" action="{{ route('lecturer.deadlines.update', $deadline->id) }}">
            @csrf
            @method('PUT')

            <!-- Unit Selection -->
            <div class="form-group">
                <label for="unit_id">
                    <i class="fas fa-layer-group label-icon"></i> Unit
                    <span class="required">*</span>
                </label>
                <select name="unit_id" 
                        id="unit_id" 
                        class="form-select @error('unit_id') is-invalid @enderror" 
                        required>
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" 
                                {{ old('unit_id', $deadline->unit_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Deadline Type -->
            <div class="form-group">
                <label>
                    <i class="fas fa-tag label-icon"></i> Deadline Type
                    <span class="required">*</span>
                </label>
                <div class="type-selector">
                    <div class="type-option">
                        <input type="radio" name="type" id="type_general" value="general" {{ old('type', $deadline->type) == 'general' ? 'checked' : '' }}>
                        <label for="type_general">
                            <i class="fas fa-clock"></i>
                            <span>General</span>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type_topic" value="topic" {{ old('type', $deadline->type) == 'topic' ? 'checked' : '' }}>
                        <label for="type_topic">
                            <i class="fas fa-book-open"></i>
                            <span>Topic</span>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type_assignment" value="assignment" {{ old('type', $deadline->type) == 'assignment' ? 'checked' : '' }}>
                        <label for="type_assignment">
                            <i class="fas fa-pencil-alt"></i>
                            <span>Assignment</span>
                        </label>
                    </div>
                </div>
                @error('type')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Topic Selection (shown only for topic type) -->
            <div id="topicContainer" class="topic-select-container {{ old('type', $deadline->type) != 'topic' ? 'hidden' : '' }}">
                <div class="form-group">
                    <label for="topic_id">
                        <i class="fas fa-list-ul label-icon"></i> Topic
                        <span class="required">*</span>
                    </label>
                    <select name="topic_id" 
                            id="topic_id" 
                            class="form-select @error('topic_id') is-invalid @enderror">
                        <option value="">Select Topic</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" 
                                    {{ old('topic_id', $deadline->topic_id) == $topic->id ? 'selected' : '' }}>
                                {{ $topic->title }}
                            </option>
                        @endforeach
                    </select>
                    <div id="topicLoading" class="loading-spinner" style="display: none;"></div>
                    @error('topic_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i> Select the topic this deadline is for
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading label-icon"></i> Deadline Title
                    <span class="required">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('title') is-invalid @enderror" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $deadline->title) }}" 
                       placeholder="e.g., Chapter 1 Quiz, Project Submission"
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
                          placeholder="Provide details about this deadline...">{{ old('description', $deadline->description) }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="form-group">
                <label for="due_date">
                    <i class="far fa-calendar-alt label-icon"></i> Due Date
                    <span class="required">*</span>
                </label>
                <input type="datetime-local" 
                       class="form-control @error('due_date') is-invalid @enderror" 
                       id="due_date" 
                       name="due_date" 
                       value="{{ old('due_date', $deadline->due_date->format('Y-m-d\TH:i')) }}"
                       min="{{ now()->format('Y-m-d\TH:i') }}"
                       required>
                @error('due_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Set a future date and time
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('lecturer.deadlines.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Deadline
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Section -->
    <div class="delete-section">
        <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
        <p>Once you delete a deadline, students will no longer see it and all acceptances will be removed.</p>
        <form method="POST" action="{{ route('lecturer.deadlines.destroy', $deadline->id) }}" 
              onsubmit="return confirm('Are you sure you want to delete this deadline? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete Deadline
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle topic selection based on deadline type
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const topicContainer = document.getElementById('topicContainer');
    const topicSelect = document.getElementById('topic_id');

    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'topic') {
                topicContainer.classList.remove('hidden');
                topicSelect.setAttribute('required', 'required');
            } else {
                topicContainer.classList.add('hidden');
                topicSelect.removeAttribute('required');
                topicSelect.value = '';
            }
        });
    });

    // Load topics when unit changes
    const unitSelect = document.getElementById('unit_id');
    const topicLoading = document.getElementById('topicLoading');

    unitSelect.addEventListener('change', function() {
        const unitId = this.value;
        const currentType = document.querySelector('input[name="type"]:checked')?.value;
        
        if (unitId && currentType === 'topic') {
            // Clear current options
            topicSelect.innerHTML = '<option value="">Loading...</option>';
            topicLoading.style.display = 'inline-block';

            // Fetch topics for the selected unit
            fetch(`/lecturer/deadlines/topics/${unitId}`)
                .then(response => response.json())
                .then(topics => {
                    topicSelect.innerHTML = '<option value="">Select Topic</option>';
                    topics.forEach(topic => {
                        const option = document.createElement('option');
                        option.value = topic.id;
                        option.textContent = topic.title;
                        
                        // Preserve selected topic if it exists in the new list
                        @if($deadline->topic_id)
                            if (topic.id == {{ $deadline->topic_id }}) {
                                option.selected = true;
                            }
                        @endif
                        
                        topicSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading topics:', error);
                    topicSelect.innerHTML = '<option value="">Error loading topics</option>';
                })
                .finally(() => {
                    topicLoading.style.display = 'none';
                });
        }
    });
</script>
@endpush
@endsection