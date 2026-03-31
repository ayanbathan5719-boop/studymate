// ===== FORUM SHOW PAGE JAVASCRIPT =====
// This file contains functions for the single post view with threaded replies

// Quote reply function
function quoteReply(replyId, authorName, content) {
    const textarea = document.getElementById('replyContent');
    if (textarea) {
        const quotedText = `> **${authorName} said:**\n> ${content.replace(/\n/g, '\n> ')}\n\n`;
        textarea.value = quotedText + textarea.value;
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth' });
    }
}

// Optional: Add AJAX reply submission (can be added later)
function submitReplyAjax(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const formData = new FormData(form);
    
    fetch(window.replyUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error posting reply');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error posting reply');
    });
}

// Optional: Auto-expand textarea as user types
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('replyContent');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});