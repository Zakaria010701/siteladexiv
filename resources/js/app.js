import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'

Alpine.plugin(focus)
window.Alpine = Alpine;

// Force light mode and prevent automatic dark mode switching
document.addEventListener('DOMContentLoaded', function() {
    // Remove dark class from html element if it exists
    document.documentElement.classList.remove('dark');

    // Force light theme
    document.documentElement.style.colorScheme = 'light';

    // Override any dark mode detection
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    if (mediaQuery.matches) {
        // If system prefers dark mode, override it
        document.documentElement.classList.remove('dark');
    }

    // Prevent future dark mode changes
    mediaQuery.addEventListener('change', function(e) {
        if (e.matches) {
            // System switched to dark mode, force back to light
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        }
    });

    // Force all Filament theme switchers to light mode
    setTimeout(function() {
        const themeButtons = document.querySelectorAll('[data-theme-switcher-button]');
        themeButtons.forEach(button => {
            if (button.dataset.themeSwitcherButton === 'dark') {
                button.style.display = 'none';
            }
        });
    }, 1000);
});

Alpine.start();
