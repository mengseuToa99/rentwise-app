// Theme persistence script
(function() {
    // Get the current theme preference
    const currentTheme = localStorage.theme;
    const isDark = currentTheme === 'dark';
    
    // Apply the current theme
    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    // Update Alpine.js if present
    if (window.Alpine) {
        document.querySelectorAll('[x-data]').forEach(el => {
            if (el.__x && el.__x.$data.dark !== undefined) {
                el.__x.$data.dark = isDark;
            }
        });
    }
    
    // Update Flux if present
    if (window.$flux && window.$flux.appearance) {
        window.$flux.appearance = isDark ? 'dark' : 'light';
    }
    
    console.log('Theme set to:', currentTheme);
    
    // Set up navigation listener
    document.addEventListener('livewire:navigated', function() {
        // Get theme preference again (in case it changed)
        const themeAfterNav = localStorage.theme;
        const isDarkAfterNav = themeAfterNav === 'dark';
        
        // Apply the theme
        if (isDarkAfterNav) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Also update Alpine.js components if they exist
        if (window.Alpine) {
            document.querySelectorAll('[x-data]').forEach(el => {
                if (el.__x && el.__x.$data.dark !== undefined) {
                    el.__x.$data.dark = isDarkAfterNav;
                }
            });
        }
        
        console.log('Theme maintained after navigation:', themeAfterNav);
    });
})();
