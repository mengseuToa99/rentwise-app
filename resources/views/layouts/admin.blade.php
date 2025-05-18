<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeToggle()" x-bind:class="{ 'dark': dark }">
    <head>
        @include('partials.head')
        
        <!-- Prevent flash of wrong theme -->
        <script>
            // Immediately set theme based ONLY on user preference, not system preference
            if (localStorage.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // FORCE LIGHT MODE if nothing specifically set
            if (!localStorage.theme) {
                localStorage.theme = 'light'; 
            }
            
            // Debug and fix theme during navigation
            document.addEventListener('livewire:navigated', () => {
                console.log("Navigation occurred, theme:", localStorage.theme);
                // Apply the current theme preference
                const isDark = localStorage.theme === 'dark';
                
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                
                // Sync Alpine.js components
                if (window.Alpine) {
                    document.querySelectorAll('[x-data]').forEach(el => {
                        if (el.__x && el.__x.$data.dark !== undefined) {
                            el.__x.$data.dark = isDark;
                        }
                    });
                }
            });
        </script>
        <style>
            /* Add smooth transitions for theme changes */
            *, *::before, *::after {
                transition-property: color, background-color, border-color, outline-color, fill, stroke;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 200ms;
            }
            /* But disable transitions on page load to prevent flashing */
            .no-transitions * {
                transition: none !important;
            }
        </style>
        <script>
            // Add no-transitions class on load/navigation to prevent flash
            document.documentElement.classList.add('no-transitions');
            window.addEventListener('load', () => {
                setTimeout(() => {
                    document.documentElement.classList.remove('no-transitions');
                }, 10);
            });
            document.addEventListener('livewire:navigated', () => {
                document.documentElement.classList.add('no-transitions');
                setTimeout(() => {
                    document.documentElement.classList.remove('no-transitions');
                }, 10);
            });
        </script>
    </head>
    <body class="min-h-screen bg-white dark:bg-black">
        <!-- Use the common sidebar component -->
        <x-layouts.app.sidebar :preserveSidebar="true">
            <flux:main>
                {{ $slot }}
            </flux:main>
        </x-layouts.app.sidebar>

        @fluxScripts
        @stack('scripts')
    </body>
</html>

<style>
    [x-cloak] { display: none !important; }
</style> 