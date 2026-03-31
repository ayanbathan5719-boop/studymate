<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lecturer Panel') - StudyMate</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo.png">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="/css/admin/admin.css">
    
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="/css/admin/dark-mode.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Professional Logout Modal Styles -->
    <style>
        /* Logout Modal Overlay */
        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 10000;
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

        .logout-modal-container {
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

        .logout-icon-wrapper i {
            font-size: 40px;
            color: #ef4444;
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

        .session-badge {
            background: #10b98120;
            color: #10b981;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
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

        .btn-logout.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-logout.loading i {
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 640px) {
            .logout-modal-container {
                width: 95%;
                margin: 16px;
            }
            
            .logout-modal-header {
                padding: 24px 24px 0 24px;
            }
            
            .logout-icon-wrapper {
                width: 64px;
                height: 64px;
            }
            
            .logout-icon-wrapper i {
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

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .logout-modal-container {
                background: #1e293b;
            }
            
            .logout-title {
                color: #f1f5f9;
            }
            
            .logout-message {
                color: #94a3b8;
            }
            
            .session-info {
                background: #0f172a;
            }
            
            .session-label {
                color: #94a3b8;
            }
            
            .session-value {
                color: #f1f5f9;
            }
            
            .btn-cancel {
                background: #334155;
                color: #cbd5e1;
                border-color: #475569;
            }
            
            .btn-cancel:hover {
                background: #475569;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-left">
                <!-- Logo - Updated height to 70 for better visibility -->
                <a href="/lecturer/dashboard" class="logo">
                    <img src="/images/logo.png" alt="StudyMate" height="70">
                    <span class="logo-text">StudyMate</span>
                </a>
                
                <!-- Lecturer Navigation -->
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="/lecturer/dashboard" class="nav-link {{ request()->is('lecturer/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a href="/lecturer/units" class="nav-link {{ request()->is('lecturer/units*') ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i> My Units
                        </a>
                        <div class="dropdown-menu">
                            <a href="/lecturer/units" class="dropdown-item">
                                <i class="fas fa-list"></i> All Units
                            </a>
                        </div>
                    </li>
                    
                    <!-- Resources Link -->
                    <li class="nav-item dropdown">
                        <a href="{{ route('lecturer.resources.index') }}" class="nav-link {{ request()->routeIs('lecturer.resources.*') || request()->routeIs('resources.*') ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i> Resources
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('lecturer.resources.index') }}" class="dropdown-item">
                                <i class="fas fa-list"></i> All Resources
                            </a>
                            <a href="{{ route('lecturer.resources.create') }}" class="dropdown-item">
                                <i class="fas fa-plus-circle"></i> Upload Resource
                            </a>
                        </div>
                    </li>
                    
                    <!-- Deadlines Link using route helpers -->
                    <li class="nav-item dropdown">
                        <a href="{{ route('lecturer.deadlines.index') }}" class="nav-link {{ request()->routeIs('lecturer.deadlines.*') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Deadlines
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('lecturer.deadlines.index') }}" class="dropdown-item">
                                <i class="fas fa-list"></i> All Deadlines
                            </a>
                            <a href="{{ route('lecturer.deadlines.create') }}" class="dropdown-item">
                                <i class="fas fa-plus-circle"></i> Set Deadline
                            </a>
                        </div>
                    </li>
                    
                    <!-- Forum Link -->
                    <li class="nav-item dropdown">
                        <a href="/lecturer/forum" class="nav-link {{ request()->is('lecturer/forum*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Forum
                        </a>
                        <div class="dropdown-menu">
                            <a href="/lecturer/forum" class="dropdown-item">
                                <i class="fas fa-list"></i> All Posts
                            </a>
                            <a href="/lecturer/forum/create" class="dropdown-item">
                                <i class="fas fa-plus-circle"></i> New Post
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/lecturer/reports" class="nav-link {{ request()->is('lecturer/reports*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-right">
                <!-- Notification Bell -->
                <div class="notification-dropdown">
                    <button id="notificationBell" class="notification-bell">
                        <i class="far fa-bell"></i>
                        <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
                    </button>
                    <div class="notification-dropdown-menu">
                        <div class="notification-dropdown-header">
                            <h4>Notifications</h4>
                            <a href="/notifications" class="view-all">View All</a>
                        </div>
                        <div id="notificationList" class="notification-list">
                            <div class="notification-empty">No notifications</div>
                        </div>
                    </div>
                </div>
                
                <button id="darkModeToggle" class="dark-mode-toggle">🌙</button>
                
                <!-- User Menu with Avatar -->
                <div class="user-menu dropdown">
                    <div class="user-avatar">
                        @if(Auth::user()->avatar)
                            <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name ?? 'Lecturer' }}</span>
                        <span class="user-role">Lecturer</span>
                    </div>
                    
                    <div class="user-dropdown">
                        <div class="user-dropdown-header">
                            <div class="user-dropdown-avatar">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                @else
                                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                                @endif
                            </div>
                            <div class="user-dropdown-info">
                                <div class="user-dropdown-name">{{ Auth::user()->name ?? 'Lecturer' }}</div>
                                <div class="user-dropdown-email">{{ Auth::user()->email ?? '' }}</div>
                            </div>
                        </div>
                        <div class="user-dropdown-divider"></div>
                        
                        <a href="/lecturer/profile" class="user-dropdown-item">
                            <i class="fas fa-user-circle"></i> Profile Settings
                        </a>
                        
                        <div class="user-dropdown-divider"></div>
                        
                        <!-- UPDATED: Logout link with showLogoutModal function -->
                        <a href="#" class="user-dropdown-item" onclick="showLogoutModal(); return false;">
                            <i class="fas fa-sign-out-alt" style="color: #ef4444;"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                @if(trim($__env->yieldContent('page-icon')))
                    <i class="fas @yield('page-icon') page-icon"></i>
                @endif
                @yield('page-title', 'Dashboard')
            </h1>
            @yield('breadcrumb')
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-copyright">
                <i class="fas fa-copyright"></i> {{ date('Y') }} StudyMate. All rights reserved.
            </div>
            <div class="footer-links">
                <a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
                <a href="#"><i class="fas fa-file-contract"></i> Terms of Service</a>
                <a href="#"><i class="fas fa-headset"></i> Contact Support</a>
            </div>
        </div>
    </footer>

    <!-- ============================================= -->
    <!-- PROFESSIONAL LOGOUT MODAL -->
    <!-- ============================================= -->
    <div id="logoutModal" class="logout-modal-overlay">
        <div class="logout-modal-container">
            <div class="logout-modal-header">
                <div class="logout-icon-wrapper">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h3 class="logout-title">Confirm Logout</h3>
                <p class="logout-message">Are you sure you want to end your session?</p>
                <div class="logout-warning">
                    <i class="fas fa-info-circle"></i>
                    <span>You'll need to sign in again to access your account</span>
                </div>
            </div>
            
            <div class="logout-modal-body">
                <!-- Session Information -->
                <div class="session-info">
                    <div class="session-info-item">
                        <div class="session-label">
                            <i class="fas fa-user-circle"></i>
                            <span>Logged in as</span>
                        </div>
                        <div class="session-value">{{ Auth::user()->name ?? 'Lecturer' }}</div>
                    </div>
                    <div class="session-info-item">
                        <div class="session-label">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </div>
                        <div class="session-value">{{ Auth::user()->email ?? 'lecturer@studymate.com' }}</div>
                    </div>
                    <div class="session-info-item">
                        <div class="session-label">
                            <i class="fas fa-clock"></i>
                            <span>Session started</span>
                        </div>
                        <div class="session-value">{{ now()->format('h:i A') }}</div>
                    </div>
                    <div class="session-info-item">
                        <div class="session-label">
                            <i class="fas fa-shield-alt"></i>
                            <span>Security</span>
                        </div>
                        <div class="session-value">
                            <span class="session-badge">
                                <i class="fas fa-check-circle"></i> Secure Connection
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="logout-modal-actions">
                    <button class="btn-modal btn-cancel" onclick="closeLogoutModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <form id="logoutForm" method="POST" action="/logout" style="flex: 1;">
                        @csrf
                        <button type="submit" class="btn-modal btn-logout" id="logoutButton">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/admin/dashboard.js"></script>
    
    <!-- Logout Modal JavaScript -->
    <script>
    // =============================================
    // PROFESSIONAL LOGOUT MODAL FUNCTIONS
    // =============================================
    
    // Show modal with animation
    function showLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.add('active');
        
        // Prevent body scrolling when modal is open
        document.body.style.overflow = 'hidden';
        
        // Add escape key listener
        document.addEventListener('keydown', handleEscapeKey);
    }
    
    // Close modal with animation
    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('active');
        
        // Restore body scrolling
        document.body.style.overflow = '';
        
        // Remove escape key listener
        document.removeEventListener('keydown', handleEscapeKey);
    }
    
    // Handle escape key press
    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            closeLogoutModal();
        }
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('logoutModal');
        const modalContainer = document.querySelector('.logout-modal-container');
        if (e.target === modal) {
            closeLogoutModal();
        }
    });
    
    // Handle logout button with loading state
    const logoutForm = document.getElementById('logoutForm');
    if (logoutForm) {
        logoutForm.addEventListener('submit', function(e) {
            const logoutButton = document.getElementById('logoutButton');
            
            // Add loading state
            logoutButton.classList.add('loading');
            logoutButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
            logoutButton.disabled = true;
            
            // Small delay to show loading state
            setTimeout(() => {
                this.submit();
            }, 300);
        });
    }
    
    // Notification System (if needed)
    document.addEventListener('DOMContentLoaded', function() {
        const bell = document.getElementById('notificationBell');
        const dropdown = document.querySelector('.notification-dropdown-menu');
        
        if (bell && dropdown) {
            bell.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
            
            document.addEventListener('click', function(e) {
                if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
    </script>
    
    @stack('scripts')
</body>
</html>