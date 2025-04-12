// jQuery compatibility layer for Bootstrap components
document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality
    const accordionToggles = document.querySelectorAll('.accordion-toggle');
    
    accordionToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-target');
            const target = document.querySelector(targetId);
            
            if (target) {
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    this.classList.add('collapsed');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    target.classList.add('show');
                    this.classList.remove('collapsed');
                    this.setAttribute('aria-expanded', 'true');
                }
            }
        });
    });
    
    // Tab functionality
    const tabToggles = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    tabToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show the target tab pane
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            if (target) {
                target.classList.add('show', 'active');
            }
        });
    });
    
    // Collapse functionality
    const collapseToggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    collapseToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-bs-target') || this.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    this.classList.add('collapsed');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    target.classList.add('show');
                    this.classList.remove('collapsed');
                    this.setAttribute('aria-expanded', 'true');
                }
            }
        });
    });
}); 