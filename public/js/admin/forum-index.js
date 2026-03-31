// ===== ADMIN FORUM INDEX JAVASCRIPT =====
// This file contains functions for the admin forum management page

function togglePin(postId, button) {
    const url = window.togglePinUrl.replace('__POST_ID__', postId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('active');
            showToast('Post pin status updated successfully', 'success');
            // Optionally reload the page to update badges
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to update pin status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function toggleAnnouncement(postId, button) {
    const url = window.toggleAnnouncementUrl.replace('__POST_ID__', postId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('active');
            showToast('Announcement status updated successfully', 'success');
            // Optionally reload the page to update badges
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to update announcement status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function confirmDelete(postId) {
    // Get the post user ID from the post card
    const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
    const postUserId = postCard ? postCard.getAttribute('data-user-id') : null;
    
    // Check if user is trying to delete their own post (admins can delete anything)
    // Admins have full permission, so we show a warning but allow it
    if (postUserId && postUserId != window.currentUserId) {
        if (!confirm('This post belongs to another user. Are you sure you want to delete it? This action cannot be undone.')) {
            return;
        }
    } else if (!confirm('Are you sure you want to delete this post? This action cannot be undone and will also delete all replies.')) {
        return;
    }
    
    const modal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = window.deleteUrl.replace('__POST_ID__', postId);
    modal.style.display = 'flex';
}

// ===== INLINE REPLY FUNCTIONS WITH TINYMCE AND ATTACHMENTS =====

// Store reply attachments per post
let replySelectedFile = {};
let replySelectedLink = {};

// Toggle reply form visibility (compatible with TinyMCE)
function toggleReplyForm(postId) {
    const form = document.getElementById(`reply-form-${postId}`);
    if (form) {
        const isHidden = form.style.display === 'none' || form.style.display === '';
        form.style.display = isHidden ? 'block' : 'none';
        
        if (isHidden) {
            // Form is being shown - TinyMCE initialization is handled in the Blade script
            // Focus the editor after a small delay
            setTimeout(() => {
                const editor = window.replyEditors ? window.replyEditors[`reply-editor-${postId}`] : null;
                if (editor) {
                    editor.focus();
                } else {
                    const textarea = document.getElementById(`reply-editor-${postId}`);
                    if (textarea) textarea.focus();
                }
            }, 200);
        }
    }
}

// Show file upload area for reply
function showReplyFileUpload(postId) {
    const fileArea = document.getElementById(`reply-file-upload-${postId}`);
    const linkArea = document.getElementById(`reply-link-upload-${postId}`);
    
    if (linkArea) linkArea.style.display = 'none';
    if (fileArea) {
        const isHidden = fileArea.style.display === 'none' || fileArea.style.display === '';
        fileArea.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            const fileInput = document.getElementById(`reply-file-${postId}`);
            if (fileInput) fileInput.click();
        }
    }
}

// Show link upload area for reply
function showReplyLinkUpload(postId) {
    const linkArea = document.getElementById(`reply-link-upload-${postId}`);
    const fileArea = document.getElementById(`reply-file-upload-${postId}`);
    
    if (fileArea) fileArea.style.display = 'none';
    if (linkArea) {
        linkArea.style.display = linkArea.style.display === 'none' ? 'flex' : 'none';
    }
}

// Handle file selection for reply
function setupReplyFileListener(postId) {
    const fileInput = document.getElementById(`reply-file-${postId}`);
    if (fileInput && !fileInput.hasListener) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                replySelectedFile[postId] = file;
                const preview = document.getElementById(`reply-file-preview-${postId}`);
                const fileNameSpan = document.getElementById(`reply-file-name-${postId}`);
                if (fileNameSpan) fileNameSpan.textContent = file.name;
                if (preview) preview.style.display = 'flex';
            }
        });
        fileInput.hasListener = true;
    }
}

// Clear selected file for reply
function clearReplyFile(postId) {
    replySelectedFile[postId] = null;
    const fileInput = document.getElementById(`reply-file-${postId}`);
    const preview = document.getElementById(`reply-file-preview-${postId}`);
    if (fileInput) fileInput.value = '';
    if (preview) preview.style.display = 'none';
    
    const fileUploadDiv = document.getElementById(`reply-file-upload-${postId}`);
    if (fileUploadDiv) fileUploadDiv.style.display = 'none';
}

