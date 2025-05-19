<x-guest-layout>
    <!-- Toast Notification -->
    <div id="toast-notification" class="fixed top-4 right-4 z-50 max-w-md p-4 rounded-lg shadow-lg text-sm border hidden bg-white dark:bg-zinc-800">
        <div class="flex items-center">
            <div id="toast-icon-success" class="w-5 h-5 mr-3 text-green-500">
                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div id="toast-icon-error" class="hidden w-5 h-5 mr-3 text-red-500">
                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div id="toast-message" class="text-sm font-normal break-words"></div>
            <button onclick="hideToast()" class="ml-auto -mx-1.5 -my-1.5 p-1.5 text-gray-400 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-zinc-900">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reset Password</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Please enter your new password below</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ request()->query('token') }}">
                <input type="hidden" name="email" value="{{ request()->query('email') }}">

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('New Password')" />
                    <div class="relative">
                        <x-text-input id="password" 
                            class="block mt-1 w-full h-12 pl-4 pr-10 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800" 
                            type="password" 
                            name="password" 
                            required 
                            autofocus 
                            autocomplete="new-password" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Password must be at least 8 characters long and include:
                        <ul class="list-disc list-inside mt-1">
                            <li>At least one uppercase letter</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                        </ul>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <div class="relative">
                        <x-text-input id="password_confirmation" 
                            class="block mt-1 w-full h-12 pl-4 pr-10 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        {{ __('Back to Login') }}
                    </a>
                    <x-primary-button class="ml-4">
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Simple functions for toast notifications
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            const successIcon = document.getElementById('toast-icon-success');
            const errorIcon = document.getElementById('toast-icon-error');
            
            // Set message
            toastMessage.textContent = message;
            
            // Set appropriate styling based on type
            if (type === 'success') {
                toast.classList.add('border-green-200', 'text-green-800');
                toast.classList.remove('border-red-200', 'text-red-800');
                successIcon.classList.remove('hidden');
                errorIcon.classList.add('hidden');
            } else {
                toast.classList.add('border-red-200', 'text-red-800');
                toast.classList.remove('border-green-200', 'text-green-800');
                successIcon.classList.add('hidden');
                errorIcon.classList.remove('hidden');
            }
            
            // Show toast
            toast.classList.remove('hidden');
            
            // Hide after 5 seconds
            setTimeout(hideToast, 5000);
        }
        
        function hideToast() {
            const toast = document.getElementById('toast-notification');
            toast.classList.add('hidden');
        }
        
        // Check for errors on page load
        document.addEventListener('DOMContentLoaded', function() {
            const errorElements = document.querySelectorAll('[role="alert"]');
            if (errorElements.length > 0) {
                const errorMessage = errorElements[0].textContent.trim();
                showToast(errorMessage, 'error');
            }
        });
    </script>
</x-guest-layout> 