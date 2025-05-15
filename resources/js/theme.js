/**
 * Theme Manager - Handles dark/light mode preferences across the application
 */

// Initialize the theme manager
function initTheme() {
    // Set default theme to light
    if (!localStorage.theme || localStorage.theme !== 'dark') {
        localStorage.theme = 'light';
    }
    
    // Apply theme preference from localStorage - respect user's choice
    function applyTheme() {
        if (localStorage.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateThemeIcons();
    }

    // Toggle between light and dark themes
    window.toggleTheme = function() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        updateThemeIcons();
        return document.documentElement.classList.contains('dark');
    };

    // Update all theme toggle icons on the page
    function updateThemeIcons() {
        const themeIcons = document.querySelectorAll('.theme-icon');
        const isDark = document.documentElement.classList.contains('dark');
        
        themeIcons.forEach(icon => {
            if (isDark) {
                // Moon icon
                icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>';
            } else {
                // Sun icon
                icon.innerHTML = `
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                `;
            }
        });
    }

    // Setup theme toggle buttons
    function setupThemeToggles() {
        const themeToggles = document.querySelectorAll('[data-theme-toggle]');
        
        themeToggles.forEach(toggle => {
            // Remove any existing listeners to prevent duplicates
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            newToggle.addEventListener('click', (e) => {
                e.preventDefault();
                window.toggleTheme();
            });
        });
    }

    // Listen for system theme changes if no preference is set
    if (!('theme' in localStorage)) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
            applyTheme();
        });
    }

    // Apply the theme immediately
    applyTheme();
    
    // Ensure there's always a theme preference set
    if (!localStorage.theme) {
        localStorage.theme = 'light';
    }
    
    // Setup event listeners after DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupThemeToggles);
    } else {
        setupThemeToggles();
    }

    // Livewire event listeners
    document.addEventListener('livewire:init', () => {
        // Handle Livewire page loads/navigations
        Livewire.hook('commit.before', () => {
            // This happens before the page updates
            applyTheme();
        });
        
        Livewire.hook('commit.after', () => {
            // This happens after the page content updates
            setupThemeToggles();
            updateThemeIcons();
        });
        
        // Handle Livewire navigations
        document.addEventListener('livewire:navigating', () => {
            // Before navigation
            applyTheme();
        });
        
        document.addEventListener('livewire:navigated', () => {
            // After navigation
            setupThemeToggles();
            updateThemeIcons();
            
            // Re-sync Alpine.js components with current theme state
            if (window.Alpine) {
                setTimeout(() => {
                    const isDark = document.documentElement.classList.contains('dark');
                    document.querySelectorAll('[x-data*="isDark"]').forEach(el => {
                        if (el.__x) {
                            el.__x.$data.isDark = isDark;
                        }
                    });
                }, 0);
            }
        });
    });
    
    // Handle regular page navigations - wire:navigate adds this event
    document.addEventListener('turbolinks:load', () => {
        setupThemeToggles();
        updateThemeIcons();
    });
    
    // Fallback for any other page load events
    window.addEventListener('load', () => {
        setupThemeToggles();
        updateThemeIcons();
    });

    // Ensure light mode is the default unless explicitly set to dark
    function ensureLightModeDefault() {
        if (!localStorage.theme) {
            localStorage.theme = 'light';
            document.documentElement.classList.remove('dark');
        }
    }
    
    // Run on initial load
    ensureLightModeDefault();
    
    // Run on all navigation events
    document.addEventListener('livewire:navigated', ensureLightModeDefault);
    document.addEventListener('turbolinks:load', ensureLightModeDefault);
    window.addEventListener('DOMContentLoaded', ensureLightModeDefault);
    window.addEventListener('load', ensureLightModeDefault);
}

// Initialize theme system
initTheme(); 