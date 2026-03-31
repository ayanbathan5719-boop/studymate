// Student Resources JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initResourceFilters();
    initResourceHover();
});

function initResourceFilters() {
    const filterSelect = document.querySelector('.filter-select');
    const searchInput = document.querySelector('.search-input');
    const filterForm = document.querySelector('.filters-form');
    
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }
    
    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    }
}

function initResourceHover() {
    const resourceCards = document.querySelectorAll('.resource-card');
    
    resourceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}