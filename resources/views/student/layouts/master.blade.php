<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Panel') - StudyMate</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo.png">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="/css/admin/admin.css">
    
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="/css/admin/dark-mode.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Notification Styles -->
    <style>
        /* Notification Bell Styles */
        .notification-dropdown {
            position: relative;
        }

        .notification-bell {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #64748b;
            position: relative;
            padding: 8px;
            transition: color 0.2s;
            border-radius: 50%;
        }

        .notification-bell:hover {
            color: #f59e0b;
            background: #f1f5f9;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 40px;
            min-width: 18px;
            text-align: center;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .notification-dropdown-menu {
            position: absolute;
            top: 45px;
            right: 0;
            width: 380px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            display: none;
            z-index: 1000;
            max-height: 500px;
            overflow-y: auto;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-dropdown-menu.show {
            display: block;
        }

        .notification-dropdown-header {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 16px 16px 0 0;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .notification-dropdown-header h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notification-dropdown-header h4 i {
            color: #f59e0b;
            font-size: 1rem;
        }

        .view-all {
            color: #f59e0b;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .view-all:hover {
            color: #d97706;
            text-decoration: underline;
        }

        .notification-list {
            padding: 8px 0;
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.2s;
            position: relative;
        }

        .notification-item:hover {
            background: #f8fafc;
        }

        .notification-item.unread {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: #f59e0b;
            border-radius: 50%;
        }

        .notification-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
            padding-left: 12px;
        }

        .notification-message {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 6px;
            line-height: 1.4;
            padding-left: 12px;
        }

        .notification-time {
            font-size: 0.7rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 6px;
            padding-left: 12px;
        }

        .notification-time i {
            font-size: 0.65rem;
        }

        .notification-empty {
            padding: 48px 32px;
            text-align: center;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .notification-empty i {
            font-size: 2rem;
            margin-bottom: 12px;
            display: block;
            color: #cbd5e1;
        }

        /* Mark all as read button */
        .mark-all-read {
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .mark-all-read:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* ===== SUBMENU STYLES FOR UNIT RESOURCES DROPDOWN ===== */
        .nav-item {
            position: relative;
        }

        .has-submenu {
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .submenu-icon {
            font-size: 0.7rem;
            transition: transform 0.2s ease;
            margin-left: auto;
        }

        .nav-item:hover .submenu-icon {
            transform: rotate(90deg);
        }

        .submenu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 250px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 100;
        }

        .nav-item:hover .submenu {
            opacity: 1;
            visibility: visible;
            top: calc(100% + 8px);
        }

        .submenu li {
            list-style: none;
        }

        .submenu li a {
            display: block;
            padding: 8px 16px;
            color: #334155;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .submenu li a:hover {
            background: #f8fafc;
            color: #f59e0b;
            padding-left: 24px;
        }

        /* Professional Logout Modal Styles */
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

        @media (max-width: 1024px) {
            .submenu {
                position: static;
                box-shadow: none;
                border: none;
                padding-left: 20px;
                background: transparent;
            }
            
            .submenu li a {
                padding: 6px 0 6px 20px;
                font-size: 0.8rem;
            }
            
            .submenu-icon {
                display: none;
            }
            
            .nav-item:hover .submenu {
                top: auto;
            }
        }

        @media (max-width: 640px) {
            .notification-dropdown-menu {
                width: 320px;
                right: -60px;
            }
            
            .notification-item {
                padding: 10px 12px;
            }

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
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-left">
                <!-- Logo -->
                <a href="/student/dashboard" class="logo">
                    <img src="/images/logo.png" alt="StudyMate" height="70">
                    <span class="logo-text">StudyMate</span>
                </a>
                
                <!-- Student Navigation -->
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="/student/dashboard" class="nav-link {{ request()->is('student/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('student.units.available') }}" class="nav-link {{ request()->is('student/units*') ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i> Units
                        </a>
                    </li>
                    
                    <!-- My Requests Link -->
                    <li class="nav-item">
                        <a href="{{ route('student.units.requests') }}" class="nav-link {{ request()->routeIs('student.units.requests') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> My Requests
                        </a>
                    </li>
                    
                    <!-- Resources Link -->
                    <li class="nav-item">
                        <a href="{{ route('student.resources.index') }}" class="nav-link {{ request()->routeIs('student.resources.*') || request()->routeIs('resources.*') ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i> Resources
                        </a>
                    </li>
                    
                    <!-- ===== UNIT RESOURCES DROPDOWN LINK ===== -->
                    <li class="nav-item">
                        <a href="#" onclick="return false;" class="nav-link has-submenu">
                            <i class="fas fa-folder-open"></i> Unit Resources
                            <i class="fas fa-chevron-right submenu-icon"></i>
                        </a>
                        <ul class="submenu">
                            @foreach(($enrolledUnits ?? Auth::user()->enrolledUnits()->get()) as $unit)
                                <li>
                                    <a href="{{ route('student.units.resources', $unit->id) }}">
                                        {{ $unit->code }} - {{ Str::limit($unit->name, 30) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/student/deadlines" class="nav-link {{ request()->is('student/deadlines*') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Deadlines
                        </a>
                    </li>

                    <!-- Study Progress Link -->
                    <li class="nav-item">
                        <a href="{{ route('study.index') }}" class="nav-link {{ request()->is('study*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> Study Progress
                        </a>
                    </li>
                    
                    <!-- Forum Link -->
                    <li class="nav-item">
                        <a href="{{ route('forum.index') }}" class="nav-link {{ request()->is('forum*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Forum
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
                            <h4><i class="fas fa-bell"></i> Notifications</h4>
                            <button id="markAllReadBtn" class="mark-all-read" style="display: none;">
                                <i class="fas fa-check-double"></i> Mark all read
                            </button>
                            <a href="{{ route('notifications.index') }}" class="view-all">View All</a>
                        </div>
                        <div id="notificationList" class="notification-list">
                            <div class="notification-empty">
                                <i class="far fa-bell-slash"></i>
                                No notifications
                            </div>
                        </div>
                    </div>
                </div>
                
                <button id="darkModeToggle" class="dark-mode-toggle">🌙</button>
                
                <!-- User Menu -->
                <div class="user-menu dropdown">
                    <div class="user-avatar">
                        @if(Auth::user()->avatar)
                            <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ substr(Auth::user()->name ?? 'S', 0, 1) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name ?? 'Student' }}</span>
                        <span class="user-role">Student</span>
                    </div>
                    
                    <div class="user-dropdown">
                        <div class="user-dropdown-header">
                            <div class="user-dropdown-avatar">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                @else
                                    {{ substr(Auth::user()->name ?? 'S', 0, 1) }}
                                @endif
                            </div>
                            <div class="user-dropdown-info">
                                <div class="user-dropdown-name">{{ Auth::user()->name ?? 'Student' }}</div>
                                <div class="user-dropdown-email">{{ Auth::user()->email ?? '' }}</div>
                            </div>
                        </div>
                        <div class="user-dropdown-divider"></div>
                        
                        <a href="/student/profile" class="user-dropdown-item">
                            <i class="fas fa-user-circle"></i> Profile Settings
                        </a>
                        
                        <div class="user-dropdown-divider"></div>
                        
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
    
    <!-- Notification JavaScript -->
    <script>
    // Notification System
    document.addEventListener('DOMContentLoaded', function() {
        const bell = document.getElementById('notificationBell');
        const dropdown = document.querySelector('.notification-dropdown-menu');
        const markAllBtn = document.getElementById('markAllReadBtn');
        
        // Toggle notification dropdown
        if (bell) {
            bell.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
                if (dropdown.classList.contains('show')) {
                    loadNotifications();
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }
        
        // Load notifications
        function loadNotifications() {
            fetch('/notifications/recent')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('notificationList');
                    const badge = document.getElementById('notificationBadge');
                    
                    if (data.notifications && data.notifications.length > 0) {
                        list.innerHTML = data.notifications.map(notification => `
                            <div class="notification-item ${notification.read_at ? '' : 'unread'}" data-id="${notification.id}" data-url="${notification.data.url || '#'}">
                                <div class="notification-title">${escapeHtml(notification.data.title)}</div>
                                <div class="notification-message">${escapeHtml(notification.data.message)}</div>
                                <div class="notification-time">
                                    <i class="far fa-clock"></i> ${notification.time_ago}
                                </div>
                            </div>
                        `).join('');
                        
                        // Show mark all button if there are unread notifications
                        if (data.unread_count > 0) {
                            markAllBtn.style.display = 'flex';
                        } else {
                            markAllBtn.style.display = 'none';
                        }
                        
                        // Update badge
                        if (data.unread_count > 0) {
                            badge.style.display = 'flex';
                            badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        } else {
                            badge.style.display = 'none';
                        }
                    } else {
                        list.innerHTML = '<div class="notification-empty"><i class="far fa-bell-slash"></i>No notifications</div>';
                        badge.style.display = 'none';
                        markAllBtn.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    document.getElementById('notificationList').innerHTML = '<div class="notification-empty"><i class="fas fa-exclamation-triangle"></i>Error loading notifications</div>';
                });
        }
        
        // Load unread count
        function loadUnreadCount() {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (data.count > 0) {
                        badge.style.display = 'flex';
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error loading unread count:', error));
        }
        
        // Mark notification as read
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    loadUnreadCount();
                }
            }).catch(error => console.error('Error marking notification as read:', error));
        }
        
        // Mark all as read
        function markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    loadNotifications();
                    loadUnreadCount();
                    console.log('All notifications marked as read');
                }
            }).catch(error => console.error('Error marking all as read:', error));
        }
        
        // Handle notification click
        document.addEventListener('click', function(e) {
            const item = e.target.closest('.notification-item');
            if (item) {
                const notificationId = item.dataset.id;
                const notificationUrl = item.dataset.url;
                
                if (notificationId) {
                    markAsRead(notificationId);
                }
                
                dropdown.classList.remove('show');
                
                // Redirect if URL exists
                if (notificationUrl && notificationUrl !== '#') {
                    window.location.href = notificationUrl;
                }
            }
        });
        
        // Handle mark all read button
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllAsRead();
            });
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Initial load
        loadUnreadCount();
        
        // Reload unread count every 30 seconds
        setInterval(loadUnreadCount, 30000);
    });
    
    // =============================================
    // PROFESSIONAL LOGOUT MODAL FUNCTIONS
    // =============================================
    
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
        const modalContainer = document.querySelector('.logout-modal-container');
        if (e.target === modal) {
            closeLogoutModal();
        }
    });
    
    document.getElementById('logoutForm')?.addEventListener('submit', function(e) {
        const logoutButton = document.getElementById('logoutButton');
        logoutButton.classList.add('loading');
        logoutButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
        logoutButton.disabled = true;
        
        setTimeout(() => {
            this.submit();
        }, 300);
    });
    </script>
    
    @stack('scripts')
</body>
</html>