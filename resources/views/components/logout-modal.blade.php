<div id="logoutModal" class="logout-modal-overlay">
    <div class="logout-modal">
        <div class="logout-modal-header">
            <div class="logout-icon-wrapper">
                <i class="fas fa-sign-out-alt logout-icon"></i>
            </div>
            <h3 class="logout-title">Confirm Logout</h3>
            <p class="logout-message">Are you sure you want to end your session?</p>
            <div class="logout-warning">
                <i class="fas fa-info-circle"></i>
                <span>You'll need to sign in again to access your account</span>
            </div>
        </div>
        
        <div class="logout-modal-body">
            <div class="session-info">
                <div class="session-info-item">
                    <div class="session-label">
                        <i class="fas fa-user-circle"></i>
                        <span>Logged in as</span>
                    </div>
                    <div class="session-value">{{ Auth::user()->name ?? 'User' }}</div>
                </div>
                <div class="session-info-item">
                    <div class="session-label">
                        <i class="fas fa-envelope"></i>
                        <span>Email</span>
                    </div>
                    <div class="session-value">{{ Auth::user()->email ?? 'user@example.com' }}</div>
                </div>
            </div>
            
            <div class="logout-modal-actions">
                <button class="btn-modal btn-cancel" onclick="closeLogoutModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-modal btn-logout" id="logoutButton">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .logout-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease;
    }

    .logout-modal-overlay.active {
        display: flex;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .logout-modal {
        background: white;
        border-radius: 32px;
        width: 90%;
        max-width: 480px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .logout-modal-header {
        padding: 32px 32px 0 32px;
        text-align: center;
    }

    .logout-icon-wrapper {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
    }

    .logout-icon {
        font-size: 40px;
        color: #ef4444;
        animation: pulse 0.5s ease;
    }

    @keyframes pulse {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .logout-title {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .logout-message {
        font-size: 16px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .logout-warning {
        font-size: 13px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
    }

    .logout-modal-body {
        padding: 24px 32px;
    }

    .session-info {
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .session-info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .session-info-item:last-child {
        margin-bottom: 0;
    }

    .session-label {
        font-size: 13px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .session-value {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
    }

    .logout-modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-modal {
        flex: 1;
        padding: 14px 24px;
        border-radius: 40px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-cancel:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .btn-logout {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-logout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }

    @media (max-width: 640px) {
        .logout-modal {
            width: 95%;
        }
        
        .logout-modal-header {
            padding: 24px 24px 0 24px;
        }
        
        .logout-icon-wrapper {
            width: 64px;
            height: 64px;
        }
        
        .logout-icon {
            font-size: 32px;
        }
        
        .logout-title {
            font-size: 24px;
        }
        
        .logout-modal-body {
            padding: 20px 24px;
        }
        
        .logout-modal-actions {
            flex-direction: column;
        }
        
        .session-info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
    }
</style>

<script>
    function showLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', handleEscapeKey);
    }
    
    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        document.removeEventListener('keydown', handleEscapeKey);
    }
    
    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            closeLogoutModal();
        }
    }
    
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('logoutModal');
        if (e.target === modal) {
            closeLogoutModal();
        }
    });
    
    document.getElementById('logoutForm')?.addEventListener('submit', function(e) {
        const logoutButton = document.getElementById('logoutButton');
        logoutButton.classList.add('loading');
        logoutButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
        logoutButton.disabled = true;
        setTimeout(() => { this.submit(); }, 300);
    });
</script>