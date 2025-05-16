import './theme.js';
import './echo.js';
import './charts.js';
import 'flowbite';

// Global theme toggle function available to Alpine
window.themeToggle = () => ({
    // Default to light mode unless explicitly set to dark
    dark: localStorage.theme === 'dark',
    init() {
        // Force light mode unless explicitly set to dark
        if (localStorage.theme !== 'dark') {
            localStorage.theme = 'light';
            document.documentElement.classList.remove('dark');
            this.dark = false;
        }
        
        // Watch for changes to the dark class on the HTML element
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (this.dark !== isDark) {
                        this.dark = isDark;
                    }
                }
            });
        });
        
        observer.observe(document.documentElement, { attributes: true });
        
        // Listen for storage events (theme changes in other tabs)
        window.addEventListener('storage', (event) => {
            if (event.key === 'theme') {
                this.dark = event.newValue === 'dark';
            }
        });
        
        // Track page navigation
        document.addEventListener('livewire:navigated', () => {
            // Force light mode unless explicitly set to dark  
            if (localStorage.theme !== 'dark') {
                localStorage.theme = 'light';
                document.documentElement.classList.remove('dark');
                this.dark = false;
            }
        });
    },
    toggle() {
        this.dark = !this.dark;
        localStorage.theme = this.dark ? 'dark' : 'light';
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Keep Flux appearance in sync if available
        if (window.$flux && window.$flux.appearance) {
            window.$flux.appearance = this.dark ? 'dark' : 'light';
        }
    }
});

// Check if Alpine is already being loaded by Livewire Flux
document.addEventListener('DOMContentLoaded', () => {
    // Wait for next tick to let Livewire initialize
    setTimeout(() => {
        if (window.Alpine) {
            // Alpine already loaded by Livewire, register our data component
            window.Alpine.data('themeToggle', window.themeToggle);
        }
    }, 0);
});
