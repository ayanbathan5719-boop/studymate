// ===== FORUM CREATE PAGE JAVASCRIPT =====
// This file contains functions for creating a new forum post

// File preview functionality
const attachmentsInput = document.getElementById('attachments');
if (attachmentsInput) {
    attachmentsInput.addEventListener('change', function(e) {
        const preview = document.getElementById('file-preview');
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        
        if (this.files.length > 0) {
            preview.style.display = 'block';
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const li = document.createElement('li');
                li.innerHTML = `
                    <i class="fas ${getFileIcon(file.name)}"></i>
                    ${file.name} (${(file.size / 1024).toFixed(2)} KB)
                `;
                fileList.appendChild(li);
            }
        } else {
            preview.style.display = 'none';
        }
    });
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        pdf: 'fa-file-pdf',
        doc: 'fa-file-word',
        docx: 'fa-file-word',
        xls: 'fa-file-excel',
        xlsx: 'fa-file-excel',
        ppt: 'fa-file-powerpoint',
        pptx: 'fa-file-powerpoint',
        jpg: 'fa-file-image',
        jpeg: 'fa-file-image',
        png: 'fa-file-image',
        gif: 'fa-file-image',
        zip: 'fa-file-archive',
        rar: 'fa-file-archive',
        txt: 'fa-file-alt'
    };
    return icons[ext] || 'fa-file';
}