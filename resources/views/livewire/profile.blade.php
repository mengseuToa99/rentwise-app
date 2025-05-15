<div class="py-4 bg-gray-50 dark:bg-zinc-950 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            <!-- Notifications -->
            @if (session('success'))
                <div class="mb-4 p-3 flex items-center text-sm text-green-600 bg-green-100 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="mb-4 p-3 flex items-center text-sm text-red-600 bg-red-100 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="updateProfile">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-700">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Account Settings</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your personal information and preferences</p>
                </div>
                
                <!-- Profile Image Section -->
                <div class="px-6 pt-6">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="relative">
                            @if ($profileImage)
                                <img src="{{ asset('storage/' . $profileImage) }}" alt="Profile Image" class="h-28 w-28 rounded-full object-cover border-4 border-white dark:border-zinc-800 shadow-sm">
                            @else
                                <div class="h-28 w-28 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center border border-gray-200 dark:border-zinc-700 text-gray-400 dark:text-zinc-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                            @endif
                            <label for="profileImage" class="absolute bottom-0 right-0 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-full p-2 shadow-sm cursor-pointer transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                            </label>
                            <input wire:model="newProfileImage" type="file" id="profileImage" class="hidden">
                        </div>
                        
                        <div class="text-center sm:text-left">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $firstName }} {{ $lastName }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $email }}</p>
                            
                            <!-- Roles Badges -->
                            <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-2">
                                @forelse ($roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                        @if(strtolower($role) == 'landlord' || strtolower($role) == 'admin') 
                                            bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif(strtolower($role) == 'tenant')
                                            bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @else
                                            bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300
                                        @endif">
                                        {{ $role }}
                                    </span>
                                @empty
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('No roles assigned') }}</p>
                                @endforelse
                            </div>
                        </div>
                        @error('newProfileImage') 
                            <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
                
                <!-- Personal Information Section -->
                <div class="px-6 py-6">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                            <label for="firstName" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('First Name') }}</label>
                            <input wire:model="firstName" type="text" id="firstName" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('firstName') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                            @enderror
                            </div>
                            
                            <div>
                            <label for="lastName" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Last Name') }}</label>
                            <input wire:model="lastName" type="text" id="lastName" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('lastName') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                            @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                            <label for="username" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Username') }}</label>
                            <input wire:model="username" type="text" id="username" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('username') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                            @enderror
                            </div>
                            
                            <div>
                            <label for="phoneNumber" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Phone Number') }}</label>
                            <input wire:model="phoneNumber" type="text" id="phoneNumber" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('phoneNumber') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                            @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                        <label for="email" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Email Address') }}</label>
                        <input wire:model="email" type="email" id="email" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('email') 
                            <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                        @enderror
                        </div>
                    </div>
                    
                    <!-- Password Section -->
                <div class="px-6 py-6 border-t border-gray-200 dark:border-zinc-700">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Change Password
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                            <label for="currentPassword" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Current Password') }}</label>
                            <input wire:model="currentPassword" type="password" id="currentPassword" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('currentPassword') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                            @enderror
                            </div>
                            
                            <div>
                            <label for="password" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('New Password') }}</label>
                            <input wire:model="password" type="password" id="password" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('password') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                            @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                            <label for="password_confirmation" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Confirm New Password') }}</label>
                            <input wire:model="password_confirmation" type="password" id="password_confirmation" class="block w-full px-4 py-2.5 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                <!-- Footer/Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Save Changes') }}
                        </button>
                </div>
            </form>
        </div>
    </div>
</div> 