@extends('admin.layouts.master')

@section('title', 'Add Lecturer')
@section('page-icon', 'fa-plus-circle')
@section('page-title', 'Add Lecturer')

@push('styles')
<link rel="stylesheet" href="/css/admin/forms.css">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Lecturers', 'url' => '/admin/lecturers'],
        ['name' => 'Create', 'url' => null],
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

        <form action="/admin/lecturers" method="POST" data-persist="true">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Full Name <span class="required-star">*</span></label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       placeholder="e.g., John Doe"
                       class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                       autocomplete="off">
                @error('name')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address <span class="required-star">*</span></label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       placeholder="e.g., john@example.com"
                       class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                       autocomplete="off">
                @error('email')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Must be a unique email address not already registered.
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Department <span class="required-star">*</span></label>
                <input type="text" 
                       name="department" 
                       value="{{ old('department') }}" 
                       required 
                       placeholder="e.g., Information Technology"
                       class="form-input {{ $errors->has('department') ? 'error' : '' }}"
                       autocomplete="off">
                @error('department')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password <span class="required-star">*</span></label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="Enter password"
                       class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                       autocomplete="new-password">
                @error('password')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Minimum 8 characters.
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password <span class="required-star">*</span></label>
                <input type="password" 
                       name="password_confirmation" 
                       required 
                       placeholder="Confirm password"
                       class="form-input"
                       autocomplete="new-password">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Create Lecturer
                </button>
                <a href="/admin/lecturers" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-right: 8px;"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection