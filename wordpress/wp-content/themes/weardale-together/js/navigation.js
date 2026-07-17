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

    // Toggle mobile main menu state
    function toggleMenu() {
        var isToggled = container.classList.toggle('toggled');
        button.setAttribute('aria-expanded', isToggled ? 'true' : 'false');
    }

    button.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
    });

    // Close menu and submenus when clicking outside
    document.addEventListener('click', function(event) {
        var isClickInside = container.contains(event.target);
        if (!isClickInside) {
            if (container.classList.contains('toggled')) {
                container.classList.remove('toggled');
                button.setAttribute('aria-expanded', 'false');
            }
            // Reset mobile active states for dropdowns
            var activeItems = container.querySelectorAll('.menu-item-has-children');
            activeItems.forEach(function(item) {
                item.classList.remove('submenu-active');
            });
            var expandedMenus = container.querySelectorAll('.sub-menu.expanded');
            expandedMenus.forEach(function(subMenu) {
                subMenu.classList.remove('expanded');
                var parentLink = subMenu.previousElementSibling;
                if (parentLink) {
                    parentLink.setAttribute('aria-expanded', 'false');
                }
            });
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
            // Also close open keyboard dropdowns
            var parents = container.querySelectorAll('.menu-item-has-children.focus');
            parents.forEach(function(parent) {
                parent.classList.remove('focus');
                var link = parent.querySelector('a');
                if (link) {
                    link.focus();
                }
            });
        }
    });

    // Close mobile menu when a simple link is clicked
    var menuLinks = menu.querySelectorAll('a');
    menuLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            var isParentDropdown = link.parentElement.classList.contains('menu-item-has-children');
            
            if (isParentDropdown && window.innerWidth <= 1120) {
                // On mobile, let's toggle the dropdown sub-menu instead of navigating
                e.preventDefault();
                e.stopPropagation();
                var parentLi = link.parentElement;
                var subMenu = link.nextElementSibling;
                if (subMenu && subMenu.classList.contains('sub-menu')) {
                    var isExpanded = subMenu.classList.toggle('expanded');
                    parentLi.classList.toggle('submenu-active');
                    link.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                }
            } else {
                if (window.innerWidth <= 1120) {
                    container.classList.remove('toggled');
                    button.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });

    // Desktop hover & keyboard focus manager for dropdowns
    var parentItems = container.querySelectorAll('.menu-item-has-children');
    parentItems.forEach(function(parent) {
        var link = parent.querySelector('a');
        if (link) {
            link.setAttribute('aria-haspopup', 'true');
            link.setAttribute('aria-expanded', 'false');
            
            // Focus management
            link.addEventListener('focus', function() {
                parent.classList.add('focus');
                link.setAttribute('aria-expanded', 'true');
            });
        }

        // Submenu links focus tracker
        var subLinks = parent.querySelectorAll('.sub-menu a');
        subLinks.forEach(function(subLink) {
            subLink.addEventListener('focus', function() {
                parent.classList.add('focus');
                if (link) {
                    link.setAttribute('aria-expanded', 'true');
                }
            });

            subLink.addEventListener('blur', function() {
                setTimeout(function() {
                    if (!parent.contains(document.activeElement)) {
                        parent.classList.remove('focus');
                        if (link) {
                            link.setAttribute('aria-expanded', 'false');
                        }
                    }
                }, 20);
            });
        });

        // Blur event on top level link
        if (link) {
            link.addEventListener('blur', function() {
                setTimeout(function() {
                    if (!parent.contains(document.activeElement)) {
                        parent.classList.remove('focus');
                        link.setAttribute('aria-expanded', 'false');
                    }
                }, 20);
            });
        }
    });

    // Reset classes and attributes cleanly when resizing back to desktop width
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1120) {
            container.classList.remove('toggled');
            button.setAttribute('aria-expanded', 'false');
            
            // Clean up mobile submenus
            var subMenus = container.querySelectorAll('.sub-menu');
            subMenus.forEach(function(subMenu) {
                subMenu.classList.remove('expanded');
            });
            var activeItems = container.querySelectorAll('.menu-item-has-children');
            activeItems.forEach(function(item) {
                item.classList.remove('submenu-active');
                var link = item.querySelector('a');
                if (link) {
                    link.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
});

