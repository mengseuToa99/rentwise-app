import './theme.js';
import './echo.js';
import './charts.js';
import './khmer-ui-translator.js';
import 'flowbite';

// window.themeToggle is defined synchronously in resources/views/partials/head.blade.php.
// Re-register it as Alpine.data so x-data="themeToggle" (no parens) also works.
document.addEventListener('alpine:init', () => {
    if (window.Alpine && window.themeToggle) {
        window.Alpine.data('themeToggle', window.themeToggle);
    }
});