// Add link to reply
function addReplyLink(postId) {
    const urlInput = document.getElementById(`reply-link-url-${postId}`);
    const titleInput = document.getElementById(`reply-link-title-${postId}`);
    const url = urlInput?.value.trim();
    const title = titleInput?.value.trim();
    
    if (url) {
        replySelectedLink[postId] = { url, title: title || url };
        const linkArea = document.getElementById(`reply-link-upload-${postId}`);
        if (linkArea) linkArea.style.display = 'none';
        showToast('Link added successfully', 'success');
        // Clear inputs
        if (urlInput) urlInput.value = '';
        if (titleInput) titleInput.value = '';
    } else {
        showToast('Please enter a URL', 'error');
    }
}

// Clear reply link
function clearReplyLink(postId) {
    replySelectedLink[postId] = null;
    
    const urlInput = document.getElementById(`reply-link-url-${postId}`);
    const titleInput = document.getElementById(`reply-link-title-${postId}`);
    if (urlInput) urlInput.value = '';
    if (titleInput) titleInput.value = '';
    
    const linkUploadDiv = document.getElementById(`reply-link-upload-${postId}`);
    if (linkUploadDiv) linkUploadDiv.style.display = 'none';
}

// Cancel reply (clear all attachments and content)
function cancelReply(postId) {
    // Clear TinyMCE editor content if it exists
    const editor = window.replyEditors ? window.replyEditors[`reply-editor-${postId}`] : null;
    if (editor) {
        editor.setContent('');
    } else {
        const textarea = document.getElementById(`reply-editor-${postId}`);
        if (textarea) textarea.value = '';
    }
    
    // Clear attachments
    clearReplyFile(postId);
    clearReplyLink(postId);
    
    // Hide reply form
    toggleReplyForm(postId);
}

// Delete a reply (admins can delete any reply)
function deleteReply(replyId, replyUserId) {
    // Check if user can delete this reply (admins can delete any reply)
    if (replyUserId != window.currentUserId && window.currentUserRole !== 'admin') {
        showToast('You can only delete your own replies.', 'error');
        return false;
    }
    
    if (!confirm('Are you sure you want to delete this reply?')) {
        return false;
    }
    
    const url = window.deleteReplyUrl.replace('__REPLY_ID__', replyId);
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove reply from DOM
            const replyElement = document.getElementById(`reply-${replyId}`);
            if (replyElement) {
                replyElement.remove();
            }
            
            // Update reply count (find parent post)
            const postCard = replyElement?.closest('.post-card');
            if (postCard) {
                const postId = postCard.getAttribute('data-post-id');
                const replyCountSpan = document.getElementById(`reply-count-${postId}`);
                if (replyCountSpan) {
                    let currentCount = parseInt(replyCountSpan.textContent) || 0;
                    replyCountSpan.textContent = currentCount - 1;
                }
            }
            
            if (typeof showToast === 'function') {
                showToast('Reply deleted successfully', 'success');
            }
        } else {
            showToast(data.message || 'Error deleting reply', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting reply. Please try again.', 'error');
    });
}

