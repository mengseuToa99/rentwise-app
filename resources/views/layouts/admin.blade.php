<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="html-root">
    <head>
        @include('partials.head')
        
        <!-- Direct DOM manipulation theme handling - NO Alpine.js for themes -->
        <script>
            // RESET and FORCE light theme on every page load
            document.addEventListener('DOMContentLoaded', function() {
                // Always reset to light theme
                setLightTheme();
                
                // Set up theme toggle button
                setTimeout(function() {
                    var themeToggleBtn = document.getElementById('theme-toggle-btn');
                    if (themeToggleBtn) {
                        themeToggleBtn.addEventListener('click', function() {
                            toggleTheme();
                        });
                    }
                    
                    // Set initial icon visibility
                    updateThemeIcons();
                }, 100);
            });
            
            // After navigation, force reapply theme
            document.addEventListener('livewire:navigated', function() {
                console.log('Navigation detected, reapplying theme');
                
                // Get current theme from localStorage
                var currentTheme = localStorage.getItem('theme') || 'light';
                console.log('Current theme from localStorage:', currentTheme);
                
                // Always force light theme after navigation (override any other setting)
                if (currentTheme !== 'dark') {
                    setLightTheme();
                } else {
                    setDarkTheme();
                }
                
                // Update icons after a slight delay to ensure DOM is ready
                setTimeout(function() {
                    updateThemeIcons();
                }, 50);
            });
            
            // Force set light theme
            function setLightTheme() {
                console.log('FORCING LIGHT THEME');
                localStorage.setItem('theme', 'light');
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark-mode');
                document.getElementById('html-root').classList.remove('dark');
                updateThemeIcons();
            }
            
            // Force set dark theme
            function setDarkTheme() {
                console.log('Setting dark theme');
                localStorage.setItem('theme', 'dark');
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark-mode');
                document.getElementById('html-root').classList.add('dark');
                updateThemeIcons();
            }
            
            // Toggle between themes
            function toggleTheme() {
                var currentTheme = localStorage.getItem('theme') || 'light';
                console.log('Toggling theme from', currentTheme);
                
                if (currentTheme === 'dark') {
                    setLightTheme();
                } else {
                    setDarkTheme();
                }
            }
            
            // Update icon visibility
            function updateThemeIcons() {
                var currentTheme = localStorage.getItem('theme') || 'light';
                var darkIcon = document.getElementById('dark-icon');
                var lightIcon = document.getElementById('light-icon');
                
                if (!darkIcon || !lightIcon) {
                    console.log('Icons not found in DOM yet');
                    return;
                }
                
                console.log('Updating icons for theme:', currentTheme);
                
                if (currentTheme === 'dark') {
                    darkIcon.style.display = 'block';
                    lightIcon.style.display = 'none';
                } else {
                    darkIcon.style.display = 'none';
                    lightIcon.style.display = 'block';
                }
            }
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
    <body class="bg-white dark:bg-black">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <flux:sidebar sticky stashable class="w-64 flex-shrink-0 h-screen flex flex-col justify-between border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-black">
                <div>
                    <!-- Profile at the top -->
                    <div class="flex items-center justify-between px-2 py-6">
                        <div class="flex items-center space-x-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white text-[15px] font-bold">
                                {{ auth()->user()->initials() }}
                            </span>
                            <div class="flex flex-col">
                                <span class="font-semibold text-zinc-900 dark:text-white leading-tight text-sm">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 leading-tight">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                        <a href="{{ route('profile') }}" class="ml-2 flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700" title="Settings">
                            <flux:icon name="cog" class="h-4 w-4" />
                </a>
                    </div>

                    <!-- Navigation -->
                <flux:navlist variant="outline">
                    <flux:navlist.group :heading="__('Platform')" class="grid">
                            <flux:navlist.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                        <flux:navlist.item icon="chat-bubble-left-right" :href="route('chat')" :current="request()->routeIs('chat')" wire:navigate>{{ __('Chat') }}</flux:navlist.item>
                    </flux:navlist.group>
                        <flux:navlist.group :heading="__('User Management')" class="grid">
                        <flux:navlist.item icon="user-group" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                        <flux:navlist.item icon="shield-check" :href="route('admin.roles')" :current="request()->routeIs('admin.roles')" wire:navigate>{{ __('Roles') }}</flux:navlist.item>
                        <flux:navlist.item icon="key" :href="route('admin.permissions')" :current="request()->routeIs('admin.permissions')" wire:navigate>{{ __('Permissions') }}</flux:navlist.item>
                        </flux:navlist.group>
                        <flux:navlist.group :heading="__('System')" class="grid">
                        <flux:navlist.item icon="cog" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate>{{ __('System Settings') }}</flux:navlist.item>
                        <flux:navlist.item icon="document-text" :href="route('admin.logs')" :current="request()->routeIs('admin.logs')" wire:navigate>{{ __('System Logs') }}</flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
                                    </div>
                <div class="mb-4 flex w-full items-center justify-between px-2">
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                            @csrf
                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </button>
                        </form>
                    <!-- Theme Toggle Button - Direct DOM Implementation -->
                    <button 
                        id="theme-toggle-btn"
                        class="flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                    >
                        <!-- Sun icon (dark mode) -->
                        <svg 
                            id="dark-icon"
                            xmlns="http://www.w3.org/2000/svg" 
                            class="h-4 w-4"
                            viewBox="0 0 24 24" 
                            fill="none" 
                            stroke="currentColor" 
                            stroke-width="2"
                            stroke-linecap="round" 
                            stroke-linejoin="round"
                            style="display: none;"
                        >
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        
                        <!-- Moon icon (light mode) -->
                        <svg 
                            id="light-icon"
                            xmlns="http://www.w3.org/2000/svg" 
                            class="h-4 w-4"
                            viewBox="0 0 24 24" 
                            fill="none" 
                            stroke="currentColor" 
                            stroke-width="2"
                            stroke-linecap="round" 
                            stroke-linejoin="round"
                            style="display: none;"
                        >
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </button>
                </div>
            </flux:sidebar>

            <!-- Main Content -->
            <div class="flex-1 h-screen overflow-y-auto">
                <!-- Mobile Header -->
                <flux:header class="lg:hidden">
                    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                    <flux:spacer />

                    <flux:dropdown position="top" align="end">
                        <flux:profile
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevron-down"
                        />

                        <flux:menu>
                            <flux:menu.radio.group>
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                            <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </span>

                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </flux:header>

                <!-- Page Content -->
                <main class="p-6">
                    {{ $slot }}
                </main>
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