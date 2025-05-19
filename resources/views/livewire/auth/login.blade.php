<div class="grid h-screen lg:grid-cols-2 overflow-hidden">
    <div class="flex flex-col gap-4 p-4 md:p-8 bg-gray-50 dark:bg-zinc-900 relative">
        <div class="flex justify-center gap-2 md:justify-start">
            <a href="#" class="flex items-center gap-2 font-medium">
                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-gray-800 text-white dark:bg-white dark:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M4 5v14h6v-3a3 3 0 0 1 3-3 3 3 0 0 1 3 3v3h6V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2Z"></path>
                        <path d="M22 19v2H2v-2"></path>
                    </svg>
                </div>
                <span class="text-gray-800 dark:text-gray-200">Rentwise</span>
            </a>
        </div>
        <div class="flex flex-1 items-center justify-center">
            <div class="w-full max-w-xs">
                <form wire:submit="login" class="flex flex-col gap-5">
                    <div class="flex flex-col items-center gap-1 text-center">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Login to your account</h1>
                        <p class="text-balance text-sm text-gray-500 dark:text-gray-400">
                            Enter your email below to login to your account
                        </p>
                    </div>

                    @if (session('status'))
                        <div class="p-4 mb-2 text-sm text-green-800 bg-green-100 dark:bg-green-900/20 dark:text-green-300 rounded-md shadow-md border border-green-200 dark:border-green-800 flex items-center justify-between">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-3 text-sm text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid gap-4">
                        <div class="grid gap-1.5">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input 
                                wire:model="email"
                                id="email"
                                type="email"
                                placeholder="m@example.com"
                                required
                                class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                            >
                            @error('email') 
                                <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-1.5">
                            <div class="flex items-center">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <a href="{{ route('password.reset') }}" class="ml-auto text-sm text-gray-500 dark:text-gray-400 underline-offset-4 hover:underline">
                                    Forgot password?
                                </a>
                            </div>
                            <input 
                                wire:model="password"
                                id="password"
                                type="password"
                                required
                                class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                            >
                            @error('password') 
                                <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <input 
                                wire:model="remember"
                                id="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-gray-600 focus:ring-gray-500 dark:bg-zinc-700"
                            >
                            <label for="remember" class="text-sm text-gray-600 dark:text-gray-400">Remember me</label>
                        </div>

                        <button 
                            type="submit"
                            class="inline-flex justify-center rounded-md bg-white border border-gray-300 dark:border-zinc-700 px-5 py-2.5 text-sm font-medium text-black dark:text-black shadow-sm hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2 w-full"
                        >
                            <span wire:loading.remove wire:target="login">Login</span>
                            <span wire:loading wire:target="login">Loading...</span>
                        </button>
                        
                        <div class="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-gray-300 dark:after:border-zinc-700">
                            <span class="relative z-10 bg-gray-50 dark:bg-zinc-900 px-2 text-gray-500 dark:text-gray-400">
                                Or continue with
                            </span>
                        </div>

                        <div class="flex justify-center gap-4">
                            <!-- Google Login Button -->
                            <a
                                href="{{ route('social.redirect', 'google') }}"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-transparent dark:border-zinc-700 p-2 text-sm font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                            >
                                <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                            </a>
                            
                            <!-- Phone Number Login Button -->
                            <a
                                href="{{ route('phone.verification') }}"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-transparent dark:border-zinc-700 p-2 text-sm font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </a>
                            
                            <!-- Telegram Login Button -->
                            <a
                                href="{{ route('social.redirect', 'telegram') }}"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-transparent dark:border-zinc-700 p-2 text-sm font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6" fill="#26A5E4">
                                    <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.73 7.73L15.8 17.46c-.12.56-.73.7-1.12.36L11.13 15l-1.79 1.73c-.18.17-.42.18-.58.04l.18-2.49 4.47-4.05c.22-.2-.01-.29-.29-.11l-5.53 3.49-2.36-.78c-.52-.16-.56-.6.08-.88l9.31-3.58c.4-.15.77.18.68.6z"/>
                                </svg>
                            </a>
                            
                            <!-- Facebook Login Button -->
                            <a
                                href="{{ route('social.redirect', 'facebook') }}"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-transparent dark:border-zinc-700 p-2 text-sm font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6" fill="#1877F2">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">
                            Sign up
                        </a>
                    </div>

                    <!-- Debug link for telegram testing -->
                    <div class="text-center text-xs mt-2">
                        <a href="{{ route('telegram.test') }}" class="text-gray-500 hover:underline">
                            Test Telegram Login
                        </a>
                    </div>
                    
                    <!-- Theme Toggle Button removed from here and positioned fixed on the screen -->
                </form>
            </div>
        </div>
    </div>
    <div class="relative hidden bg-gray-100 dark:bg-zinc-950 lg:block">
        <img
            src="https://images.unsplash.com/photo-1554995207-c18c203602cb?q=80&w=1470&auto=format&fit=crop"
            alt="Property Image"
            class="absolute inset-0 h-full w-full object-cover"
        />
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
<script>
    // Initialize Alpine.js globally for the theme toggle
    document.addEventListener('alpine:init', () => {
        Alpine.data('themeToggle', () => ({
            dark: localStorage.theme === 'dark',
            toggle() {
                this.dark = !this.dark;
                localStorage.theme = this.dark ? 'dark' : 'light';
                document.documentElement.classList.toggle('dark', this.dark);
            }
        }));
    });
</script>