// View resource (file) inline
function viewResource(filePath, title) {
    // Open in new tab for now
    window.open(filePath, '_blank');
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Convert newlines to <br> tags
function nl2br(str) {
    if (!str) return '';
    return str.replace(/\n/g, '<br>');
}

// Setup file listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup file listeners for all reply forms
    document.querySelectorAll('[id^="reply-file-"]').forEach(fileInput => {
        const postId = fileInput.id.replace('reply-file-', '');
        setupReplyFileListener(postId);
    });
    
    // Modal close functionality
    const modal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('cancelDelete');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// ===== NESTED REPLIES FUNCTIONS =====

// Store current parent ID for nested replies
let currentParentId = null;

// Toggle reply form for a specific comment
function toggleReplyToComment(postId, replyId, userName) {
    // Hide any open reply forms first
    const mainReplyForm = document.getElementById(`reply-form-${postId}`);
    const nestedReplyForm = document.getElementById(`reply-to-comment-form-${postId}`);
    
    // Hide main reply form if open
    if (mainReplyForm && mainReplyForm.style.display !== 'none') {
        mainReplyForm.style.display = 'none';
    }
    
    // Show the nested reply form
    if (nestedReplyForm) {
        nestedReplyForm.style.display = 'block';
        // Set the replying-to name
        const replyingToSpan = document.getElementById(`replying-to-name-${postId}`);
        if (replyingToSpan) {
            replyingToSpan.textContent = userName;
        }
        // Store the parent ID for submission
        currentParentId = replyId;
        
        // Focus on the editor after a small delay
        setTimeout(() => {
            const editor = window.replyEditors ? window.replyEditors[`reply-to-editor-${postId}`] : null;
            if (editor) {
                editor.focus();
            } else {
                const textarea = document.getElementById(`reply-to-editor-${postId}`);
                if (textarea) textarea.focus();
            }
        }, 200);
    }
}

// Cancel reply to a specific comment
function cancelReplyToComment(postId) {
    const nestedReplyForm = document.getElementById(`reply-to-comment-form-${postId}`);
    if (nestedReplyForm) {
        nestedReplyForm.style.display = 'none';
        
        // Clear the editor content
        const editor = window.replyEditors ? window.replyEditors[`reply-to-editor-${postId}`] : null;
        if (editor) {
            editor.setContent('');
        } else {
            const textarea = document.getElementById(`reply-to-editor-${postId}`);
            if (textarea) textarea.value = '';
        }
        
        // Clear stored parent ID
        currentParentId = null;
        
        // Clear any attachments for this post
        if (replySelectedFile && replySelectedFile[postId]) {
            delete replySelectedFile[postId];
        }
        if (replySelectedLink && replySelectedLink[postId]) {
            delete replySelectedLink[postId];
        }
        
        // Clear file preview if exists
        const previewDiv = document.getElementById(`reply-file-preview-${postId}`);
        if (previewDiv) previewDiv.style.display = 'none';
        
        // Clear file input
        const fileInput = document.getElementById(`reply-file-${postId}`);
        if (fileInput) fileInput.value = '';
        
        // Clear link inputs
        const urlInput = document.getElementById(`reply-link-url-${postId}`);
        const titleInput = document.getElementById(`reply-link-title-${postId}`);
        if (urlInput) urlInput.value = '';
        if (titleInput) titleInput.value = '';
        
        // Clear nested reply file and link areas
        const nestedFileUpload = document.getElementById(`nested-reply-file-upload-${postId}`);
        const nestedLinkUpload = document.getElementById(`nested-reply-link-upload-${postId}`);
        if (nestedFileUpload) nestedFileUpload.style.display = 'none';
        if (nestedLinkUpload) nestedLinkUpload.style.display = 'none';
        
        // Clear nested reply attachments
        if (window.nestedReplyData && window.nestedReplyData[postId]) {
            delete window.nestedReplyData[postId];
        }
    }
}

// Initialize TinyMCE for nested reply editor
function initReplyEditorForNested(postId) {
    const editorId = `reply-to-editor-${postId}`;
    
    if (window.replyEditors && window.replyEditors[editorId]) {
        return; // Already initialized
    }
    
    tinymce.init({
        selector: `#${editorId}`,
        height: 120,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | bold italic | bullist numlist | link',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('init', function() {
                if (!window.replyEditors) window.replyEditors = {};
                window.replyEditors[editorId] = editor;
            });
        }
    });
}

