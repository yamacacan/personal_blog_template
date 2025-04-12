// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add active class to current navigation link
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (currentLocation.endsWith(href)) {
            link.classList.add('active');
        }
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
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
    
    // Comment form validation
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            let isValid = true;
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const comment = document.getElementById('comment');
            
            // Simple validation
            if (name.value.trim() === '') {
                markInvalid(name, 'Name is required');
                isValid = false;
            } else {
                markValid(name);
            }
            
            if (email.value.trim() === '') {
                markInvalid(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                markInvalid(email, 'Please enter a valid email');
                isValid = false;
            } else {
                markValid(email);
            }
            
            if (comment.value.trim() === '') {
                markInvalid(comment, 'Comment is required');
                isValid = false;
            } else {
                markValid(comment);
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Admin post form validation
    const postForm = document.getElementById('postForm');
    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            let isValid = true;
            const title = document.getElementById('title');
            const content = document.getElementById('content');
            
            if (title.value.trim() === '') {
                markInvalid(title, 'Title is required');
                isValid = false;
            } else {
                markValid(title);
            }
            
            if (content.value.trim() === '') {
                markInvalid(content, 'Content is required');
                isValid = false;
            } else {
                markValid(content);
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Helper functions
    function markInvalid(element, message) {
        element.classList.add('is-invalid');
        element.classList.remove('is-valid');
        
        // Create or update feedback message
        let feedback = element.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            element.parentNode.insertBefore(feedback, element.nextSibling);
        }
        feedback.textContent = message;
    }
    
    function markValid(element) {
        element.classList.remove('is-invalid');
        element.classList.add('is-valid');
    }
    
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    // Image preview for file uploads
    const imageUpload = document.getElementById('featured_image');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageUpload && imagePreview) {
        imageUpload.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }
}); 