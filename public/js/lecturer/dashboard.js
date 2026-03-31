// Lecturer Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
});

function initDashboard() {
    // Add any interactive features here
    initStatsHover();
    initQuickActions();
}

function initStatsHover() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

function initQuickActions() {
    const quickActions = document.querySelectorAll('.quick-action');
    
    quickActions.forEach(action => {
        action.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        action.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Optional: Add tooltips for deadlines
function addDeadlineTooltips() {
    const deadlines = document.querySelectorAll('.deadline-card');
    
    deadlines.forEach(deadline => {
        const dateElement = deadline.querySelector('.deadline-date');
        if (dateElement && dateElement.textContent.includes('Today')) {
            deadline.classList.add('urgent');
        }
    });
}

// Call additional functions if needed
addDeadlineTooltips();