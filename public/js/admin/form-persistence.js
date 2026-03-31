// public/js/admin/form-persistence.js

class FormPersistence {
    constructor() {
        this.forms = document.querySelectorAll('[data-persist="true"]');
        this.init();
    }

    init() {
        this.forms.forEach(form => {
            const formId = form.id || this.generateFormId(form);
            this.loadFormData(form, formId);
            
            // Save data on input change
            form.addEventListener('input', debounce((e) => {
                this.saveFormData(form, formId);
            }, 500));
            
            // Clear saved data on successful submit
            form.addEventListener('submit', () => {
                localStorage.removeItem(`form_${formId}`);
            });
        });
    }

    generateFormId(form) {
        // Generate a unique ID based on form action and method
        const action = form.getAttribute('action') || 'unknown';
        const method = form.getAttribute('method') || 'get';
        const id = `form_${method}_${action.replace(/[^a-z0-9]/gi, '_')}`;
        form.id = id;
        return id;
    }

    saveFormData(form, formId) {
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            // Skip CSRF token and submit buttons
            if (key === '_token' || key.includes('button')) continue;
            data[key] = value;
        }
        
        localStorage.setItem(`form_${formId}`, JSON.stringify(data));
    }

    loadFormData(form, formId) {
        const saved = localStorage.getItem(`form_${formId}`);
        if (!saved) return;
        
        try {
            const data = JSON.parse(saved);
            
            for (let [key, value] of Object.entries(data)) {
                const input = form.querySelector(`[name="${key}"]`);
                if (input && !input.value) { // Only restore if field is empty
                    input.value = value;
                    
                    // Trigger change event for any dependent scripts
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        } catch (e) {
            console.error('Error loading saved form data:', e);
        }
    }

    clearFormData(formId) {
        localStorage.removeItem(`form_${formId}`);
    }
}

// Debounce utility to prevent too many saves
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new FormPersistence();
});