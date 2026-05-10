/**
 * Theme runtime extras. The synchronous primitive (window.Theme + window.themeToggle)
 * lives in resources/views/partials/theme-init.blade.php so it's available before Alpine boots.
 * No cross-tab sync: each tab manages its own theme.
 */
(function () {
    if (!window.Theme) return;

    function suppressTransitionsBriefly() {
        const root = document.documentElement;
        root.classList.add('no-transitions');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => root.classList.remove('no-transitions'));
        });
    }

    document.addEventListener('livewire:navigated', () => {
        suppressTransitionsBriefly();
        window.Theme.apply();
    });
})();
