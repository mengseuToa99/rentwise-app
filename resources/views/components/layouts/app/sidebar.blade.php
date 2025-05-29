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
        *,
        *::before,
        *::after {
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
    <!-- Add reset-theme utility -->
    <script src="{{ asset('reset-theme.js') }}"></script>
    <flux:sidebar sticky stashable="{{ !($preserveSidebar ?? false) }}" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-black">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <!-- Header with logo -->
        <div class="mb-5 flex w-full items-center justify-between">
            <div class="flex-grow">
                <a href="{{ route('profile') }}" class="flex items-center cursor-pointer" wire:navigate>
                <!-- Profile Image -->
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Image" class="h-10 w-10 rounded-full object-cover border-2 border-white dark:border-zinc-800 shadow-sm">
                @else
                    <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center border border-gray-200 dark:border-zinc-700 text-gray-400 dark:text-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                @endif
                <!-- Username -->
                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ auth()->user()->username }}</span>
            </a>
      
            </div>
        </div>

        <flux:navlist variant="outline">
            @if(auth()->user() && auth()->user()->roles && auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
            <!-- Admin User Navigation -->
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
            @else
            <!-- Non-Admin User Navigation -->
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                <flux:navlist.item icon="chat-bubble-left-right" :href="route('chat')" :current="request()->routeIs('chat')" wire:navigate>{{ __('Chat') }}</flux:navlist.item>
            </flux:navlist.group>

                @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'landlord'; }))
                <!-- Property Management - For Landlords only -->
                <flux:navlist.group :heading="__('Property Management')" class="grid">
                    <flux:navlist.item icon="building-office-2" :href="route('properties.index')" :current="request()->routeIs('properties.*')" wire:navigate>{{ __('Properties') }}</flux:navlist.item>
                    <flux:navlist.item icon="squares-2x2" :href="route('units.index')" :current="request()->routeIs('units.*')" wire:navigate>{{ __('Units') }}</flux:navlist.item>
                </flux:navlist.group>
                
                <!-- Leasing - For Landlords only -->
                <flux:navlist.group :heading="__('Leasing')" class="grid">
                    <flux:navlist.item icon="document-text" :href="route('rentals.index')" :current="request()->routeIs('rentals.*')" wire:navigate>{{ __('Rentals') }}</flux:navlist.item>
                    <flux:navlist.item icon="user-group" :href="route('tenants.index')" :current="request()->routeIs('tenants.*')" wire:navigate>{{ __('Tenant Info') }}</flux:navlist.item>
                </flux:navlist.group>
                
                <!-- Finance - For Landlords only -->
                <flux:navlist.group :heading="__('Finance')" class="grid">
                    <flux:navlist.item icon="currency-dollar" :href="route('invoices.index')" :current="request()->routeIs('invoices.*')" wire:navigate>{{ __('Invoices') }}</flux:navlist.item>
                    <flux:navlist.item icon="bolt" :href="route('utilities.index')" :current="request()->routeIs('utilities.index')" wire:navigate>{{ __('Utilities') }}</flux:navlist.item>
                    <flux:navlist.item icon="chart-bar" :href="route('utilities.usage')" :current="request()->routeIs('utilities.usage')" wire:navigate>{{ __('Utility Usage') }}</flux:navlist.item>
                </flux:navlist.group>
                @endif
           

            @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'tenant'; }))
            <!-- Tenant Access Only -->
            <flux:navlist.group :heading="__('My Rentals')" class="grid">
                <flux:navlist.item icon="currency-dollar" :href="route('tenant.invoices')" :current="request()->routeIs('tenant.invoices')" wire:navigate>{{ __('My Invoices') }}</flux:navlist.item>
                <flux:navlist.item icon="building-office" :href="route('tenant.property')" :current="request()->routeIs('tenant.property')" wire:navigate>{{ __('My Property') }}</flux:navlist.item>
            </flux:navlist.group>
            @endif
            @endif
        </flux:navlist>

        <flux:spacer />

        <!-- Bottom Controls -->
        <div class="mb-4 flex w-full items-center justify-between">
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

            <!-- Theme Toggle Button - Icon only -->
            <button
                x-data="{
                        isDark: localStorage.theme === 'dark' || (!localStorage.theme && window.matchMedia('(prefers-color-scheme: dark)').matches),
                        toggle() {
                            this.isDark = !this.isDark;
                            localStorage.theme = this.isDark ? 'dark' : 'light';
                            document.documentElement.classList.toggle('dark', this.isDark);
                        }
                    }"
                @click="toggle()"
                class="flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                <!-- Sun icon for dark mode -->
                <svg
                    x-show="isDark"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
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

                <!-- Moon icon for light mode -->
                <svg
                    x-show="!isDark"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
    </flux:sidebar>

    <!-- Mobile Header -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <!-- Theme Toggle Button -->
        <button
            x-data="{
                    isDark: localStorage.theme === 'dark' || (!localStorage.theme && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    toggle() {
                        this.isDark = !this.isDark;
                        localStorage.theme = this.isDark ? 'dark' : 'light';
                        document.documentElement.classList.toggle('dark', this.isDark);
                    }
                }"
            @click="toggle()"
            class="mx-2 inline-flex h-10 w-10 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
            <!-- Sun icon for dark mode -->
            <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            <!-- Moon icon for light mode -->
            <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>
    </flux:header>

    {{ $slot }}

    @fluxScripts
    @stack('scripts')
</body>

</html>