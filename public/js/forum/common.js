// ===== FORUM COMMON JAVASCRIPT =====
// Shared functions for all forum pages (Student, Lecturer, Admin)

// Flag post with protection (can't flag own post)
function flagPost(postId, postUserId) {
    // Check if user is trying to flag their own post
    if (postUserId == window.currentUserId) {
        alert('You cannot flag your own post.');
        return false;
    }
    
    if (!confirm('Are you sure you want to flag this post as inappropriate?')) {
        return false;
    }
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || window.csrfToken;
    
    fetch(`/forum/${postId}/flag`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ post_id: postId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Post has been flagged for review.');
            // Optionally update UI
            const flagButton = document.querySelector(`.post-card[data-post-id="${postId}"] .btn-flag`);
            if (flagButton) {
                flagButton.disabled = true;
                flagButton.innerHTML = '<i class="fas fa-flag"></i> Flagged';
                flagButton.style.opacity = '0.5';
            }
        } else {
            alert(data.message || 'Error flagging post. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Delete post with protection (can't delete someone else's post)
function deletePost(postId, postUserId) {
    // Check if user is trying to delete someone else's post
    if (postUserId != window.currentUserId) {
        alert('You can only delete your own posts.');
        return false;
    }
    
    if (!confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        return false;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || window.csrfToken;
    
    fetch(`/forum/${postId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Post deleted successfully.');
            // Remove post from DOM or redirect
            const postElement = document.querySelector(`.post-card[data-post-id="${postId}"]`);
            if (postElement) {
                postElement.remove();
            } else {
                window.location.href = '/forum';
            }
        } else {
            alert(data.message || 'Error deleting post. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Delete reply with confirmation
function deleteReply(replyId, replyUserId) {
    // Check if user is trying to delete someone else's reply
    if (replyUserId != window.currentUserId) {
        alert('You can only delete your own replies.');
        return false;
    }
    
    if (!confirm('Are you sure you want to delete this reply?')) {
        return false;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || window.csrfToken;
    
    fetch(`/forum/reply/${replyId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Reply deleted successfully.');
            const replyElement = document.getElementById(`reply-${replyId}`);
            if (replyElement) {
                replyElement.remove();
            }
        } else {
            alert(data.message || 'Error deleting reply. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Quote a reply (for reply forms)
function quoteReply(replyId, authorName, content) {
    const textarea = document.getElementById('replyContent');
    if (textarea) {
        const quotedText = `> **${authorName} said:**\n> ${content.replace(/\n/g, '\n> ')}\n\n`;
        textarea.value = quotedText + textarea.value;
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth' });
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.alert-success, .alert-error').forEach(function(alert) {
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }
        });
    }, 5000);
});