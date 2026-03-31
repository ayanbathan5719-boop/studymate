const { createApp, ref, onMounted } = Vue;

const app = createApp({
    setup() {
        // State
        const notifications = ref([]);
        const unreadCount = ref({{ Auth::user()->unreadNotifications()->count() }});
        const selectedNotifications = ref([]);
        const selectedNotification = ref(null);
        const showPreferences = ref(false);
        const preferences = ref({
            email_replies: true,
            email_new_posts: true,
            email_flags: true,
            push_replies: true,
            push_new_posts: true,
            push_flags: true
        });

        // Load preferences on mount
        onMounted(() => {
            loadPreferences();
            setupRealTimeUpdates();
        });

        // Real-time updates via polling (every 30 seconds)
        const setupRealTimeUpdates = () => {
            setInterval(() => {
                checkForNewNotifications();
            }, 30000);
        };

        // Check for new notifications
        const checkForNewNotifications = async () => {
            try {
                const response = await fetch('/notifications/recent');
                const data = await response.json();
                
                if (data.unread_count > unreadCount.value) {
                    // New notifications arrived
                    showNewNotificationAlert(data.unread_count - unreadCount.value);
                    updateNotificationBadge(data.unread_count);
                }
                
                unreadCount.value = data.unread_count;
            } catch (error) {
                console.error('Error checking notifications:', error);
            }
        };

        // Show alert for new notifications
        const showNewNotificationAlert = (count) => {
            // You can implement a toast notification here
            if (Notification.permission === 'granted') {
                new Notification('New Notifications', {
                    body: `You have ${count} new notification${count > 1 ? 's' : ''}`,
                    icon: '/images/logo.png'
                });
            }
        };

        // Update notification badge in navbar
        const updateNotificationBadge = (count) => {
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        };

        // Mark all as read
        const markAllAsRead = async () => {
            try {
                const response = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    // Update UI
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');
                    });
                    
                    unreadCount.value = 0;
                    updateNotificationBadge(0);
                    showToast('All notifications marked as read', 'success');
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
                showToast('Error marking notifications as read', 'error');
            }
        };

        // Mark single notification as read
        const markAsRead = async (notificationId) => {
            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const element = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (element) {
                        element.classList.remove('unread');
                        element.classList.add('read');
                    }
                    
                    unreadCount.value = Math.max(0, unreadCount.value - 1);
                    updateNotificationBadge(unreadCount.value);
                    
                    // Remove from selected if present
                    const index = selectedNotifications.value.indexOf(notificationId);
                    if (index > -1) {
                        selectedNotifications.value.splice(index, 1);
                    }
                }
            } catch (error) {
                console.error('Error marking as read:', error);
                showToast('Error marking notification as read', 'error');
            }
        };

        // Delete notification
        const deleteNotification = async (notificationId) => {
            if (!confirm('Delete this notification?')) return;

            try {
                const response = await fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const element = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (element) {
                        // Check if it was unread
                        if (element.classList.contains('unread')) {
                            unreadCount.value = Math.max(0, unreadCount.value - 1);
                            updateNotificationBadge(unreadCount.value);
                        }
                        element.remove();
                    }
                    
                    showToast('Notification deleted', 'success');
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
                showToast('Error deleting notification', 'error');
            }
        };

        // View notification and mark as read
        const viewNotification = async (notificationId, link) => {
            await markAsRead(notificationId);
            if (link && link !== '#') {
                window.location.href = link;
            }
        };

        // Toggle notification selection
        const toggleSelection = (notificationId) => {
            const index = selectedNotifications.value.indexOf(notificationId);
            if (index === -1) {
                selectedNotifications.value.push(notificationId);
            } else {
                selectedNotifications.value.splice(index, 1);
            }
        };

        // Check if notification is selected
        const isSelected = (notificationId) => {
            return selectedNotifications.value.includes(notificationId);
        };

        // Refresh notifications list
        const refreshNotifications = () => {
            location.reload();
        };

        // Load user preferences
        const loadPreferences = async () => {
            try {
                const response = await fetch('/notifications/preferences');
                const data = await response.json();
                preferences.value = { ...preferences.value, ...data.preferences };
            } catch (error) {
                console.error('Error loading preferences:', error);
            }
        };

        // Open preferences modal
        const openPreferences = () => {
            showPreferences.value = true;
        };

        // Close preferences modal
        const closePreferences = () => {
            showPreferences.value = false;
        };

        // Save preferences
        const savePreferences = async () => {
            try {
                const response = await fetch('/notifications/preferences', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(preferences.value)
                });

                if (response.ok) {
                    showToast('Preferences saved', 'success');
                    closePreferences();
                }
            } catch (error) {
                console.error('Error saving preferences:', error);
                showToast('Error saving preferences', 'error');
            }
        };

        // Show toast notification
        const showToast = (message, type = 'info') => {
            // You can implement a toast component here
            // For now, we'll use alert
            alert(message);
        };

        // Request notification permission
        const requestNotificationPermission = () => {
            if ('Notification' in window) {
                Notification.requestPermission();
            }
        };

        // Request permission on mount
        onMounted(() => {
            requestNotificationPermission();
        });

        return {
            notifications,
            unreadCount,
            selectedNotifications,
            selectedNotification,
            showPreferences,
            preferences,
            markAllAsRead,
            markAsRead,
            deleteNotification,
            viewNotification,
            toggleSelection,
            isSelected,
            refreshNotifications,
            openPreferences,
            closePreferences,
            savePreferences
        };
    }
});

