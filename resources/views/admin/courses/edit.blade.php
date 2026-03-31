@extends('admin.layouts.master')

@section('title', 'Edit Course')
@section('page-icon', 'fa-edit')
@section('page-title', 'Edit Course')

@push('styles')
<link rel="stylesheet" href="/css/admin/forms.css">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Courses', 'url' => '/admin/courses'],
        ['name' => 'Edit', 'url' => null],
    ]" />

    <div class="form-container">
        @if($errors->any())
            <div class="error-summary">
                <strong><i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>Please fix the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/admin/courses/{{ $course->id }}" method="POST" data-persist="true">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Course Name <span class="required-star">*</span></label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $course->name) }}" 
                       required 
                       placeholder="e.g., Diploma in Business IT"
                       class="form-input {{ $errors->has('name') ? 'error' : '' }}">
                @error('name')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Course Code <span class="required-star">*</span></label>
                <input type="text" 
                       name="code" 
                       value="{{ old('code', $course->code) }}" 
                       required 
                       placeholder="e.g., DBIT"
                       class="form-input {{ $errors->has('code') ? 'error' : '' }}">
                @error('code')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" 
                          placeholder="Enter course description..." 
                          class="form-textarea">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-edit">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Update Course
                </button>
                <a href="/admin/courses" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-right: 8px;"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection