<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" x-data="themeToggle()">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-black">
        <flux:sidebar sticky stashable="{{ !($preserveSidebar ?? false) }}" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-black">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <!-- Header with logo and settings button -->
            <div class="mb-5 flex w-full items-center justify-between" x-data="{ activeModal: null }">
                <div class="flex-grow">
                <x-app-logo />
                </div>
                
                <!-- Settings Button with Modals -->
                <button 
                    @click="activeModal = activeModal === null ? 'settings' : null" 
                    class="ml-2 flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-black dark:text-zinc-300 dark:hover:bg-zinc-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                
                <!-- Settings Menu Panel -->
                <div 
                    x-show="activeModal === 'settings'" 
                    @click.outside="activeModal = null"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute top-16 right-2 z-50 w-56 rounded-md border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-black"
                >
                    <!-- Settings Navigation -->
                    <div x-show="!['password', 'appearance'].includes(activeModal)" class="p-2">
                        <a 
                            href="{{ route('profile') }}" 
                            wire:navigate
                            class="flex w-full items-center rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            {{ __('Profile') }}
                        </a>
                        
                        <button 
                            @click="activeModal = 'password'" 
                            class="flex w-full items-center rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                            </svg>
                            {{ __('Password') }}
                        </button>
                        
                        <button 
                            @click="activeModal = 'appearance'" 
                            class="flex w-full items-center rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                            {{ __('Appearance') }}
                        </button>
                    </div>
                    
                    <!-- Profile content removed - using route instead -->
                    
                    <!-- Password Content -->
                    <div x-show="activeModal === 'password'">
                        <div class="flex items-center justify-between border-b border-zinc-200 p-3 dark:border-zinc-700">
                            <h3 class="text-sm font-medium">{{ __('Change Password') }}</h3>
                            <button @click="activeModal = 'settings'" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 12H5M12 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-3">
                            <form class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-zinc-700 dark:text-zinc-300" for="current_password">{{ __('Current password') }}</label>
                                    <input type="password" id="current_password" class="w-full rounded-md border border-zinc-300 p-1.5 text-sm dark:border-zinc-600 dark:bg-zinc-700">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-zinc-700 dark:text-zinc-300" for="new_password">{{ __('New password') }}</label>
                                    <input type="password" id="new_password" class="w-full rounded-md border border-zinc-300 p-1.5 text-sm dark:border-zinc-600 dark:bg-zinc-700">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-zinc-700 dark:text-zinc-300" for="confirm_password">{{ __('Confirm password') }}</label>
                                    <input type="password" id="confirm_password" class="w-full rounded-md border border-zinc-300 p-1.5 text-sm dark:border-zinc-600 dark:bg-zinc-700">
                                </div>
                                <button type="submit" class="w-full rounded-md bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Appearance Content -->
                    <div x-show="activeModal === 'appearance'">
                        <div class="flex items-center justify-between border-b border-zinc-200 p-3 dark:border-zinc-700">
                            <h3 class="text-sm font-medium">{{ __('Appearance') }}</h3>
                            <button @click="activeModal = 'settings'" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 12H5M12 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-3">
                            <div class="space-y-2">
                                <p class="text-xs text-zinc-700 dark:text-zinc-300">{{ __('Choose theme mode') }}</p>
                                <div class="flex gap-2">
                                    <button @click="$flux.appearance = 'light'" :class="{'bg-blue-100 border-blue-500 dark:bg-blue-900': $flux.appearance === 'light'}" class="flex-1 rounded-md border border-zinc-300 p-1.5 text-xs hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-700">
                                        <div class="flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                                            {{ __('Light') }}
                                        </div>
                                    </button>
                                    <button @click="$flux.appearance = 'dark'" :class="{'bg-blue-100 border-blue-500 dark:bg-blue-900': $flux.appearance === 'dark'}" class="flex-1 rounded-md border border-zinc-300 p-1.5 text-xs hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-700">
                                        <div class="flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                            </svg>
                                            {{ __('Dark') }}
                                        </div>
                                    </button>
                                </div>
                                <button @click="$flux.appearance = 'system'" :class="{'bg-blue-100 border-blue-500 dark:bg-blue-900': $flux.appearance === 'system'}" class="w-full rounded-md border border-zinc-300 p-1.5 text-xs hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-700">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                            <line x1="8" y1="21" x2="16" y2="21"></line>
                                            <line x1="12" y1="17" x2="12" y2="21"></line>
                                        </svg>
                                        {{ __('System') }}
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="chat-bubble-left-right" :href="route('chat')" :current="request()->routeIs('chat')" wire:navigate>{{ __('Chat') }}</flux:navlist.item>
                </flux:navlist.group>

                @if(auth()->user() && auth()->user()->roles && (
                  auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'landlord'; }) || 
                  auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; })))
                <!-- Property Management - For Landlords and Admins only -->
                <flux:navlist.group :heading="__('Property Management')" class="grid">
                    <flux:navlist.item icon="building-office-2" :href="route('properties.index')" :current="request()->routeIs('properties.*')" wire:navigate>{{ __('Properties') }}</flux:navlist.item>
                    <flux:navlist.item icon="squares-2x2" :href="route('units.index')" :current="request()->routeIs('units.*')" wire:navigate>{{ __('Units') }}</flux:navlist.item>
                </flux:navlist.group>
                
                <!-- Leasing - For Landlords only -->
                <flux:navlist.group :heading="__('Leasing')" class="grid">
                    <flux:navlist.item icon="document-text" :href="route('rentals.index')" :current="request()->routeIs('rentals.*')" wire:navigate>{{ __('Rentals') }}</flux:navlist.item>
                </flux:navlist.group>
                
                <!-- Finance - For Landlords only -->
                <flux:navlist.group :heading="__('Finance')" class="grid">
                    <flux:navlist.item icon="currency-dollar" :href="route('invoices.index')" :current="request()->routeIs('invoices.*')" wire:navigate>{{ __('Invoices') }}</flux:navlist.item>
                </flux:navlist.group>
                @endif

                @if(auth()->user() && auth()->user()->roles && auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'tenant'; }))
                <!-- Tenant Access Only -->
                <flux:navlist.group :heading="__('My Rentals')" class="grid">
                    <flux:navlist.item icon="currency-dollar" :href="route('tenant.invoices')" :current="request()->routeIs('tenant.invoices')" wire:navigate>{{ __('My Invoices') }}</flux:navlist.item>
                </flux:navlist.group>
                @endif

                @if(auth()->user() && auth()->user()->roles && auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
                <!-- Admin Only Section -->
                <flux:navlist.group :heading="__('Administration')" class="grid">
                    <flux:navlist.item icon="chart-bar" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="user-group" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                    <flux:navlist.item icon="shield-check" :href="route('admin.roles')" :current="request()->routeIs('admin.roles')" wire:navigate>{{ __('Roles') }}</flux:navlist.item>
                    <flux:navlist.item icon="key" :href="route('admin.permissions')" :current="request()->routeIs('admin.permissions')" wire:navigate>{{ __('Permissions') }}</flux:navlist.item>
                    <flux:navlist.item icon="cog" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate>{{ __('System Settings') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.logs')" :current="request()->routeIs('admin.logs')" wire:navigate>{{ __('System Logs') }}</flux:navlist.item>
                </flux:navlist.group>
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
                    x-data="{isDark: document.documentElement.classList.contains('dark')}"
                    @click="isDark = !isDark; document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', isDark ? 'dark' : 'light')" 
                    class="flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                >
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
                        stroke-linejoin="round"
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
                        stroke-linejoin="round"
                    >
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
                        </div>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden" x-data="{ mobileActiveModal: null }">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <!-- Mobile Settings Button -->
            <button 
                @click="mobileActiveModal = mobileActiveModal === null ? 'settings' : null" 
                class="mx-2 inline-flex h-10 w-10 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-black dark:text-zinc-300 dark:hover:bg-zinc-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
            
            <!-- Mobile Settings Menu Panel -->
            <div 
                x-show="mobileActiveModal === 'settings'" 
                @click.outside="mobileActiveModal = null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute top-14 right-2 z-50 w-56 rounded-md border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-black"
            >
                <div class="p-2">
                    <a 
                        href="{{ route('profile') }}" 
                        wire:navigate
                        class="flex w-full items-center rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        {{ __('Profile') }}
                    </a>
                    
                    <button 
                        @click="mobileActiveModal = 'password'" 
                        class="flex w-full items-center rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                        </svg>
                        {{ __('Password') }}
                    </button>
                    
                    <button 
                        @click="mobileActiveModal = 'appearance'" 
                        class="flex w-full items-center rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        {{ __('Appearance') }}
                    </button>
                </div>
            </div>
            
            <!-- Mobile Profile Modal removed - using route instead -->
            
            <!-- Mobile Password Modal -->
            <div 
                x-show="mobileActiveModal === 'password'" 
                @click.outside="mobileActiveModal = 'settings'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute top-14 right-2 z-50 w-56 rounded-md border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-black"
            >
                <div class="flex items-center justify-between border-b border-zinc-200 p-3 dark:border-zinc-700">
                    <h3 class="text-sm font-medium">{{ __('Change Password') }}</h3>
                    <button @click="mobileActiveModal = 'settings'" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-3">
                    <form class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-zinc-700 dark:text-zinc-300" for="mobile-current-password">{{ __('Current password') }}</label>
                            <input type="password" id="mobile-current-password" class="w-full rounded-md border border-zinc-300 p-1.5 text-sm dark:border-zinc-600 dark:bg-zinc-700">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-zinc-700 dark:text-zinc-300" for="mobile-new-password">{{ __('New password') }}</label>
                            <input type="password" id="mobile-new-password" class="w-full rounded-md border border-zinc-300 p-1.5 text-sm dark:border-zinc-600 dark:bg-zinc-700">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-zinc-700 dark:text-zinc-300" for="mobile-confirm-password">{{ __('Confirm password') }}</label>
                            <input type="password" id="mobile-confirm-password" class="w-full rounded-md border border-zinc-300 p-1.5 text-sm dark:border-zinc-600 dark:bg-zinc-700">
                        </div>
                        <button type="submit" class="w-full rounded-md bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
            
            <!-- Mobile Appearance Modal -->
            <div 
                x-show="mobileActiveModal === 'appearance'" 
                @click.outside="mobileActiveModal = 'settings'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute top-14 right-2 z-50 w-56 rounded-md border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-black"
            >
                <div class="flex items-center justify-between border-b border-zinc-200 p-3 dark:border-zinc-700">
                    <h3 class="text-sm font-medium">{{ __('Appearance') }}</h3>
                    <button @click="mobileActiveModal = 'settings'" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-3">
                    <div class="space-y-2">
                        <p class="text-xs text-zinc-700 dark:text-zinc-300">{{ __('Choose theme mode') }}</p>
                        <div class="flex gap-2">
                            <button @click="$flux.appearance = 'light'" :class="{'bg-blue-100 border-blue-500 dark:bg-blue-900': $flux.appearance === 'light'}" class="flex-1 rounded-md border border-zinc-300 p-1.5 text-xs hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-700">
                                <div class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                                    {{ __('Light') }}
                                </div>
                            </button>
                            <button @click="$flux.appearance = 'dark'" :class="{'bg-blue-100 border-blue-500 dark:bg-blue-900': $flux.appearance === 'dark'}" class="flex-1 rounded-md border border-zinc-300 p-1.5 text-xs hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-700">
                                <div class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                    </svg>
                                    {{ __('Dark') }}
                                </div>
                            </button>
                        </div>
                        <button @click="$flux.appearance = 'system'" :class="{'bg-blue-100 border-blue-500 dark:bg-blue-900': $flux.appearance === 'system'}" class="w-full rounded-md border border-zinc-300 p-1.5 text-xs hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-700">
                            <div class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                                {{ __('System') }}
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Theme Toggle -->
            <button 
                x-data="{isDark: document.documentElement.classList.contains('dark')}"
                @click="isDark = !isDark; document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', isDark ? 'dark' : 'light')" 
                class="mx-2 inline-flex h-10 w-10 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
            >
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

            <!-- Mobile User Dropdown -->
            <flux:dropdown position="top" align="end">
                <div class="flex h-10 w-10 items-center justify-center rounded-md bg-neutral-200 text-sm font-medium text-black hover:bg-neutral-300 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600">
                    {{ auth()->user() && method_exists(auth()->user(), 'initials') ? auth()->user()->initials() : 'U' }}
                </div>

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user() && method_exists(auth()->user(), 'initials') ? auth()->user()->initials() : 'U' }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user() ? auth()->user()->name : 'User' }}</span>
                                    <span class="truncate text-xs">{{ auth()->user() ? auth()->user()->email : 'user@example.com' }}</span>
                                </div>
                            </div>
                        </div>
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

        {{ $slot }}

        @fluxScripts
        @stack('scripts')
    </body>
</html>
