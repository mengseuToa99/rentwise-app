import './theme.js';
import './echo.js';
import './charts.js';
import 'flowbite';

// Global theme toggle function available to Alpine
window.themeToggle = () => ({
    dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggle() {
        this.dark = !this.dark;
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
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
