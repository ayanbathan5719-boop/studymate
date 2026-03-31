// Custom Units JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initColorPicker();
    initIconSelector();
    initProgressPreview();
    initDeleteConfirmation();
});

function initColorPicker() {
    const colorInput = document.getElementById('color');
    const colorValue = document.querySelector('.color-value');
    
    if (colorInput && colorValue) {
        colorInput.addEventListener('input', function() {
            colorValue.value = this.value;
        });
    }
}

function initIconSelector() {
    const iconInput = document.getElementById('icon');
    const iconOptions = document.querySelectorAll('.icon-option');
    
    if (!iconInput || !iconOptions.length) return;
    
    iconOptions.forEach(option => {
        option.addEventListener('click', function() {
            iconOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            iconInput.value = this.dataset.icon;
        });
    });
}

function initProgressPreview() {
    const progressInput = document.getElementById('progress');
    const progressFill = document.getElementById('progressFill');
    const progressPercent = document.getElementById('progressPercent');
    const goalInput = document.getElementById('goal_minutes');
    
    if (!progressInput || !progressFill || !progressPercent) return;
    
    function updateProgressPreview() {
        const progress = parseInt(progressInput.value) || 0;
        const goal = parseInt(goalInput?.value) || 0;
        
        let percentage = 0;
        if (goal > 0) {
            percentage = Math.min(Math.round((progress / goal) * 100), 100);
        }
        
        progressFill.style.width = percentage + '%';
        progressPercent.textContent = percentage + '%';
    }
    
    progressInput.addEventListener('input', updateProgressPreview);
    if (goalInput) {
        goalInput.addEventListener('input', updateProgressPreview);
    }
}

function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('.delete-btn, form button[type="submit"][class*="btn-delete"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
}