// ===== STUDENT FORUM JAVASCRIPT =====
// This file contains student-specific forum functions

// ===== GLOBAL VARIABLES =====
let selectedFile = null;
let selectedLink = null;
let replySelectedFile = {};
let replySelectedLink = {};
let currentParentId = null;
let currentEditingReplyId = null;

// ===== INLINE POST FUNCTIONS =====
// Note: TinyMCE is initialized in the Blade file for the post content
// These functions work with TinyMCE

function getPostContent() {
    // Check if TinyMCE is initialized for post content
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get('postContent');
        if (editor) {
            const content = editor.getContent().trim();
            // Check if content is empty (just empty tags)
            if (content === '' || content === '<p></p>' || content === '<p><br></p>' || content === '<p>&nbsp;</p>') {
                return '';
            }
            return content;
        }
    }
    // Fallback to textarea
    const textarea = document.getElementById('postContent');
    return textarea ? textarea.value.trim() : '';
}

function showFileUpload() {
    const fileArea = document.getElementById('fileUploadArea');
    const linkArea = document.getElementById('linkUploadArea');
    
    if (linkArea) linkArea.style.display = 'none';
    if (fileArea) fileArea.style.display = fileArea.style.display === 'none' ? 'block' : 'none';
    if (fileArea && fileArea.style.display === 'block') {
        const fileInput = document.getElementById('postFile');
        if (fileInput) fileInput.click();
    }
}

function showLinkUpload() {
    const linkArea = document.getElementById('linkUploadArea');
    const fileArea = document.getElementById('fileUploadArea');
    
    if (fileArea) fileArea.style.display = 'none';
    if (linkArea) linkArea.style.display = linkArea.style.display === 'none' ? 'flex' : 'none';
}

const postFileInput = document.getElementById('postFile');
if (postFileInput) {
    postFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB');
                this.value = '';
                return;
            }
            selectedFile = file;
            const fileNameSpan = document.getElementById('selectedFileName');
            const previewDiv = document.getElementById('selectedFilePreview');
            if (fileNameSpan) fileNameSpan.textContent = file.name;
            if (previewDiv) previewDiv.style.display = 'flex';
        }
    });
}

function clearSelectedFile() {
    selectedFile = null;
    const fileInput = document.getElementById('postFile');
    const previewDiv = document.getElementById('selectedFilePreview');
    if (fileInput) fileInput.value = '';
    if (previewDiv) previewDiv.style.display = 'none';
}

function addLink() {
    const urlInput = document.getElementById('postLink');
    const titleInput = document.getElementById('linkTitle');
    const url = urlInput ? urlInput.value.trim() : '';
    const title = titleInput ? titleInput.value.trim() : '';
    
    if (url) {
        // Validate URL
        try {
            new URL(url);
            selectedLink = { url, title: title || url };
            const linkArea = document.getElementById('linkUploadArea');
            if (linkArea) linkArea.style.display = 'none';
            alert('Link added: ' + (title || url));
            if (urlInput) urlInput.value = '';
            if (titleInput) titleInput.value = '';
        } catch (e) {
            alert('Please enter a valid URL (including http:// or https://)');
        }
    } else {
        alert('Please enter a URL');
    }
}

function cancelPost() {
    // Clear TinyMCE content if it exists
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get('postContent');
        if (editor) {
            editor.setContent('');
        }
    }
    
    const textarea = document.getElementById('postContent');
    if (textarea) textarea.value = '';
    
    const postActions = document.getElementById('postActions');
    if (postActions) postActions.style.display = 'none';
    
    selectedFile = null;
    selectedLink = null;
    clearSelectedFile();
    
    const urlInput = document.getElementById('postLink');
    const titleInput = document.getElementById('linkTitle');
    if (urlInput) urlInput.value = '';
    if (titleInput) titleInput.value = '';
    
    const fileArea = document.getElementById('fileUploadArea');
    const linkArea = document.getElementById('linkUploadArea');
    if (fileArea) fileArea.style.display = 'none';
    if (linkArea) linkArea.style.display = 'none';
    
    // Reset checkboxes
    const announcementCheckbox = document.getElementById('isAnnouncement');
    const pinnedCheckbox = document.getElementById('isPinned');
    if (announcementCheckbox) announcementCheckbox.checked = false;
    if (pinnedCheckbox) pinnedCheckbox.checked = false;
}

