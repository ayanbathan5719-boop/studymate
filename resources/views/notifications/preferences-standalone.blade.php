<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Preferences - StudyMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .preferences-card {
            max-width: 700px;
            width: 100%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 24px;
            color: white;
        }
        .card-header h2 { font-size: 1.5rem; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; }
        .card-header p { opacity: 0.9; }
        .preferences-section { padding: 24px; border-bottom: 1px solid #e2e8f0; }
        .preferences-section h3 { font-size: 1rem; margin-bottom: 20px; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .preference-item { display: flex; align-items: center; gap: 16px; padding: 12px 0; }
        .switch { position: relative; display: inline-block; width: 52px; height: 28px; flex-shrink: 0; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 22px; width: 22px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
        input:checked + .slider { background-color: #f59e0b; }
        input:checked + .slider:before { transform: translateX(24px); }
        .preference-info strong { display: block; color: #0f172a; margin-bottom: 4px; }
        .preference-info p { color: #64748b; font-size: 0.8rem; }
        .form-actions { padding: 20px 24px; background: #f8fafc; display: flex; justify-content: flex-end; gap: 12px; }
        .btn-save, .btn-cancel { padding: 10px 24px; border-radius: 40px; font-size: 0.9rem; font-weight: 500; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-save { background: #f59e0b; color: white; }
        .btn-save:hover { background: #d97706; transform: translateY(-1px); }
        .btn-cancel { background: #f1f5f9; color: #475569; }
        .btn-cancel:hover { background: #e2e8f0; transform: translateY(-1px); }
        .alert-success { margin: 20px; padding: 12px; background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 12px; color: #166534; display: flex; align-items: center; gap: 10px; }
        @media (max-width: 640px) {
            .preference-item { flex-direction: column; align-items: flex-start; }
            .form-actions { flex-direction: column; }
            .btn-save, .btn-cancel { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="preferences-card">
        <div class="card-header">
            <h2><i class="fas fa-bell"></i> Notification Preferences</h2>
            <p>Choose how you want to be notified</p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('notifications.update-preferences') }}" id="preferencesForm">
            @csrf

            <div class="preferences-section">
                <h3><i class="fas fa-envelope"></i> Email Notifications</h3>
                
                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="email_replies" {{ ($preferences['email_replies'] ?? true) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <div class="preference-info">
                        <strong>Replies to my posts</strong>
                        <p>Receive email when someone replies to your forum posts</p>
                    </div>
                </div>

                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="email_new_posts" {{ ($preferences['email_new_posts'] ?? true) ? 'checked' : '' }}>
                        <span class="slider"></span>
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
                        <span class="slider"></span>
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
                        <span class="slider"></span>
                    </label>
                    <div class="preference-info">
                        <strong>Replies to my posts</strong>
                        <p>Receive in-app notifications when someone replies</p>
                    </div>
                </div>

                <div class="preference-item">
                    <label class="switch">
                        <input type="checkbox" name="push_new_posts" {{ ($preferences['push_new_posts'] ?? true) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <div class="preference-info">
                        <strong>New posts in my units</strong>
                        <p>Receive in-app notifications for new posts</p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('student.dashboard') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-save" id="saveBtn">
                    <i class="fas fa-save"></i> Save Preferences
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('saveBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('preferencesForm').submit();
        });
    </script>
</body>
</html>