// Submit a nested reply (reply to a specific comment)
function submitNestedReply(postId) {
    // Get content from TinyMCE editor
    const editor = window.replyEditors ? window.replyEditors[`reply-to-editor-${postId}`] : null;
    let content = '';
    
    if (editor) {
        content = editor.getContent();
    } else {
        const textarea = document.getElementById(`reply-to-editor-${postId}`);
        if (textarea) content = textarea.value;
    }
    
    if (!content || content.trim() === '') {
        showToast('Please write a reply', 'error');
        return;
    }
    
    // Check if we have a parent ID
    if (!currentParentId) {
        showToast('Error: No comment selected to reply to.', 'error');
        return;
    }
    
    // Disable submit button to prevent double submission
    const submitButton = document.querySelector(`#reply-to-comment-form-${postId} .btn-submit-reply`);
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
    
    // Create form data
    const formData = new FormData();
    formData.append('content', content);
    formData.append('parent_id', currentParentId);
    
    // Add attachment if any (from nested reply data)
    if (window.nestedReplyData && window.nestedReplyData[postId]) {
        if (window.nestedReplyData[postId].file) {
            formData.append('attachment', window.nestedReplyData[postId].file);
        }
        if (window.nestedReplyData[postId].linkUrl) {
            formData.append('link_url', window.nestedReplyData[postId].linkUrl);
            if (window.nestedReplyData[postId].linkTitle) {
                formData.append('link_title', window.nestedReplyData[postId].linkTitle);
            }
        }
    }
    
    // Send AJAX request
    const url = window.replyUrl.replace('__POST_ID__', postId);
    
    fetch(url, {
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
            // Clear the editor
            if (editor) {
                editor.setContent('');
            } else {
                const textarea = document.getElementById(`reply-to-editor-${postId}`);
                if (textarea) textarea.value = '';
            }
            
            // Clear attachments
            if (window.nestedReplyData && window.nestedReplyData[postId]) {
                delete window.nestedReplyData[postId];
            }
            
            // Clear the file preview if exists
            const previewDiv = document.getElementById(`nested-reply-file-preview-${postId}`);
            if (previewDiv) previewDiv.style.display = 'none';
            
            // Hide the nested reply form
            cancelReplyToComment(postId);
            
            // Show success toast
            showToast('Reply posted successfully!', 'success');
            
            // Reload the page to show the new nested reply
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error posting reply', 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error posting reply. Please try again.', 'error');
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

// Initialize nested reply editors when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize nested reply editors for all posts
    document.querySelectorAll('[id^="reply-to-editor-"]').forEach(textarea => {
        const postId = textarea.id.replace('reply-to-editor-', '');
        initReplyEditorForNested(postId);
    });
    
    // Initialize nested reply file listeners
    document.querySelectorAll('[id^="nested-reply-file-"]').forEach(fileInput => {
        const postId = fileInput.id.replace('nested-reply-file-', '');
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!window.nestedReplyData) window.nestedReplyData = {};
                if (!window.nestedReplyData[postId]) window.nestedReplyData[postId] = {};
                window.nestedReplyData[postId].file = file;
                
                const previewDiv = document.getElementById(`nested-reply-file-preview-${postId}`);
                const fileNameSpan = document.getElementById(`nested-reply-file-name-${postId}`);
                if (fileNameSpan) fileNameSpan.textContent = file.name;
                if (previewDiv) previewDiv.style.display = 'flex';
            }
        });
    });
    
    // Initialize nested reply link listeners
    window.showNestedReplyFileUpload = function(postId) {
        const fileUploadDiv = document.getElementById(`nested-reply-file-upload-${postId}`);
        const linkUploadDiv = document.getElementById(`nested-reply-link-upload-${postId}`);
        
        if (linkUploadDiv) linkUploadDiv.style.display = 'none';
        if (fileUploadDiv) {
            fileUploadDiv.style.display = fileUploadDiv.style.display === 'none' ? 'block' : 'none';
            if (fileUploadDiv.style.display === 'block') {
                const fileInput = document.getElementById(`nested-reply-file-${postId}`);
                if (fileInput) fileInput.click();
            }
        }
    };
    
    window.showNestedReplyLinkUpload = function(postId) {
        const linkUploadDiv = document.getElementById(`nested-reply-link-upload-${postId}`);
        const fileUploadDiv = document.getElementById(`nested-reply-file-upload-${postId}`);
        
        if (fileUploadDiv) fileUploadDiv.style.display = 'none';
        if (linkUploadDiv) {
            linkUploadDiv.style.display = linkUploadDiv.style.display === 'none' ? 'flex' : 'none';
        }
    };
    
    window.addNestedReplyLink = function(postId) {
        const urlInput = document.getElementById(`nested-reply-link-url-${postId}`);
        const titleInput = document.getElementById(`nested-reply-link-title-${postId}`);
        const url = urlInput?.value.trim();
        const title = titleInput?.value.trim();
        
        if (url) {
            if (!window.nestedReplyData) window.nestedReplyData = {};
            if (!window.nestedReplyData[postId]) window.nestedReplyData[postId] = {};
            window.nestedReplyData[postId].linkUrl = url;
            window.nestedReplyData[postId].linkTitle = title || url;
            
            const linkUploadDiv = document.getElementById(`nested-reply-link-upload-${postId}`);
            if (linkUploadDiv) linkUploadDiv.style.display = 'none';
            showToast('Link added successfully', 'success');
            
            if (urlInput) urlInput.value = '';
            if (titleInput) titleInput.value = '';
        } else {
            showToast('Please enter a URL', 'error');
        }
    };
    
    window.clearNestedReplyFile = function(postId) {
        if (window.nestedReplyData && window.nestedReplyData[postId]) {
            delete window.nestedReplyData[postId].file;
        }
        
        const fileInput = document.getElementById(`nested-reply-file-${postId}`);
        const previewDiv = document.getElementById(`nested-reply-file-preview-${postId}`);
        if (fileInput) fileInput.value = '';
        if (previewDiv) previewDiv.style.display = 'none';
        
        const fileUploadDiv = document.getElementById(`nested-reply-file-upload-${postId}`);
        if (fileUploadDiv) fileUploadDiv.style.display = 'none';
    };
    
    window.clearNestedReplyLink = function(postId) {
        if (window.nestedReplyData && window.nestedReplyData[postId]) {
            delete window.nestedReplyData[postId].linkUrl;
            delete window.nestedReplyData[postId].linkTitle;
        }
        
        const urlInput = document.getElementById(`nested-reply-link-url-${postId}`);
        const titleInput = document.getElementById(`nested-reply-link-title-${postId}`);
        if (urlInput) urlInput.value = '';
        if (titleInput) titleInput.value = '';
        
        const linkUploadDiv = document.getElementById(`nested-reply-link-upload-${postId}`);
        if (linkUploadDiv) linkUploadDiv.style.display = 'none';
    };
});