function submitPost() {
    const content = getPostContent();
    const unitSelect = document.getElementById('postUnit');
    const unitCode = unitSelect ? unitSelect.value : '';
    
    if (!unitCode) {
        alert('Please select a unit first');
        if (unitSelect) unitSelect.focus();
        return;
    }
    
    if (!content) {
        alert('Please write a message');
        // Focus TinyMCE editor
        if (typeof tinymce !== 'undefined') {
            const editor = tinymce.get('postContent');
            if (editor) editor.focus();
        } else {
            const textarea = document.getElementById('postContent');
            if (textarea) textarea.focus();
        }
        return;
    }
    
    const postButton = document.querySelector('.btn-post');
    if (postButton) {
        postButton.disabled = true;
        postButton.textContent = 'Posting...';
    }
    
    const formData = new FormData();
    formData.append('content', content);
    formData.append('unit_code', unitCode);
    
    const announcementCheckbox = document.getElementById('isAnnouncement');
    const pinnedCheckbox = document.getElementById('isPinned');
    formData.append('is_announcement', announcementCheckbox && announcementCheckbox.checked ? 1 : 0);
    formData.append('is_pinned', pinnedCheckbox && pinnedCheckbox.checked ? 1 : 0);
    
    if (selectedFile) {
        formData.append('attachment', selectedFile);
    }
    if (selectedLink) {
        formData.append('link_url', selectedLink.url);
        formData.append('link_title', selectedLink.title);
    }
    
    fetch(window.forumStoreUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error creating post');
            if (postButton) {
                postButton.disabled = false;
                postButton.textContent = 'Post';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error creating post. Please try again.');
        if (postButton) {
            postButton.disabled = false;
            postButton.textContent = 'Post';
        }
    });
}

// ===== INLINE REPLY FUNCTIONS =====

