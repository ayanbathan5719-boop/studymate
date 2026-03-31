@extends('student.layouts.master')

@section('title', 'Profile Settings')
@section('page-icon', 'fa-user-circle')
@section('page-title', 'Profile Settings')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .alert-success {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 30px;
    }

    /* Profile Card */
    .profile-card {
        background: white;
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .profile-avatar-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 42px;
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
        border: 4px solid white;
        overflow: hidden;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-upload-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 36px;
        height: 36px;
        background: #f59e0b;
        border: none;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 3px solid white;
    }

    .avatar-upload-btn:hover {
        background: #d97706;
        transform: scale(1.1);
    }

    .profile-name {
        color: #1e293b;
        font-size: 1.5rem;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .profile-email {
        color: #64748b;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .profile-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 25px;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .profile-meta {
        background: #f8fafc;
        border-radius: 16px;
        padding: 15px;
        text-align: left;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .meta-item:last-child {
        border-bottom: none;
    }

    .meta-icon {
        width: 36px;
        height: 36px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 1.1rem;
    }

    .meta-content {
        flex: 1;
    }

    .meta-label {
        display: block;
        color: #64748b;
        font-size: 0.8rem;
        margin-bottom: 3px;
    }

    .meta-value {
        display: block;
        color: #1e293b;
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Settings Card */
    .settings-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .header-icon {
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

    .card-header h4 {
        color: #1e293b;
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: #475569;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .label-icon {
        color: #f59e0b;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: white;
    }

    .form-control:focus {
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    .form-control[readonly] {
        background: #f1f5f9;
        cursor: not-allowed;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 5px;
        display: block;
    }

    .password-requirements {
        background: #f8fafc;
        border-radius: 12px;
        padding: 15px;
        margin: 20px 0;
        border: 1px solid #e2e8f0;
    }

    .password-requirements p {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .password-requirements p i {
        color: #f59e0b;
    }

    .password-requirements ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .password-requirements li {
        color: #64748b;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 25px;
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
    }

    .btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    @media (max-width: 992px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
        
        .profile-card {
            max-width: 400px;
            margin: 0 auto;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-container">
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="profile-grid">
        <!-- Left Column - Profile Card -->
        <div class="profile-card">
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar" id="avatarPreview">
                    @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar">
                    @else
                        {{ substr(Auth::user()->name ?? 'S', 0, 1) }}
                    @endif
                </div>
                <label for="avatarUpload" class="avatar-upload-btn">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
            </div>
            <h3 class="profile-name">{{ Auth::user()->name }}</h3>
            <p class="profile-email">{{ Auth::user()->email }}</p>
            <span class="profile-role-badge">
                <i class="fas fa-user-graduate"></i> Student
            </span>
            
            <div class="profile-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar-alt meta-icon"></i>
                    <div class="meta-content">
                        <span class="meta-label">Member Since</span>
                        <span class="meta-value">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock meta-icon"></i>
                    <div class="meta-content">
                        <span class="meta-label">Last Updated</span>
                        <span class="meta-value">{{ Auth::user()->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Edit Forms -->
        <div class="settings-section">
            <!-- Profile Information Form -->
            <div class="settings-card">
                <div class="card-header">
                    <i class="fas fa-user-edit header-icon"></i>
                    <h4>Profile Information</h4>
                </div>
                
                <form method="POST" action="{{ route('student.profile.update') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user label-icon"></i> Full Name
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', Auth::user()->name) }}" 
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope label-icon"></i> Email Address
                        </label>
                        <input type="email" 
                               class="form-control" 
                               value="{{ Auth::user()->email }}" 
                               readonly>
                        <small style="color: #64748b; margin-top: 5px; display: block;">
                            <i class="fas fa-info-circle"></i> Email cannot be changed
                        </small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="settings-card">
                <div class="card-header">
                    <i class="fas fa-key header-icon"></i>
                    <h4>Change Password</h4>
                </div>
                
                <form method="POST" action="{{ route('student.profile.password.update') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="current_password">
                            <i class="fas fa-lock label-icon"></i> Current Password
                        </label>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        @error('current_password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">
                            <i class="fas fa-key label-icon"></i> New Password
                        </label>
                        <input type="password" 
                               class="form-control @error('new_password') is-invalid @enderror" 
                               id="new_password" 
                               name="new_password" 
                               required>
                        @error('new_password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">
                            <i class="fas fa-check-circle label-icon"></i> Confirm Password
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               required>
                    </div>

                    <div class="password-requirements">
                        <p><i class="fas fa-shield-alt"></i> Password Requirements:</p>
                        <ul>
                            <li>✓ At least 8 characters</li>
                            <li>✓ At least one letter</li>
                            <li>✓ At least one number</li>
                        </ul>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('avatarUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("student.profile.avatar") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error uploading image');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error uploading image');
            });
        }
    });
</script>
@endsection