<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeToggle()" x-bind:class="{ 'dark': dark }">
    <head>
        @include('partials.head')
        
        <!-- Prevent flash of wrong theme -->
        <script>
            // Immediately set theme to prevent flashing
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
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
                        <a href="{{ route('profile') }}" class="ml-2 flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 px-4" title="Settings">
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
                <div class="mb-4 flex w-full items-center justify-start gap-2 px-2">
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 px-4">
                            <flux:icon name="arrow-right-start-on-rectangle" class="h-4 w-4" />
                        </button>
                    </form>
                    <!-- Theme Toggle Button - Icon only -->
                    <button 
                        x-data="themeToggle()"
                        @click="toggle()"
                        class="flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 px-4"
                    >
                        <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            class="h-4 w-4"
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