function toggleReplyForm(postId) {
    const form = document.getElementById(`reply-form-${postId}`);
    if (form) {
        const isHidden = form.style.display === 'none' || form.style.display === '';
        form.style.display = isHidden ? 'block' : 'none';
        
        if (isHidden) {
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

function showReplyLinkUpload(postId) {
    const linkArea = document.getElementById(`reply-link-upload-${postId}`);
    const fileArea = document.getElementById(`reply-file-upload-${postId}`);
    
    if (fileArea) fileArea.style.display = 'none';
    if (linkArea) {
        linkArea.style.display = linkArea.style.display === 'none' ? 'flex' : 'none';
    }
}

function setupReplyFileListener(postId) {
    const fileInput = document.getElementById(`reply-file-${postId}`);
    if (fileInput && !fileInput.hasListener) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    this.value = '';
                    return;
                }
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

function clearReplyFile(postId) {
    delete replySelectedFile[postId];
    const fileInput = document.getElementById(`reply-file-${postId}`);
    const preview = document.getElementById(`reply-file-preview-${postId}`);
    if (fileInput) fileInput.value = '';
    if (preview) preview.style.display = 'none';
    
    const fileUploadDiv = document.getElementById(`reply-file-upload-${postId}`);
    if (fileUploadDiv) fileUploadDiv.style.display = 'none';
}

function addReplyLink(postId) {
    const urlInput = document.getElementById(`reply-link-url-${postId}`);
    const titleInput = document.getElementById(`reply-link-title-${postId}`);
    const url = urlInput ? urlInput.value.trim() : '';
    const title = titleInput ? titleInput.value.trim() : '';
    
    if (url) {
        // Validate URL
        try {
            new URL(url);
            replySelectedLink[postId] = { url, title: title || url };
            const linkArea = document.getElementById(`reply-link-upload-${postId}`);
            if (linkArea) linkArea.style.display = 'none';
            alert('Link added: ' + (title || url));
            if (urlInput) urlInput.value = '';
            if (titleInput) titleInput.value = '';
        } catch (e) {
            alert('Please enter a valid URL (including http:// or https://)');
        }
    } else {
        alert('Please enter a URL');
    }
}

function clearReplyLink(postId) {
    delete replySelectedLink[postId];
    
    const urlInput = document.getElementById(`reply-link-url-${postId}`);
    const titleInput = document.getElementById(`reply-link-title-${postId}`);
    if (urlInput) urlInput.value = '';
    if (titleInput) titleInput.value = '';
    
    const linkUploadDiv = document.getElementById(`reply-link-upload-${postId}`);
    if (linkUploadDiv) linkUploadDiv.style.display = 'none';
}

function cancelReply(postId) {
    const editor = window.replyEditors ? window.replyEditors[`reply-editor-${postId}`] : null;
    if (editor) {
        editor.setContent('');
    } else {
        const textarea = document.getElementById(`reply-editor-${postId}`);
        if (textarea) textarea.value = '';
    }
    
    clearReplyFile(postId);
    clearReplyLink(postId);
    toggleReplyForm(postId);
}

function submitReplyWithEditor(postId) {
    const editor = window.replyEditors ? window.replyEditors[`reply-editor-${postId}`] : null;
    let content = '';
    
    if (editor) {
        content = editor.getContent();
    } else {
        const textarea = document.getElementById(`reply-editor-${postId}`);
        if (textarea) content = textarea.value;
    }
    
    // Check if content is empty (including TinyMCE empty states)
    if (!content || content.trim() === '' || content === '<p></p>' || content === '<p><br></p>' || content === '<p>&nbsp;</p>') {
        alert('Please enter a reply.');
        return;
    }
    
    const submitButton = document.querySelector(`#reply-form-${postId} .btn-submit-reply`);
    const originalText = submitButton ? submitButton.innerHTML : 'Post Reply';
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
    }
    
    const formData = new FormData();
    formData.append('content', content);
    
    if (replySelectedFile[postId]) {
        formData.append('attachment', replySelectedFile[postId]);
    }
    if (replySelectedLink[postId]) {
        formData.append('link_url', replySelectedLink[postId].url);
        formData.append('link_title', replySelectedLink[postId].title);
    }
    
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
            // Clear the editor content
            if (editor) {
                editor.setContent('');
            }
            
            // Clear attachments
            clearReplyFile(postId);
            clearReplyLink(postId);
            
            // Close the reply form
            toggleReplyForm(postId);
            
            // RELOAD THE PAGE TO SHOW THE NEW REPLY
            location.reload();
        } else {
            alert(data.message || 'Failed to post reply.');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while posting your reply.');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    });
}

function deleteReply(replyId, replyUserId) {
    if (replyUserId != window.currentUserId) {
        alert('You can only delete your own replies.');
        return false;
    }
    
    if (!confirm('Are you sure you want to delete this reply? This action cannot be undone.')) {
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
            location.reload();
        } else {
            alert(data.message || 'Error deleting reply');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting reply. Please try again.');
    });
}

// ===== NESTED REPLIES FUNCTIONS =====

function toggleReplyToComment(postId, replyId, userName) {
    const mainReplyForm = document.getElementById(`reply-form-${postId}`);
    const nestedReplyForm = document.getElementById(`reply-to-comment-form-${postId}`);
    
    if (mainReplyForm && mainReplyForm.style.display !== 'none') {
        mainReplyForm.style.display = 'none';
    }
    
    if (nestedReplyForm) {
        nestedReplyForm.style.display = 'block';
        const replyingToSpan = document.getElementById(`replying-to-name-${postId}`);
        if (replyingToSpan) {
            replyingToSpan.textContent = userName;
        }
        currentParentId = replyId;
        
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

function cancelReplyToComment(postId) {
    const nestedReplyForm = document.getElementById(`reply-to-comment-form-${postId}`);
    if (nestedReplyForm) {
        nestedReplyForm.style.display = 'none';
        
        const editor = window.replyEditors ? window.replyEditors[`reply-to-editor-${postId}`] : null;
        if (editor) {
            editor.setContent('');
        } else {
            const textarea = document.getElementById(`reply-to-editor-${postId}`);
            if (textarea) textarea.value = '';
        }
        
        currentParentId = null;
        
        if (replySelectedFile && replySelectedFile[postId]) {
            delete replySelectedFile[postId];
        }
        if (replySelectedLink && replySelectedLink[postId]) {
            delete replySelectedLink[postId];
        }
        
        const previewDiv = document.getElementById(`reply-file-preview-${postId}`);
        if (previewDiv) previewDiv.style.display = 'none';
        
        const fileInput = document.getElementById(`reply-file-${postId}`);
        if (fileInput) fileInput.value = '';
        
        const urlInput = document.getElementById(`reply-link-url-${postId}`);
        const titleInput = document.getElementById(`reply-link-title-${postId}`);
        if (urlInput) urlInput.value = '';
        if (titleInput) titleInput.value = '';
    }
}

function initReplyEditorForNested(postId) {
    const editorId = `reply-to-editor-${postId}`;
    
    if (window.replyEditors && window.replyEditors[editorId]) {
        return;
    }
    
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: `#${editorId}`,
            height: 120,
            menubar: false,
            plugins: ['advlist', 'autolink', 'lists', 'link', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount'],
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
}

function showNestedReplyFileUpload(postId) {
    showReplyFileUpload(postId);
}

function showNestedReplyLinkUpload(postId) {
    showReplyLinkUpload(postId);
}

function addNestedReplyLink(postId) {
    addReplyLink(postId);
}

function clearNestedReplyFile(postId) {
    clearReplyFile(postId);
}

function clearNestedReplyLink(postId) {
    clearReplyLink(postId);
}

function submitNestedReply(postId) {
    const editor = window.replyEditors ? window.replyEditors[`reply-to-editor-${postId}`] : null;
    let content = '';
    
    if (editor) {
        content = editor.getContent();
    } else {
        const textarea = document.getElementById(`reply-to-editor-${postId}`);
        if (textarea) content = textarea.value;
    }
    
    // Check if content is empty (including TinyMCE empty states)
    if (!content || content.trim() === '' || content === '<p></p>' || content === '<p><br></p>' || content === '<p>&nbsp;</p>') {
        alert('Please write a reply');
        return;
    }
    
    if (!currentParentId) {
        alert('Error: No comment selected to reply to.');
        return;
    }
    
    const submitButton = document.querySelector(`#reply-to-comment-form-${postId} .btn-submit-reply`);
    const originalText = submitButton ? submitButton.innerHTML : 'Post Reply';
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
        
        // Disable all buttons in the form to prevent double submission
        document.querySelectorAll(`#reply-to-comment-form-${postId} button`).forEach(btn => {
            if (btn !== submitButton) btn.disabled = true;
        });
    }
    
    const formData = new FormData();
    formData.append('content', content);
    formData.append('parent_id', currentParentId);
    
    if (replySelectedFile && replySelectedFile[postId]) {
        formData.append('attachment', replySelectedFile[postId]);
    }
    if (replySelectedLink && replySelectedLink[postId]) {
        formData.append('link_url', replySelectedLink[postId].url);
        formData.append('link_title', replySelectedLink[postId].title);
    }
    
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
            if (editor) {
                editor.setContent('');
            }
            
            if (replySelectedFile && replySelectedFile[postId]) {
                delete replySelectedFile[postId];
            }
            if (replySelectedLink && replySelectedLink[postId]) {
                delete replySelectedLink[postId];
            }
            
            const previewDiv = document.getElementById(`reply-file-preview-${postId}`);
            if (previewDiv) previewDiv.style.display = 'none';
            
            const fileInput = document.getElementById(`reply-file-${postId}`);
            if (fileInput) fileInput.value = '';
            
            cancelReplyToComment(postId);
            
            // RELOAD THE PAGE TO SHOW THE NEW NESTED REPLY
            location.reload();
        } else {
            alert(data.message || 'Error posting reply');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                
                // Re-enable other buttons
                document.querySelectorAll(`#reply-to-comment-form-${postId} button`).forEach(btn => {
                    btn.disabled = false;
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error posting reply. Please try again.');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
            
            // Re-enable other buttons
            document.querySelectorAll(`#reply-to-comment-form-${postId} button`).forEach(btn => {
                btn.disabled = false;
            });
        }
    });
}

// ===== EDIT REPLY FUNCTIONS =====

function editReply(replyId, button) {
    const contentDiv = document.getElementById(`reply-content-${replyId}`);
    const originalContent = button.getAttribute('data-original') || '';
    
    if (currentEditingReplyId && currentEditingReplyId !== replyId) {
        cancelEdit(currentEditingReplyId);
    }
    
    if (contentDiv) {
        contentDiv.innerHTML = `
            <textarea id="edit-textarea-${replyId}" class="edit-textarea" rows="3">${escapeHtml(originalContent)}</textarea>
            <div class="edit-actions">
                <button class="cancel-edit" onclick="cancelEdit(${replyId})">Cancel</button>
                <button class="save-edit" onclick="saveEdit(${replyId})">Save</button>
            </div>
        `;
    }
    
    currentEditingReplyId = replyId;
    
    const textarea = document.getElementById(`edit-textarea-${replyId}`);
    if (textarea) textarea.focus();
}

function cancelEdit(replyId) {
    const contentDiv = document.getElementById(`reply-content-${replyId}`);
    const originalButton = document.querySelector(`.btn-edit-reply[data-reply-id="${replyId}"]`);
    const originalContent = originalButton ? originalButton.getAttribute('data-original') : '';
    
    if (contentDiv) {
        contentDiv.innerHTML = nl2br(escapeHtml(originalContent));
        
        if (originalContent && originalContent.match(/(https?:\/\/[^\s]+)/g)) {
            const previewHtml = extractAndPreviewLinks(originalContent);
            if (previewHtml) {
                contentDiv.insertAdjacentHTML('beforeend', previewHtml);
            }
        }
    }
    
    currentEditingReplyId = null;
}

function saveEdit(replyId) {
    const textarea = document.getElementById(`edit-textarea-${replyId}`);
    const newContent = textarea ? textarea.value.trim() : '';
    
    if (!newContent) {
        alert('Reply cannot be empty');
        return;
    }
    
    const editButton = document.querySelector(`.btn-edit-reply[data-reply-id="${replyId}"]`);
    const originalText = editButton ? editButton.innerHTML : 'Edit';
    
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
            location.reload();
        } else {
            alert(data.message || 'Error saving edit');
            if (editButton) {
                editButton.disabled = false;
                editButton.innerHTML = originalText;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving edit. Please try again.');
        if (editButton) {
            editButton.disabled = false;
            editButton.innerHTML = originalText;
        }
    });
}

// ===== LINK PREVIEW FUNCTIONS =====

function extractAndPreviewLinks(content) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    const urls = content.match(urlRegex);
    
    if (!urls) return '';
    
    let previewHtml = '';
    const processedUrls = new Set();
    
    urls.forEach(url => {
        if (processedUrls.has(url)) return;
        processedUrls.add(url);
        
        const isYouTube = url.includes('youtube.com') || url.includes('youtu.be');
        const isVideo = url.includes('vimeo.com') || url.includes('dailymotion.com');
        
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

// ===== HELPER FUNCTIONS =====

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function nl2br(str) {
    if (!str) return '';
    return str.replace(/\n/g, '<br>');
}

function viewResource(filePath, title) {
    window.open(filePath, '_blank');
}

// ===== DOM EVENT LISTENERS =====

document.addEventListener('DOMContentLoaded', function() {
    // Initialize window.replyEditors object if it doesn't exist
    if (!window.replyEditors) window.replyEditors = {};
    
    // Initialize reply editors with TinyMCE
    if (typeof tinymce !== 'undefined') {
        // Initialize main reply editors
        document.querySelectorAll('[id^="reply-editor-"]').forEach(textarea => {
            const postId = textarea.id.replace('reply-editor-', '');
            
            // Only initialize if not already initialized
            if (!window.replyEditors[`reply-editor-${postId}`]) {
                tinymce.init({
                    selector: `#reply-editor-${postId}`,
                    height: 120,
                    menubar: false,
                    plugins: ['advlist', 'autolink', 'lists', 'link', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount'],
                    toolbar: 'undo redo | bold italic | bullist numlist | link',
                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; }',
                    setup: function(editor) {
                        editor.on('init', function() {
                            window.replyEditors[`reply-editor-${postId}`] = editor;
                        });
                    }
                });
            }
        });
        
        // Initialize nested reply editors
        document.querySelectorAll('[id^="reply-to-editor-"]').forEach(textarea => {
            const postId = textarea.id.replace('reply-to-editor-', '');
            initReplyEditorForNested(postId);
        });
    }
    
    // Setup file listeners for all reply forms
    document.querySelectorAll('[id^="reply-file-"]').forEach(fileInput => {
        const postId = fileInput.id.replace('reply-file-', '');
        setupReplyFileListener(postId);
    });
    
    // Process existing link previews
    setTimeout(function() {
        document.querySelectorAll('.post-content-full, .reply-content').forEach(container => {
            const content = container.innerHTML;
            if (content && content.match(/(https?:\/\/[^\s]+)/g)) {
                if (!container.querySelector('.link-preview-subtle')) {
                    const previewHtml = extractAndPreviewLinks(content);
                    if (previewHtml) {
                        container.insertAdjacentHTML('beforeend', previewHtml);
                    }
                }
            }
        });
    }, 100);
    
    // Handle post input actions with TinyMCE (if it exists)
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get('postContent');
        if (editor) {
            editor.on('focus', function() {
                const postActions = document.getElementById('postActions');
                if (postActions) postActions.style.display = 'flex';
            });
            
            editor.on('keyup', function() {
                const content = editor.getContent().trim();
                const postActions = document.getElementById('postActions');
                if (postActions) {
                    if (content === '' || content === '<p></p>' || content === '<p><br></p>' || content === '<p>&nbsp;</p>') {
                        postActions.style.display = 'none';
                    } else {
                        postActions.style.display = 'flex';
                    }
                }
            });
            
            // Also handle blur event to clean up empty content
            editor.on('blur', function() {
                const content = editor.getContent().trim();
                const postActions = document.getElementById('postActions');
                if (postActions && (content === '' || content === '<p></p>' || content === '<p><br></p>' || content === '<p>&nbsp;</p>')) {
                    postActions.style.display = 'none';
                }
            });
        }
    }
});