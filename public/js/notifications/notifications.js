// notifications.js

function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    }).catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function deleteNotification(id) {
    if (confirm('Delete this notification?')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        }).catch(error => {
            console.error('Error deleting notification:', error);
        });
    }
}

// Mark all as read functionality
document.addEventListener('DOMContentLoaded', function() {
    const markAllBtn = document.getElementById('markAllRead');
    
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                }
            }).catch(error => {
                console.error('Error marking all as read:', error);
            });
        });
    }
});