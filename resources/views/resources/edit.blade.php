/**
 * Resource Edit Page JavaScript
 * Handles file upload preview and interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeFileUpload();
    initializeUnitCodeFilter();
    autoHideAlerts();
});

/**
 * File upload preview functionality
 */
function initializeFileUpload() {
    const fileInput = document.getElementById('file');
    if (!fileInput) return;

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size based on type
            const resourceType = document.querySelector('input[name="type"]')?.value;
            const maxSize = getMaxFileSize(resourceType);
            
            if (file.size > maxSize) {
                showError(`File size exceeds the maximum allowed (${formatFileSize(maxSize)})`);
                this.value = '';
                return;
            }

            // Validate file type
            if (!validateFileType(file, resourceType)) {
                showError(`Invalid file type. Please select a valid ${resourceType} file.`);
                this.value = '';
                return;
            }

            // Show file preview
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = `(${formatFileSize(file.size)})`;
            document.getElementById('filePreview').style.display = 'flex';
            
            // Update upload area text
            const browseSpan = document.querySelector('.file-upload-area p span');
            if (browseSpan) {
                browseSpan.textContent = 'change file';
            }
        }
    });
}

/**
 * Get maximum file size based on resource type
 */
function getMaxFileSize(type) {
    const sizes = {
        'pdf': 50 * 1024 * 1024,      // 50MB
        'video': 200 * 1024 * 1024,    // 200MB
        'document': 50 * 1024 * 1024,  // 50MB
        'default': 50 * 1024 * 1024    // 50MB
    };
    return sizes[type] || sizes.default;
}

/**
 * Validate file type based on resource type
 */
function validateFileType(file, type) {
    const validTypes = {
        'pdf': ['application/pdf'],
        'video': ['video/mp4', 'video/quicktime', 'video/x-msvideo'],
        'document': [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain'
        ]
    };

    const allowedTypes = validTypes[type] || [];
    return allowedTypes.length === 0 || allowedTypes.includes(file.type);
}

/**
 * Format file size for display
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Clear file selection
 */
window.clearFileSelection = function() {
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.value = '';
    }
    
    const filePreview = document.getElementById('filePreview');
    if (filePreview) {
        filePreview.style.display = 'none';
    }
    
    const browseSpan = document.querySelector('.file-upload-area p span');
    if (browseSpan) {
        browseSpan.textContent = 'browse';
    }
};

/**
 * Initialize unit code filter/topics loader
 */
function initializeUnitCodeFilter() {
    const unitCodeSelect = document.getElementById('unit_code');
    if (!unitCodeSelect) return;

    unitCodeSelect.addEventListener('change', function() {
        const unitCode = this.value;
        if (unitCode) {
            loadTopicsForUnit(unitCode);
        }
    });
}

/**
 * Load topics for selected unit via AJAX
 */
function loadTopicsForUnit(unitCode) {
    const topicSelect = document.getElementById('topic_id');
    if (!topicSelect) return;

    // Show loading state
    topicSelect.innerHTML = '<option value="">Loading topics...</option>';
    topicSelect.disabled = true;

    // Fetch topics for this unit
    fetch(`/api/units/${unitCode}/topics`)
        .then(response => response.json())
        .then(data => {
            topicSelect.innerHTML = '<option value="">-- No specific topic --</option>';
            
            if (data.topics && data.topics.length > 0) {
                data.topics.forEach(topic => {
                    const option = document.createElement('option');
                    option.value = topic.id;
                    option.textContent = topic.name;
                    topicSelect.appendChild(option);
                });
            }
            
            topicSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading topics:', error);
            topicSelect.innerHTML = '<option value="">-- No specific topic --</option>';
            topicSelect.disabled = false;
        });
}

/**
 * Auto-hide alerts after 5 seconds
 */
function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert-error');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    }
}

/**
 * Show error message (can be enhanced to use a toast notification)
 */
function showError(message) {
    // You can implement a toast notification here
    // For now, we'll use console.error
    console.error(message);
    
    // Optionally, you could create a temporary error display
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert-error';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <div class="error-content">${message}</div>
    `;
    
    const container = document.querySelector('.resource-edit-container');
    if (container) {
        container.insertBefore(errorDiv, container.firstChild);
        
        setTimeout(() => {
            errorDiv.style.transition = 'opacity 0.5s ease';
            errorDiv.style.opacity = '0';
            setTimeout(() => {
                errorDiv.remove();
            }, 500);
        }, 5000);
    }
}

// Form validation before submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editResourceForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const title = document.getElementById('title')?.value.trim();
            const unitCode = document.getElementById('unit_code')?.value;
            
            if (!title) {
                e.preventDefault();
                showError('Please enter a resource title');
                return false;
            }
            
            if (!unitCode) {
                e.preventDefault();
                showError('Please select a unit');
                return false;
            }
            
            return true;
        });
    }
});