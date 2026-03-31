@extends('lecturer.layouts.master')

@section('title', 'Profile Settings')
@section('page-icon', 'fa-user-circle')
@section('page-title', 'Profile Settings')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="profile-settings-container">
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Profile Card -->
        <div class="col-md-4">
            <div class="profile-card">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar" id="avatarPreview">
                        @if(Auth::user()->avatar)
                            <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
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
                    <i class="fas fa-chalkboard-user"></i> Lecturer
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
        </div>
        
        <!-- Right Column - Edit Forms -->
        <div class="col-md-8">
            <!-- Profile Information Form -->
            <div class="settings-card">
                <div class="card-header">
                    <i class="fas fa-user-edit header-icon"></i>
                    <h4>Profile Information</h4>
                </div>
                
                <form method="POST" action="{{ route('lecturer.profile.update') }}" class="profile-form">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user label-icon"></i> Full Name
                            </label>
                            <input type="text" 
                                   class="form-input @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', Auth::user()->name) }}" 
                                   required>
                            @error('name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Change Password Form -->
            <div class="settings-card mt-4">
                <div class="card-header">
                    <i class="fas fa-key header-icon"></i>
                    <h4>Change Password</h4>
                </div>
                
                <form method="POST" action="{{ route('lecturer.profile.password.update') }}" class="profile-form">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="current_password">
                                <i class="fas fa-lock label-icon"></i> Current Password
                            </label>
                            <input type="password" 
                                   class="form-input @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            @error('current_password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-key label-icon"></i> New Password
                            </label>
                            <input type="password" 
                                   class="form-input @error('new_password') is-invalid @enderror" 
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
                                   class="form-input" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   required>
                        </div>
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

<style>
.profile-settings-container {
    padding: 20px 0;
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

.row {
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

/* Form Styles */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    width: 100%;
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

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

.form-input:focus {
    border-color: #f59e0b;
    outline: none;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.form-input.is-invalid {
    border-color: #ef4444;
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

.mt-4 {
    margin-top: 25px;
}

/* Responsive */
@media (max-width: 992px) {
    .row {
        grid-template-columns: 1fr;
    }
    
    .profile-card {
        max-width: 400px;
        margin: 0 auto;
    }
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}
</style>

<script>
    document.getElementById('avatarUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("lecturer.profile.avatar") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error uploading image: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Network error - please check your connection and try again.');
            });
        }
    });
</script>
@endsection