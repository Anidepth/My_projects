// DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Fish & Accessories Store application loaded');
    
    // Initialize tooltips, animations, etc.
    initializeApp();
});

// Initialize app
function initializeApp() {
    // Add any global event listeners here
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Form validation can be added here if needed
            console.log('Form submitted:', form.id || 'unnamed form');
        });
    });
}

// Utility: Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Utility: Show notification
function showNotification(message, type = 'info') {
    console.log(`[${type.toUpperCase()}] ${message}`);
    // Can be extended to show visual notifications
}

// Utility: Redirect with delay
function redirect(url, delay = 0) {
    setTimeout(() => {
        window.location.href = url;
    }, delay);
}

// Export for use in other scripts
window.AppUtils = {
    validateEmail,
    showNotification,
    redirect
};
