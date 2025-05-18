<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="html-root" x-data="themeToggle()" x-bind:class="{ 'dark': dark }">
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
                transition-duration: 150ms;
            }
            /* Disable transitions on page load to prevent flashing */
            .no-transitions * {
                transition: none !important;
            }
            
            /* Hide elements with x-cloak until Alpine initializes */
            [x-cloak] { display: none !important; }
        </style>
        
        <script>
            // Prevent flash during transitions
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
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <x-layouts.app.sidebar preserveSidebar="true" />

            <!-- Main Content -->
            <div class="flex-1 h-screen overflow-y-auto">
                <!-- Mobile Header -->
                <!-- Main content goes here -->
                {{ $slot }}
            </div>
        </div>

        @fluxScripts

        @unless(auth()->user()->roles->contains(function($role) { 
            return strtolower($role->role_name) === 'admin'; 
        }))
            <script>
                window.location.href = '{{ route('dashboard') }}';
            </script>
        @endunless
    </body>
</html>

<style>
    [x-cloak] { display: none !important; }
</style> 