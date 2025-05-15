<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeToggle()" x-bind:class="{ 'dark': dark }" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Rentwise') }}</title>

        <!-- Prevent flash of wrong theme -->
        <script>
            // Immediately set theme to prevent flashing
            if (localStorage.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Ensure there's always a theme preference
            if (!localStorage.theme) {
                localStorage.theme = 'light';
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased h-full overflow-hidden">
        {{ $slot }}
        
        <!-- Theme Toggle Button - Fixed to bottom right of screen -->
        <div class="fixed bottom-4 left-4 z-50">
            <button 
                type="button" 
                x-data="themeToggle()"
                @click="toggle()"
                class="p-1.5 rounded-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-800 dark:text-gray-300 transition-all shadow-md"
            >
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="w-4 h-4"
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                >
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
        
        @livewireScripts
        
        <!-- Theme persistence for Livewire navigation -->
        <script>
            // Listen for Livewire navigation to ensure theme persists
            document.addEventListener('livewire:navigated', () => {
                if (localStorage.theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });
        </script>
    </body>
</html>
