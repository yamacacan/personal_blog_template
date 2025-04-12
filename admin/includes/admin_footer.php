            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const wrapper = document.getElementById('wrapper');
        const sidebarWrapper = document.getElementById('sidebar-wrapper');
        const pageContentWrapper = document.getElementById('page-content-wrapper');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                if (sidebarWrapper.style.width === '250px') {
                    sidebarWrapper.style.width = '0';
                    pageContentWrapper.style.width = '100%';
                } else {
                    sidebarWrapper.style.width = '250px';
                    pageContentWrapper.style.width = 'calc(100% - 250px)';
                }
            });
        }
        
        // Set active sidebar item
        const currentLocation = window.location.pathname;
        const menuItems = document.querySelectorAll('.list-group-item');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (currentLocation.includes(href) && href !== '../index.php') {
                item.classList.add('active');
                item.style.backgroundColor = '#0d6efd';
            }
        });
        
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
        
        // Auto-hide alert messages after 5 seconds
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                // Create fade out effect
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                
                // Remove alert after fade out
                setTimeout(() => {
                    alert.remove();
                }, 1000);
            }, 5000);
        });
    });
    </script>
</body>
</html>