// ===== LECTURER FORUM SHOW JAVASCRIPT =====
// This file contains functions for the lecturer single post view

function togglePin(postId, button) {
    const url = window.togglePinUrl.replace('__POST_ID__', postId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('active');
            const card = document.querySelector('.post-card');
            if (data.is_pinned) {
                card.classList.add('pinned');
                button.innerHTML = '<i class="fas fa-thumbtack"></i> Unpin';
            } else {
                card.classList.remove('pinned');
                button.innerHTML = '<i class="fas fa-thumbtack"></i> Pin';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling pin status');
    });
}

function toggleAnnouncement(postId, button) {
    const url = window.toggleAnnouncementUrl.replace('__POST_ID__', postId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('active');
            const card = document.querySelector('.post-card');
            if (data.is_announcement) {
                card.classList.add('announcement');
                button.innerHTML = '<i class="fas fa-bullhorn"></i> Remove Announcement';
            } else {
                card.classList.remove('announcement');
                button.innerHTML = '<i class="fas fa-bullhorn"></i> Mark as Announcement';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling announcement status');
    });
}