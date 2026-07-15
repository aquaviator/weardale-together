/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables keyboard
 * accessibility for the dropdown structures.
 */
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('site-navigation');
    if (!container) return;

    var button = document.getElementById('menu-toggle');
    if (!button) return;

    var menu = container.getElementsByTagName('ul')[0];

    // Hide button if menu is missing or empty.
    if (!menu) {
        button.style.display = 'none';
        return;
    }

    button.addEventListener('click', function() {
        container.classList.toggle('toggled');
        
        var expanded = container.classList.contains('toggled');
        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    });

    // Close menu when clicking outside on mobile
    document.addEventListener('click', function(event) {
        var isClickInside = container.contains(event.target) || button.contains(event.target);
        if (!isClickInside && container.classList.contains('toggled')) {
            container.classList.remove('toggled');
            button.setAttribute('aria-expanded', 'false');
        }
    });
});
