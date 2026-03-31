// public/js/student/deadlines.js

document.addEventListener('DOMContentLoaded', function() {
    console.log('Deadlines page loaded');
    
    // Add confirmation for accepting deadlines
    const acceptButtons = document.querySelectorAll('.btn-accept');
    acceptButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Populate this deadline into your calendar?')) {
                e.preventDefault();
            }
        });
    });
});