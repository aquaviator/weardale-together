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

    var menu = document.getElementById('primary-menu') || container.getElementsByTagName('ul')[0];

    // Hide button if menu is missing or empty.
    if (!menu) {
        button.style.display = 'none';
        return;
    }

    // Toggle menu state
    function toggleMenu() {
        var isToggled = container.classList.toggle('toggled');
        button.setAttribute('aria-expanded', isToggled ? 'true' : 'false');
    }

    button.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
    });

    // Close menu when clicking outside on mobile
    document.addEventListener('click', function(event) {
        var isClickInside = container.contains(event.target);
        if (!isClickInside && container.classList.contains('toggled')) {
            container.classList.remove('toggled');
            button.setAttribute('aria-expanded', 'false');
        }
    });

    // Close menu when Escape key is pressed and restore focus to toggle button
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' || event.keyCode === 27) {
            if (container.classList.contains('toggled')) {
                container.classList.remove('toggled');
                button.setAttribute('aria-expanded', 'false');
                button.focus();
            }
        }
    });

    // Close menu when a link inside the menu is selected/clicked
    var menuLinks = menu.getElementsByTagName('a');
    for (var i = 0; i < menuLinks.length; i++) {
        menuLinks[i].addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                container.classList.remove('toggled');
                button.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Reset classes and attributes cleanly when resizing back to desktop width
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            container.classList.remove('toggled');
            button.setAttribute('aria-expanded', 'false');
        }
    });
});

