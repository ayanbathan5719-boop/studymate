@extends('student.layouts.master')

@section('title', 'Edit Custom Unit')
@section('page-icon', 'fa-edit')
@section('page-title', 'Edit Custom Unit')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.custom.index') }}">Custom Units</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/student/custom.css">
@endpush

@section('content')
<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Edit Custom Unit</h2>
            <p>Update your personal study unit</p>
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

        <form method="POST" action="{{ route('student.custom.update', $customUnit) }}" class="form-body">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-heading"></i> Unit Name <span class="required">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $customUnit->name) }}" 
                       placeholder="e.g., Personal Development, Coding Practice"
                       required>
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea name="description" 
                          id="description" 
                          class="form-control @error('description') is-invalid @enderror" 
                          placeholder="What do you want to learn?">{{ old('description', $customUnit->description) }}</textarea>
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
                               value="{{ old('color', $customUnit->color) }}">
                        <input type="text" 
                               class="form-control color-value" 
                               value="{{ old('color', $customUnit->color) }}" 
                               readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="goal_minutes">
                        <i class="fas fa-bullseye"></i> Goal (minutes)
                    </label>
                    <input type="number" 
                           name="goal_minutes" 
                           id="goal_minutes" 
                           class="form-control @error('goal_minutes') is-invalid @enderror" 
                           value="{{ old('goal_minutes', $customUnit->goal_minutes) }}" 
                           placeholder="e.g., 120"
                           min="0">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-icons"></i> Icon</label>
                <input type="hidden" name="icon" id="icon" value="{{ old('icon', $customUnit->icon) }}">
                <div class="icon-selector">
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-book') ? 'selected' : '' }}" data-icon="fa-book">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-code') ? 'selected' : '' }}" data-icon="fa-code">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-pencil-alt') ? 'selected' : '' }}" data-icon="fa-pencil-alt">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-calculator') ? 'selected' : '' }}" data-icon="fa-calculator">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-flask') ? 'selected' : '' }}" data-icon="fa-flask">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-music') ? 'selected' : '' }}" data-icon="fa-music">
                        <i class="fas fa-music"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-paint-brush') ? 'selected' : '' }}" data-icon="fa-paint-brush">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-globe') ? 'selected' : '' }}" data-icon="fa-globe">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-heart') ? 'selected' : '' }}" data-icon="fa-heart">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-star') ? 'selected' : '' }}" data-icon="fa-star">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-camera') ? 'selected' : '' }}" data-icon="fa-camera">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="icon-option {{ (old('icon', $customUnit->icon) == 'fa-microscope') ? 'selected' : '' }}" data-icon="fa-microscope">
                        <i class="fas fa-microscope"></i>
                    </div>
                </div>
            </div>

            <!-- Progress Update Section -->
            <div class="progress-update">
                <h4><i class="fas fa-chart-line"></i> Update Progress</h4>
                <div class="progress-input-group">
                    <input type="number" 
                           name="progress" 
                           id="progress" 
                           class="form-control" 
                           value="{{ old('progress', $customUnit->progress) }}" 
                           placeholder="Progress (minutes)"
                           min="0">
                    <span>/ {{ $customUnit->goal_minutes ?? '∞' }} min</span>
                </div>
                <div class="progress-preview">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill" style="width: {{ $customUnit->progress_percentage }}%"></div>
                    </div>
                    <span class="progress-percent" id="progressPercent">{{ $customUnit->progress_percentage }}%</span>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('student.custom.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/student/custom.js"></script>
@endpush