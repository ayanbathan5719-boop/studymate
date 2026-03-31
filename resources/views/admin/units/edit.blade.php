@extends('admin.layouts.master')

@section('title', 'Edit Unit')
@section('page-icon', 'fa-edit')
@section('page-title', 'Edit Unit')

@push('styles')
<link rel="stylesheet" href="/css/admin/forms.css">
<style>
    .select2-container--default .select2-selection--single {
        height: 40px;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .select2-results__option {
        padding: 8px 12px;
    }
    .select2-results__option--highlighted {
        background: #667eea !important;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Units', 'url' => '/admin/units'],
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

        <form action="/admin/units/{{ $unit->id }}" method="POST" data-persist="true">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Unit Name <span class="required-star">*</span></label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $unit->name) }}" 
                       required 
                       placeholder="e.g., Web Development"
                       class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                       autocomplete="off">
                @error('name')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Unit Code <span class="required-star">*</span></label>
                <input type="text" 
                       name="code" 
                       value="{{ old('code', $unit->code) }}" 
                       required 
                       placeholder="e.g., BIT2204"
                       class="form-input {{ $errors->has('code') ? 'error' : '' }}"
                       autocomplete="off">
                @error('code')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" 
                          placeholder="Enter unit description..." 
                          class="form-textarea">{{ old('description', $unit->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Course <span class="required-star">*</span></label>
                <select name="course_id" id="course_id" required class="form-select {{ $errors->has('course_id') ? 'error' : '' }}">
                    <option value=""></option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $unit->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Lecturer (Optional)</label>
                <select name="lecturer_id" id="lecturer_id" class="form-select">
                    <option value=""></option>
                    @foreach($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ old('lecturer_id', $unit->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                            {{ $lecturer->name }} ({{ $lecturer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-edit">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Update Unit
                </button>
                <a href="/admin/units" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-right: 8px;"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#course_id').select2({
            placeholder: '-- Select a course --',
            allowClear: true,
            width: '100%'
        });
        
        $('#lecturer_id').select2({
            placeholder: '-- Select a lecturer --',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush