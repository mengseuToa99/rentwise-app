<div class="bg-gray-50 dark:bg-black min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Card -->
        <div class="bg-transparent backdrop-blur-none border border-gray-300 dark:border-zinc-700 rounded-lg shadow-none overflow-hidden">
            <!-- Notifications -->
            @if (session('success'))
                <div class="p-3 text-sm text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="p-3 text-sm text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="updateProfile">
                <!-- Profile Image Section -->
                <div class="relative px-4 sm:px-6 pt-8">
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            @if ($profileImage)
                                <img src="{{ asset('storage/' . $profileImage) }}" alt="Profile Image" class="h-32 w-32 rounded-full object-cover border-4 border-white/80 dark:border-zinc-800/80 shadow-sm">
                            @else
                                <div class="h-32 w-32 rounded-full bg-gray-200/20 dark:bg-zinc-700/20 flex items-center justify-center border-4 border-white/80 dark:border-zinc-800/80 shadow-sm text-gray-400 dark:text-zinc-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                            @endif
                            <label for="profileImage" class="absolute bottom-0 right-0 bg-gray-800/80 hover:bg-black/80 text-white rounded-full p-2 shadow-sm cursor-pointer transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                            </label>
                            <input wire:model="newProfileImage" type="file" id="profileImage" class="hidden">
                        </div>
                        @error('newProfileImage') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        
                        <div class="mt-4 text-center">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $firstName }} {{ $lastName }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $email }}</p>
                            
                            <!-- Roles Badges -->
                            <div class="flex flex-wrap justify-center gap-2 mt-2">
                                @forelse ($roles as $role)
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium 
                                        @if(strtolower($role) == 'landlord' || strtolower($role) == 'admin') 
                                            bg-blue-100/60 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200 border border-blue-300 dark:border-blue-800
                                        @elseif(strtolower($role) == 'tenant')
                                            bg-green-100/60 text-green-800 dark:bg-green-900/40 dark:text-green-200 border border-green-300 dark:border-green-800
                                        @else
                                            bg-gray-100/40 text-gray-800 dark:bg-zinc-800/40 dark:text-gray-200 border border-gray-300 dark:border-zinc-700
                                        @endif">
                                        {{ $role }}
                                    </span>
                                @empty
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('No roles assigned') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="px-4 sm:px-6 py-6">
                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="firstName" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('First Name') }}</label>
                                <input wire:model="firstName" type="text" id="firstName" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                                @error('firstName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="lastName" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Last Name') }}</label>
                                <input wire:model="lastName" type="text" id="lastName" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                                @error('lastName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="username" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Username') }}</label>
                                <input wire:model="username" type="text" id="username" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                                @error('username') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="phoneNumber" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Phone Number') }}</label>
                                <input wire:model="phoneNumber" type="text" id="phoneNumber" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                                @error('phoneNumber') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="email" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Email Address') }}</label>
                            <input wire:model="email" type="email" id="email" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Password Section -->
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Change Password
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="currentPassword" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Current Password') }}</label>
                                <input wire:model="currentPassword" type="password" id="currentPassword" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                                @error('currentPassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('New Password') }}</label>
                                <input wire:model="password" type="password" id="password" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="password_confirmation" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Confirm New Password') }}</label>
                                <input wire:model="password_confirmation" type="password" id="password_confirmation" class="block w-full px-4 py-3 rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 shadow-none focus:border-gray-500 focus:ring-gray-500 text-md dark:text-white">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 dark:border-zinc-700 rounded-md text-sm font-medium text-black dark:text-black shadow-sm hover:bg-gray-50 hover:text-black dark:hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-transparent transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> 