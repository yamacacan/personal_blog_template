            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="sticky-footer">
        <div class="container">
            <div class="copyright text-center">
                <span>Copyright &copy; <?php echo date('Y'); ?> Blog Admin</span>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Toggle sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const wrapper = document.getElementById('wrapper');
        const sidebarWrapper = document.getElementById('sidebar-wrapper');
        const pageContentWrapper = document.getElementById('page-content-wrapper');
        
        // Check local storage for sidebar state
        if (localStorage.getItem('sidebarClosed') === 'true') {
            sidebarWrapper.style.width = '0px';
            sidebarWrapper.style.overflow = 'hidden';
            pageContentWrapper.style.width = '100%';
        }
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                
                if (window.innerWidth < 768) {
                    if (sidebarWrapper.classList.contains('active')) {
                        sidebarWrapper.classList.remove('active');
                        sidebarWrapper.style.overflow = 'hidden';
                        pageContentWrapper.style.width = '100%';
                        localStorage.setItem('sidebarClosed', 'true');
                    } else {
                        sidebarWrapper.classList.add('active');
                        sidebarWrapper.style.overflow = 'visible';
                        pageContentWrapper.style.width = '100%';
                        localStorage.setItem('sidebarClosed', 'false');
                    }
                } else {
                    if (sidebarWrapper.style.width === '0px') {
                        sidebarWrapper.style.width = '250px';
                        sidebarWrapper.style.overflow = 'visible';
                        pageContentWrapper.style.width = 'calc(100% - 250px)';
                        localStorage.setItem('sidebarClosed', 'false');
                    } else {
                        sidebarWrapper.style.width = '0px';
                        sidebarWrapper.style.overflow = 'hidden';
                        pageContentWrapper.style.width = '100%';
                        localStorage.setItem('sidebarClosed', 'true');
                    }
                }
            });
        }
        
        // Set active sidebar item
        const currentLocation = window.location.pathname;
        const menuItems = document.querySelectorAll('.list-group-item');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (currentLocation.endsWith(href) || 
                (href !== 'index.php' && currentLocation.includes(href))) {
                item.classList.add('active');
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