// ===== LINK PREVIEW FUNCTIONS =====
// Extract and display subtle link previews (YouTube-style)

// Function to extract links from content and generate preview HTML
function extractAndPreviewLinks(content) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    const urls = content.match(urlRegex);
    
    if (!urls) return '';
    
    let previewHtml = '';
    const processedUrls = new Set(); // Avoid duplicate previews
    
    urls.forEach(url => {
        if (processedUrls.has(url)) return;
        processedUrls.add(url);
        
        // Check if it's a YouTube link
        const isYouTube = url.includes('youtube.com') || url.includes('youtu.be');
        const isVideo = url.includes('vimeo.com') || url.includes('dailymotion.com');
        
        // Determine icon and text
        let icon = 'fa-link';
        let text = url.length > 50 ? url.substring(0, 50) + '...' : url;
        
        if (isYouTube) {
            icon = 'fa-youtube-play';
            text = 'Watch on YouTube';
        } else if (isVideo) {
            icon = 'fa-video';
            text = 'Watch Video';
        }
        
        previewHtml += `
            <div class="link-preview-subtle">
                <i class="fab ${icon}"></i>
                <a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer" class="preview-link">
                    ${escapeHtml(text)}
                </a>
            </div>
        `;
    });
    
    return previewHtml;
}

// Process all existing content for link previews when page loads
function processExistingLinkPreviews() {
    // Process all post contents
    document.querySelectorAll('.post-content-full').forEach(container => {
        const content = container.innerHTML;
        if (content && content.match(/(https?:\/\/[^\s]+)/g)) {
            // Check if preview already exists to avoid duplicates
            if (!container.querySelector('.link-preview-subtle')) {
                const previewHtml = extractAndPreviewLinks(content);
                if (previewHtml) {
                    container.insertAdjacentHTML('beforeend', previewHtml);
                }
            }
        }
    });
    
    // Process all reply contents
    document.querySelectorAll('.reply-content').forEach(container => {
        const content = container.innerHTML;
        if (content && content.match(/(https?:\/\/[^\s]+)/g)) {
            // Check if preview already exists to avoid duplicates
            if (!container.querySelector('.link-preview-subtle')) {
                const previewHtml = extractAndPreviewLinks(content);
                if (previewHtml) {
                    container.insertAdjacentHTML('beforeend', previewHtml);
                }
            }
        }
    });
}

