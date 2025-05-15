<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<!-- Prevent flash of wrong theme -->
<script>
    // Immediately set theme to prevent flashing
    (function() {
        function applyTheme() {
            if (localStorage.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        
        // Ensure we always have a theme preference set
        if (!localStorage.theme) {
            localStorage.theme = 'light';
        }
        
        // Apply theme now
        applyTheme();
        
        // Re-apply theme on each page navigation
        document.addEventListener('livewire:navigated', applyTheme);
        document.addEventListener('turbolinks:load', applyTheme);
        window.addEventListener('load', applyTheme);
    })();
</script>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