app.mount('#notificationsApp');

// Add notification bell functionality to all layouts
document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.querySelector('.notification-dropdown-menu');
    
    if (notificationBell && notificationDropdown) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
            loadRecentNotifications();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });
    }
});

// Load recent notifications for dropdown
async function loadRecentNotifications() {
    try {
        const response = await fetch('/notifications/recent');
        const data = await response.json();
        
        const list = document.getElementById('notificationList');
        if (list) {
            if (data.notifications.length > 0) {
                list.innerHTML = data.notifications.map(n => `
                    <a href="${n.link}" class="notification-item-dropdown ${n.is_read ? 'read' : 'unread'}" 
                       onclick="markNotificationRead('${n.id}')">
                        <div class="notification-icon" style="background: ${n.color}20; color: ${n.color}">
                            <i class="fas ${n.icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${n.title}</div>
                            <div class="notification-message">${n.message}</div>
                            <div class="notification-time">${n.time}</div>
                        </div>
                        ${!n.is_read ? '<span class="unread-dot"></span>' : ''}
                    </a>
                `).join('');
            } else {
                list.innerHTML = '<div class="notification-empty">No notifications</div>';
            }
            
            // Update badge
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    } catch (error) {
        console.error('Error loading recent notifications:', error);
    }
}

// Mark notification as read from dropdown
async function markNotificationRead(id) {
    try {
        await fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

// Add CSS for dropdown
const style = document.createElement('style');
style.textContent = `
    .notification-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        width: 350px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        display: none;
        z-index: 1000;
        margin-top: 10px;
    }
    
    .notification-dropdown-menu.show {
        display: block;
    }
    
    .notification-dropdown-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .notification-dropdown-header h4 {
        margin: 0;
        color: #1e293b;
        font-size: 1rem;
    }
    
    .notification-dropdown-header .view-all {
        color: #f59e0b;
        text-decoration: none;
        font-size: 0.85rem;
    }
    
    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .notification-item-dropdown {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        color: inherit;
        transition: background 0.2s;
        position: relative;
    }
    
    .notification-item-dropdown:hover {
        background: #f8fafc;
    }
    
    .notification-item-dropdown.unread {
        background: #fffbeb;
    }
    
    .notification-item-dropdown .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    
    .notification-item-dropdown .notification-content {
        flex: 1;
    }
    
    .notification-item-dropdown .notification-title {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
        margin-bottom: 3px;
    }
    
    .notification-item-dropdown .notification-message {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 5px;
        line-height: 1.4;
    }
    
    .notification-item-dropdown .notification-time {
        color: #94a3b8;
        font-size: 0.75rem;
    }
    
    .notification-item-dropdown .unread-dot {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 8px;
        height: 8px;
        background: #f59e0b;
        border-radius: 50%;
    }
    
    .notification-empty {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }
    
    .notification-empty i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 10px;
    }
`;

document.head.appendChild(style);