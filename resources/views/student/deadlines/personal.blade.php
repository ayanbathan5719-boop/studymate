@extends('student.layouts.master')

@section('title', 'Set Personal Deadline')
@section('page-icon', 'fa-calendar-plus')
@section('page-title', 'Set Personal Deadline')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.deadlines.index') }}">Deadlines</a></li>
            <li class="breadcrumb-item active" aria-current="page">Personal Deadline</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .personal-container {
        max-width: 700px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .personal-card {
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

    .personal-form {
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

    .form-control.is-invalid {
        border-color: #ef4444;
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
        .personal-form {
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
<div class="personal-container">
    <div class="personal-card">
        <div class="card-header">
            <h2><i class="fas fa-calendar-plus"></i> Set Personal Deadline</h2>
            <p>Create your own study goals and track your progress</p>
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

        <form method="POST" action="{{ route('student.deadlines.personal.store') }}" class="personal-form">
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
                       placeholder="e.g., Revision on Chapter 3, Practice Questions"
                       required>
                @error('title')
                    <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Choose a clear, actionable title.
                </div>
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
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit_id')
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
                          placeholder="Add details about what you need to accomplish...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
                <div class="help-text">
                    <i class="fas fa-info-circle"></i> Optional, but helps you stay focused.
                </div>
            </div>

            <div class="form-group">
                <label for="due_date">
                    <i class="fas fa-calendar-alt"></i> Due Date & Time <span class="required">*</span>
                </label>
                <input type="datetime-local" 
                       name="due_date" 
                       id="due_date" 
                       class="form-control @error('due_date') is-invalid @enderror" 
                       value="{{ old('due_date') }}"
                       min="{{ now()->format('Y-m-d\TH:i') }}"
                       required>
                @error('due_date')
                    <div class="error-message" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
                <div class="help-text">
                    <i class="fas fa-clock"></i> Must be a future date and time.
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('student.deadlines.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-plus"></i> Create Personal Deadline
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Set minimum date to now
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    document.getElementById('due_date').min = minDateTime;
</script>
@endsection