// Run link preview processing when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure content is fully loaded
    setTimeout(processExistingLinkPreviews, 100);
});

// ===== EDIT REPLY FUNCTIONS =====
// Allows admins to edit any reply (no time limit)

let currentEditingReplyId = null;

// Edit a reply - replaces content with textarea
function editReply(replyId, button) {
    const contentDiv = document.getElementById(`reply-content-${replyId}`);
    const originalContent = button.getAttribute('data-original') || '';
    
    // Close any other open edit forms
    if (currentEditingReplyId && currentEditingReplyId !== replyId) {
        cancelEdit(currentEditingReplyId);
    }
    
    // Store current content and replace with textarea
    contentDiv.innerHTML = `
        <textarea id="edit-textarea-${replyId}" class="edit-textarea" rows="3">${escapeHtml(originalContent)}</textarea>
        <div class="edit-actions">
            <button class="cancel-edit" onclick="cancelEdit(${replyId})">Cancel</button>
            <button class="save-edit" onclick="saveEdit(${replyId})">Save</button>
        </div>
    `;
    
    currentEditingReplyId = replyId;
    
    // Focus textarea
    const textarea = document.getElementById(`edit-textarea-${replyId}`);
    if (textarea) textarea.focus();
}

// Cancel editing - restore original content
function cancelEdit(replyId) {
    const contentDiv = document.getElementById(`reply-content-${replyId}`);
    // Get original content from the edit button's data attribute
    const originalButton = document.querySelector(`.btn-edit-reply[data-reply-id="${replyId}"]`);
    const originalContent = originalButton ? originalButton.getAttribute('data-original') : '';
    
    // Restore original content
    contentDiv.innerHTML = nl2br(escapeHtml(originalContent));
    
    // Re-add link previews
    if (originalContent && originalContent.match(/(https?:\/\/[^\s]+)/g)) {
        const previewHtml = extractAndPreviewLinks(originalContent);
        if (previewHtml) {
            contentDiv.insertAdjacentHTML('beforeend', previewHtml);
        }
    }
    
    currentEditingReplyId = null;
}

// Save edited reply
function saveEdit(replyId) {
    const textarea = document.getElementById(`edit-textarea-${replyId}`);
    const newContent = textarea ? textarea.value.trim() : '';
    
    if (!newContent) {
        showToast('Reply cannot be empty', 'error');
        return;
    }
    
    const editButton = document.querySelector(`.btn-edit-reply[data-reply-id="${replyId}"]`);
    const originalText = editButton ? editButton.innerHTML : 'Edit';
    
    // Disable edit button while saving
    if (editButton) {
        editButton.disabled = true;
        editButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    }
    
    const url = window.editReplyUrl.replace('__REPLY_ID__', replyId);
    const formData = new FormData();
    formData.append('content', newContent);
    formData.append('_method', 'PUT');
    
    fetch(url, {
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
            const contentDiv = document.getElementById(`reply-content-${replyId}`);
            
            // Update content
            contentDiv.innerHTML = data.content;
            
            // Add edited indicator if not present
            const replyCard = contentDiv.closest('.reply-card');
            const metaDiv = replyCard?.querySelector('.reply-meta');
            if (metaDiv && !metaDiv.querySelector('.reply-edited')) {
                metaDiv.insertAdjacentHTML('beforeend', `<span class="reply-edited" title="Edited ${data.edited_time}">(edited)</span>`);
            }
            
            // Update edit button's data-original attribute
            if (editButton) {
                editButton.setAttribute('data-original', newContent);
            }
            
            currentEditingReplyId = null;
            
            showToast('Reply updated successfully!', 'success');
        } else {
            showToast(data.message || 'Error saving edit', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error saving edit. Please try again.', 'error');
    })
    .finally(() => {
        if (editButton) {
            editButton.disabled = false;
            editButton.innerHTML = originalText;
        }
    });
}

// Note: flagPost() function is now in common.js
// Use it like: flagPost(postId, postUserId)

// Note: showToast() function is now in common.js
// Already being used above

// Note: submitReplyWithEditor() is defined in the Blade script (TinyMCE integration)
// This file now handles attachment management, nested replies, link previews, and edit functionality