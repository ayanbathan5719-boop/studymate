document.addEventListener('DOMContentLoaded', function() {
    renderStatsCards();
    renderRecentUsers();
    renderRecentLogs();
    initDarkMode();
    initNotifications();
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('logoutModal');
        if (e.target === modal) {
            hideLogoutModal();
        }
    });
});

// =============================================
// DASHBOARD FUNCTIONS
// =============================================

function renderStatsCards() {
    const statsGrid = document.getElementById('stats-grid');
    if (!statsGrid || !window.statsData) return;
    
    const stats = [
        { label: 'Total Users', value: statsData.total_users, icon: '👥' },
        { label: 'Students', value: statsData.total_students, icon: '🎓' },
        { label: 'Lecturers', value: statsData.total_lecturers, icon: '👨‍🏫' },
        { label: 'Admins', value: statsData.total_admins, icon: '👑' },
        { label: 'Courses', value: statsData.total_courses, icon: '📚' },
        { label: 'Units', value: statsData.total_units, icon: '📖' },
        { label: 'Forum Posts', value: statsData.total_forum_posts, icon: '💬' }
    ];
    
    let html = '';
    stats.forEach(stat => {
        html += `
            <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <h3 style="color: #718096; font-size: 0.9em; text-transform: uppercase;">${stat.label}</h3>
                    <span style="font-size: 1.5em;">${stat.icon}</span>
                </div>
                <div style="font-size: 2.5em; font-weight: bold; color: #2d3748; margin: 10px 0 5px;">${stat.value}</div>
                <div style="color: #a0aec0; font-size: 0.8em;">total in system</div>
            </div>
        `;
    });
    
    statsGrid.innerHTML = html;
}

function renderRecentUsers() {
    const usersList = document.querySelector('#recent-users .users-list');
    if (!usersList || !statsData?.recent_users) return;
    
    let html = '';
    statsData.recent_users.forEach(user => {
        html += `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                <div>
                    <h4 style="color: #2d3748; margin-bottom: 4px;">${escapeHtml(user.name)}</h4>
                    <p style="color: #718096; font-size: 0.9em;">${escapeHtml(user.email)}</p>
                </div>
                <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.8em; background: #e2e8f0;">
                    ${user.role || 'No role'}
                </span>
            </div>
        `;
    });
    
    usersList.innerHTML = html;
}

function renderRecentLogs() {
    const logsList = document.querySelector('#recent-logs .logs-list');
    if (!logsList || !statsData?.recent_logs) return;
    
    let html = '';
    statsData.recent_logs.forEach(log => {
        html += `
            <div style="display: flex; align-items: flex-start; gap: 15px; padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                <span style="font-size: 1.5em;">${getActionIcon(log.action)}</span>
                <div style="flex: 1;">
                    <p style="color: #2d3748; margin-bottom: 4px;">
                        <strong>${escapeHtml(log.user)}</strong> ${escapeHtml(log.description)}
                    </p>
                    <span style="color: #a0aec0; font-size: 0.8em;">${log.time}</span>
                </div>
            </div>
        `;
    });
    
    logsList.innerHTML = html;
}

function getActionIcon(action) {
    const icons = {
        'LOGIN': '🔐',
        'LOGOUT': '🚪',
        'CREATE': '➕',
        'UPDATE': '✏️',
        'DELETE': '❌',
        'DENY_ACCESS': '⛔',
        'RESTORE_ACCESS': '✅'
    };
    return icons[action] || '📋';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// =============================================
// DARK MODE FUNCTIONS
// =============================================

function initDarkMode() {
    const toggle = document.getElementById('darkModeToggle');
    const body = document.body;
    
    if (!toggle) return;
    
    const darkMode = localStorage.getItem('darkMode') === 'true';
    
    if (darkMode) {
        body.classList.add('dark-mode');
        toggle.textContent = '☀️';
    }
    
    toggle.addEventListener('click', function() {
        body.classList.toggle('dark-mode');
        const isDark = body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isDark);
        toggle.textContent = isDark ? '☀️' : '🌙';
    });
}

// =============================================
// USER DROPDOWN FUNCTIONS
// =============================================

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (!dropdown) return;
    
    dropdown.style.opacity = dropdown.style.opacity === '1' ? '0' : '1';
    dropdown.style.visibility = dropdown.style.visibility === 'visible' ? 'hidden' : 'visible';
    dropdown.style.transform = dropdown.style.transform === 'translateY(0)' ? 'translateY(-10px)' : 'translateY(0)';
}

// =============================================
// LOGOUT MODAL FUNCTIONS
// =============================================

function showLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.style.display = 'flex';
}

function hideLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.style.display = 'none';
}

// =============================================
// NOTIFICATION FUNCTIONS - REDESIGNED
// =============================================

function initNotifications() {
    loadNotifications();
    
    // Refresh notifications every 60 seconds
    setInterval(loadNotifications, 60000);
}

function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    const bellIcon = document.querySelector('#notificationBell i');
    
    if (!notificationList) return;
    
    // Show loading state
    notificationList.innerHTML = '<div class="notification-empty"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    fetch('/notifications/recent')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const { notifications, unread_count } = data;
            
            // Update badge and bell icon
            if (notificationBadge) {
                if (unread_count > 0) {
                    notificationBadge.textContent = unread_count > 9 ? '9+' : unread_count;
                    notificationBadge.style.display = 'flex';
                    
                    // Change bell icon to solid when unread
                    if (bellIcon) {
                        bellIcon.className = 'fas fa-bell';
                    }
                } else {
                    notificationBadge.style.display = 'none';
                    
                    // Change bell icon back to regular
                    if (bellIcon) {
                        bellIcon.className = 'far fa-bell';
                    }
                }
            }
            
            // Update list
            if (!notifications || notifications.length === 0) {
                notificationList.innerHTML = '<div class="notification-empty"><i class="far fa-bell-slash"></i><br>No notifications</div>';
                return;
            }
            
            let html = '';
            notifications.forEach(notif => {
                const isUnread = notif.read_at === null;
                const timeAgo = getTimeAgo(notif.created_at);
                const iconClass = notif.icon_class || 'fas fa-bell';
                const bgColor = notif.bg_color || 'bg-gray-100';
                
                html += `
                    <a href="/notifications" class="notification-item ${isUnread ? 'unread' : ''}">
                        <div class="notification-icon ${bgColor}">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${escapeHtml(notif.title)}</div>
                            <div class="notification-message">${escapeHtml(notif.message.substring(0, 60))}${notif.message.length > 60 ? '...' : ''}</div>
                            <div class="notification-time">
                                <i class="far fa-clock"></i> ${timeAgo}
                            </div>
                        </div>
                    </a>
                `;
            });
            
            notificationList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = '<div class="notification-empty"><i class="fas fa-exclamation-triangle"></i><br>Could not load notifications</div>';
        });
}

function getTimeAgo(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return diffMins + 'm ago';
    if (diffHours < 24) return diffHours + 'h ago';
    if (diffDays < 7) return diffDays + 'd ago';
    
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}