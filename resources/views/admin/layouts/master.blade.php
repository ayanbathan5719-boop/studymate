<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - StudyMate</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link rel="shortcut icon" type="image/png" href="/images/logo.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="/css/admin/admin.css">
    <link rel="stylesheet" href="/css/admin/dashboard.css">
    <link rel="stylesheet" href="/css/admin/forms.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="/css/admin/dark-mode.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Professional Styles -->
    <style>
        /* Bootstrap Override for Dark Mode Toggle */
        .dark-mode-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s;
            margin-right: 12px;
        }
        .dark-mode-toggle:hover {
            background: rgba(0,0,0,0.05);
        }
        
        /* Fix for dropdown menus */
        .nav-item.dropdown {
            position: relative;
        }

        .nav-item.dropdown .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            font-size: 0.875rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.375rem;
        }

        .nav-item.dropdown .dropdown-menu.show {
            display: block;
        }

        .nav-link.dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
        
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
        body.dark-mode .logout-modal-container {
            background: #1e293b;
        }
        
        body.dark-mode .logout-title {
            color: #f1f5f9;
        }
        
        body.dark-mode .logout-message {
            color: #94a3b8;
        }
        
        body.dark-mode .session-info {
            background: #0f172a;
        }
        
        body.dark-mode .session-label {
            color: #94a3b8;
        }
        
        body.dark-mode .session-value {
            color: #f1f5f9;
        }
        
        body.dark-mode .btn-cancel {
            background: #334155;
            color: #cbd5e1;
            border-color: #475569;
        }
        
        body.dark-mode .btn-cancel:hover {
            background: #475569;
        }
        
        /* Badge styling */
        .badge-warning {
            background-color: #f59e0b;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            margin-left: 8px;
        }
        
        /* Dropdown Menu Styling */
        .dropdown-menu {
            display: none;
            position: absolute;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            min-width: 200px;
            z-index: 1000;
            padding: 8px 0;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: #374151;
            text-decoration: none;
            transition: background 0.2s;
        }
        
        .dropdown-item:hover {
            background: #f3f4f6;
            color: #1f2937;
        }
        
        /* Notification Dropdown */
        .notification-dropdown {
            position: relative;
        }
        
        .notification-bell {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            position: relative;
            padding: 8px;
            border-radius: 50%;
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #ef4444;
            color: white;
            font-size: 10px;
            border-radius: 50%;
            padding: 2px 5px;
            min-width: 18px;
        }
        
        .notification-dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .notification-dropdown-menu.show {
            display: block;
        }
        
        .notification-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .notification-empty {
            padding: 20px;
            text-align: center;
            color: #6b7280;
        }
        
        /* Dark Mode */
        body.dark-mode .dropdown-menu,
        body.dark-mode .notification-dropdown-menu {
            background: #1f2937;
            color: #e5e7eb;
        }
        
        body.dark-mode .dropdown-item {
            color: #e5e7eb;
        }
        
        body.dark-mode .dropdown-item:hover {
            background: #374151;
        }
        
        body.dark-mode .notification-dropdown-header {
            border-bottom-color: #374151;
        }
        
        /* ============================================= */
        /* SIMPLE USER MENU - GUARANTEED TO WORK */
        /* ============================================= */
        .simple-user-menu {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 16px 6px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .simple-user-menu:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .simple-user-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .simple-user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .simple-user-info {
            display: flex;
            flex-direction: column;
        }

        .simple-user-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.85rem;
        }

        .simple-user-role {
            font-size: 0.65rem;
            color: #64748b;
        }

        .simple-chevron {
            font-size: 0.7rem;
            color: #64748b;
            transition: transform 0.2s;
        }

        .simple-user-menu.active .simple-chevron {
            transform: rotate(180deg);
        }

        .simple-user-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
            min-width: 280px;
            z-index: 99999;
            border: 1px solid #e2e8f0;
        }

        .simple-user-dropdown.show {
            display: block;
        }

        .simple-dropdown-header {
            display: flex;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            background: #fafbfc;
        }

        .simple-dropdown-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .simple-dropdown-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .simple-dropdown-info {
            flex: 1;
        }

        .simple-dropdown-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .simple-dropdown-email {
            font-size: 0.7rem;
            color: #64748b;
        }

        .simple-dropdown-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 8px 0;
        }

        .simple-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: #374151;
            transition: background 0.2s;
            font-size: 0.85rem;
        }

        .simple-dropdown-item:hover {
            background: #f3f4f6;
            color: #1e293b;
        }

        /* Dark Mode for Simple User Menu */
        body.dark-mode .simple-user-menu {
            background: #1f2937;
            border-color: #374151;
        }

        body.dark-mode .simple-user-menu:hover {
            background: #2d3748;
        }

        body.dark-mode .simple-user-name {
            color: #f3f4f6;
        }

        body.dark-mode .simple-user-role {
            color: #9ca3af;
        }

        body.dark-mode .simple-user-dropdown {
            background: #1f2937;
            border-color: #374151;
        }

        body.dark-mode .simple-dropdown-header {
            background: #111827;
            border-bottom-color: #374151;
        }

        body.dark-mode .simple-dropdown-name {
            color: #f3f4f6;
        }

        body.dark-mode .simple-dropdown-email {
            color: #9ca3af;
        }

        body.dark-mode .simple-dropdown-divider {
            background: #374151;
        }

        body.dark-mode .simple-dropdown-item {
            color: #e5e7eb;
        }

        body.dark-mode .simple-dropdown-item:hover {
            background: #374151;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-left">
                <!-- Logo Section -->
                <a href="/admin/dashboard" class="logo">
                    <img src="/images/logo.png" alt="StudyMate" height="70">
                    <span class="logo-text">StudyMate</span>
                </a>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/courses*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-book"></i> Courses
                        </a>
                        <div class="dropdown-menu">
                            <a href="/admin/courses" class="dropdown-item">
                                <i class="fas fa-list"></i> Manage Courses
                            </a>
                            <a href="/admin/courses/create" class="dropdown-item">
                                <i class="fas fa-plus-circle"></i> Add New Course
                            </a>
                        </div>
                    </li>
                    
                    <!-- Units Dropdown with Unit Assignments -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/units*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-layer-group"></i> Units
                        </a>
                        <div class="dropdown-menu">
                            <a href="/admin/units" class="dropdown-item">
                                <i class="fas fa-list"></i> Manage Units
                            </a>
                            <a href="/admin/units/create" class="dropdown-item">
                                <i class="fas fa-plus-circle"></i> Add New Unit
                            </a>
                            <a href="/admin/units/assignments" class="dropdown-item">
                                <i class="fas fa-tasks"></i> Unit Assignments
                            </a>
                        </div>
                    </li>

                    <!-- Resources Link -->
                    <li class="nav-item">
                        <a href="/admin/resources" class="nav-link {{ request()->is('admin/resources*') ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i> Resources
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/lecturers*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chalkboard-user"></i> Lecturers
                        </a>
                        <div class="dropdown-menu">
                            <a href="/admin/lecturers" class="dropdown-item">
                                <i class="fas fa-list"></i> Manage Lecturers
                            </a>
                            <a href="/admin/lecturers/create" class="dropdown-item">
                                <i class="fas fa-plus-circle"></i> Add New Lecturer
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/students*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-graduate"></i> Students
                        </a>
                        <div class="dropdown-menu">
                            <a href="/admin/students" class="dropdown-item">
                                <i class="fas fa-list"></i> Manage Students
                            </a>
                            <!-- Pending Requests Link -->
                            <a href="{{ route('admin.students.pending-requests') }}" class="dropdown-item">
                                <i class="fas fa-clock"></i> Pending Requests
                                @php
                                    $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge-warning">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/forum*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-comments"></i> Forum
                        </a>
                        <div class="dropdown-menu">
                            <a href="/admin/forum" class="dropdown-item">
                                <i class="fas fa-list"></i> All Posts
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/flags*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-flag"></i> Flags
                        </a>
                        <div class="dropdown-menu">
                            <a href="/admin/flags" class="dropdown-item">
                                <i class="fas fa-list"></i> All Flags
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/audit*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-history"></i> Audit
                        </a>
                        <div class="dropdown-menu">
                            <a href="/admin/audit" class="dropdown-item">
                                <i class="fas fa-list"></i> View Logs
                            </a>
                        </div>
                    </li>
                    <!-- ============================================= -->
                    <!-- REPORTS DROPDOWN -->
                    <!-- ============================================= -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/reports*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('admin.reports.courses') }}" class="dropdown-item">
                                <i class="fas fa-book"></i> Courses Report
                            </a>
                            <a href="{{ route('admin.reports.units') }}" class="dropdown-item">
                                <i class="fas fa-layer-group"></i> Units Report
                            </a>
                            <a href="{{ route('admin.reports.lecturers') }}" class="dropdown-item">
                                <i class="fas fa-chalkboard-user"></i> Lecturers Report
                            </a>
                            <a href="{{ route('admin.reports.students') }}" class="dropdown-item">
                                <i class="fas fa-user-graduate"></i> Students Report
                            </a>
                            <a href="{{ route('admin.reports.forum') }}" class="dropdown-item">
                                <i class="fas fa-comments"></i> Forum Activity Report
                            </a>
                            <a href="{{ route('admin.reports.flags') }}" class="dropdown-item">
                                <i class="fas fa-flag"></i> Flags Report
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- nav-right section -->
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
                
                <!-- SIMPLE USER MENU - GUARANTEED TO WORK -->
                <div class="simple-user-menu">
                    <div class="simple-user-avatar">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar">
                        @else
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        @endif
                    </div>
                    <div class="simple-user-info">
                        <span class="simple-user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span class="simple-user-role">Administrator</span>
                    </div>
                    <i class="fas fa-chevron-down simple-chevron"></i>
                    
                    <div class="simple-user-dropdown" id="simpleUserDropdown">
                        <div class="simple-dropdown-header">
                            <div class="simple-dropdown-avatar">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar">
                                @else
                                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                                @endif
                            </div>
                            <div class="simple-dropdown-info">
                                <div class="simple-dropdown-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="simple-dropdown-email">{{ Auth::user()->email ?? 'admin@studymate.com' }}</div>
                            </div>
                        </div>
                        <div class="simple-dropdown-divider"></div>
                        <a href="/admin/profile" class="simple-dropdown-item">
                            <i class="fas fa-user-circle"></i> Profile Settings
                        </a>
                        <div class="simple-dropdown-divider"></div>
                        <a href="#" class="simple-dropdown-item" onclick="showLogoutModal(); return false;">
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
                <a href="#"><i class="fas fa-sitemap"></i> Sitemap</a>
            </div>
            <div class="footer-version">
                <i class="fas fa-code-branch"></i> v1.0.0
                <span class="footer-build">Build {{ date('Ymd') }}</span>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications -->
    <x-toast />

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
                        <div class="session-value">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    </div>
                    <div class="session-info-item">
                        <div class="session-label">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </div>
                        <div class="session-value">{{ Auth::user()->email ?? 'admin@studymate.com' }}</div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/js/admin/dashboard.js"></script>
    <script src="/js/admin/form-persistence.js"></script>
    
    <!-- Simple User Dropdown and Logout Modal JavaScript -->
    <script>
    // =============================================
    // SIMPLE USER DROPDOWN - GUARANTEED TO WORK
    // =============================================
    document.addEventListener('DOMContentLoaded', function() {
        const userMenu = document.querySelector('.simple-user-menu');
        const userDropdown = document.getElementById('simpleUserDropdown');
        
        if (userMenu && userDropdown) {
            userMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
                userMenu.classList.toggle('active');
                console.log('Dropdown toggled:', userDropdown.classList.contains('show'));
            });
            
            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenu.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('show');
                    userMenu.classList.remove('active');
                }
            });
        }
    });
    
    // =============================================
    // PROFESSIONAL LOGOUT MODAL FUNCTIONS
    // =============================================
    
    // Show modal with animation
    function showLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', handleEscapeKey);
    }
    
    // Close modal with animation
    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
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
        if (e.target === modal) {
            closeLogoutModal();
        }
    });
    
    // Handle logout button with loading state
    const logoutForm = document.getElementById('logoutForm');
    if (logoutForm) {
        logoutForm.addEventListener('submit', function(e) {
            const logoutButton = document.getElementById('logoutButton');
            logoutButton.classList.add('loading');
            logoutButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
            logoutButton.disabled = true;
            setTimeout(() => {
                this.submit();
            }, 300);
        });
    }
    
    // Dark Mode Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            darkModeToggle.textContent = '☀️';
        }
        
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            darkModeToggle.textContent = isDark ? '☀️' : '🌙';
        });
    });
    
    // Notification System
    function fetchNotifications() {
        fetch('/notifications/recent')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                const list = document.getElementById('notificationList');
                
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
                
                if (data.notifications && data.notifications.length > 0) {
                    let html = '';
                    data.notifications.forEach(notif => {
                        html += `
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas ${notif.icon || 'fa-bell'}"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">${notif.title}</div>
                                    <div class="notification-message">${notif.message}</div>
                                    <div class="notification-time">${notif.time_ago}</div>
                                </div>
                            </div>
                        `;
                    });
                    list.innerHTML = html;
                } else {
                    list.innerHTML = '<div class="notification-empty">No notifications</div>';
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }
    
    if (document.getElementById('notificationBell')) {
        fetchNotifications();
        setInterval(fetchNotifications, 30000);
    }
    </script>
    
    @stack('scripts')
</body>
</html>