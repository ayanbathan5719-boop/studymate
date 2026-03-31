@php
    $user = Auth::user();
    $layout = 'student.layouts.master';
    if ($user) {
        if ($user->hasRole('admin')) {
            $layout = 'admin.layouts.master';
        } elseif ($user->hasRole('lecturer')) {
            $layout = 'lecturer.layouts.master';
        }
    }
@endphp

@extends($layout)

@section('title', 'Notification Preferences')
@section('page-icon', 'fa-bell')
@section('page-title', 'Notification Preferences')

@section('content')
<div class="preferences-container">
    <div class="preferences-card">
        
        <!-- TEST LINK - ADDED HERE -->
        <div style="background: yellow; padding: 10px; margin: 10px; border-radius: 8px; text-align: center;">
            <a href="{{ route('notifications.update-preferences') }}" 
               onclick="event.preventDefault(); document.querySelector('form').submit();" 
               style="color: black; font-weight: bold; text-decoration: none;">
                ⚡ Test Submit Link - Click to Submit Form
            </a>
        </div>
        
        <div class="card-header">
            <h2><i class="fas fa-bell"></i> Notification Preferences</h2>
            <p>Choose how you want to be notified</p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('notifications.update-preferences') }}">
            @csrf

            <div class="preferences-section">
                <h3><i class="fas fa-envelope"></i> Email Notifications</h3>
                
                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="email_replies" {{ ($preferences['email_replies'] ?? true) ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <div class="preference-info">
                        <strong>Replies to my posts</strong>
                        <p>Receive email when someone replies to your forum posts</p>
                    </div>
                </div>

                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="email_new_posts" {{ ($preferences['email_new_posts'] ?? true) ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <div class="preference-info">
                        <strong>New posts in my units</strong>
                        <p>Receive email when new posts are created in your units</p>
                    </div>
                </div>

                @if(Auth::user()->hasRole('admin'))
                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="email_flags" {{ ($preferences['email_flags'] ?? true) ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <div class="preference-info">
                        <strong>Flag notifications</strong>
                        <p>Receive email when posts are flagged</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="preferences-section">
                <h3><i class="fas fa-mobile-alt"></i> Push Notifications</h3>
                
                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="push_replies" {{ ($preferences['push_replies'] ?? true) ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <div class="preference-info">
                        <strong>Replies to my posts</strong>
                        <p>Receive in-app notifications when someone replies</p>
                    </div>
                </div>

                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="push_new_posts" {{ ($preferences['push_new_posts'] ?? true) ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <div class="preference-info">
                        <strong>New posts in my units</strong>
                        <p>Receive in-app notifications for new posts</p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ url()->previous() }}" class="btn-cancel" id="cancelBtn">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-save" id="saveBtn">
                    <i class="fas fa-save"></i> Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.preferences-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 24px;
}

.preferences-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    padding: 24px;
    color: white;
}

.card-header h2 {
    font-size: 1.5rem;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header p {
    opacity: 0.9;
    margin: 0;
}

.alert-success {
    margin: 20px;
    padding: 12px 16px;
    background: #f0fdf4;
    border-left: 4px solid #10b981;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #166534;
}

.preferences-section {
    padding: 24px;
    border-bottom: 1px solid #e2e8f0;
}

.preferences-section:last-child {
    border-bottom: none;
}

.preferences-section h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.preferences-section h3 i {
    color: #f59e0b;
}

.preference-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.preference-item:last-child {
    border-bottom: none;
}

.switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
    flex-shrink: 0;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
}

input:checked + .slider {
    background-color: #f59e0b;
}

input:checked + .slider:before {
    transform: translateX(24px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}

.preference-info {
    flex: 1;
}

.preference-info strong {
    display: block;
    color: #0f172a;
    margin-bottom: 4px;
    font-size: 0.9rem;
}

.preference-info p {
    color: #64748b;
    font-size: 0.8rem;
    margin: 0;
}

.form-actions {
    padding: 20px 24px;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-save, .btn-cancel {
    padding: 10px 24px;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-save {
    background: #f59e0b;
    color: white;
}

.btn-save:hover {
    background: #d97706;
    transform: translateY(-2px);
}

.btn-cancel {
    background: #f1f5f9;
    color: #475569;
}

.btn-cancel:hover {
    background: #e2e8f0;
}

@media (max-width: 640px) {
    .preference-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-save, .btn-cancel {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save button
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            console.log('Save clicked');
            document.querySelector('form').submit();
        });
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.history.back();
        });
    }
});
</script>
@endpush