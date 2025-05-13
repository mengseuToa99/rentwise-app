<div class="py-6">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden dark:bg-zinc-800">
            <!-- Notifications -->
            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="p-3 bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-100">
                    {{ session('error') }}
                </div>
            @endif
            
            <form wire:submit.prevent="updateProfile" class="p-4">
                <!-- Profile Image Section -->
                <div class="flex flex-col items-center mb-6">
                    <div class="mb-3 text-center">
                        @if ($profileImage)
                            <img src="{{ asset('storage/' . $profileImage) }}" alt="Profile Image" class="h-28 w-28 rounded-full object-cover mx-auto border-2 border-gray-200 dark:border-zinc-600">
                        @else
                            <div class="h-28 w-28 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center mx-auto text-gray-400 dark:text-zinc-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                        @endif
                        
                        <label for="profileImage" class="mt-3 inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800 rounded-md cursor-pointer text-xs font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            {{ __('Change Photo') }}
                        </label>
                        <input wire:model="newProfileImage" type="file" id="profileImage" class="hidden">
                        @error('newProfileImage') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <!-- Profile Fields Section -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="firstName" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('First Name') }}</label>
                            <input wire:model="firstName" type="text" id="firstName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                            @error('firstName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="lastName" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Last Name') }}</label>
                            <input wire:model="lastName" type="text" id="lastName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                            @error('lastName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="username" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Username') }}</label>
                        <input wire:model="username" type="text" id="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                        <input wire:model="email" type="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="phoneNumber" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Phone Number') }}</label>
                        <input wire:model="phoneNumber" type="text" id="phoneNumber" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        @error('phoneNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="pt-2 border-t border-gray-200 dark:border-zinc-700">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">{{ __('Change Password') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="currentPassword" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Current Password') }}</label>
                                <input wire:model="currentPassword" type="password" id="currentPassword" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                @error('currentPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('New Password') }}</label>
                                <input wire:model="password" type="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="password_confirmation" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Confirm New Password') }}</label>
                                <input wire:model="password_confirmation" type="password" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
            
            <!-- User Roles Section -->
            <div class="p-4 bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Your Roles') }}</h3>
                
                <div class="flex flex-wrap gap-2">
                    @forelse ($roles as $role)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            {{ $role }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No roles assigned to your account.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div> 