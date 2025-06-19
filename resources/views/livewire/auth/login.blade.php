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
                    </div>
                    
                    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">
                            Sign up
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

<!-- Alpine.js and themeToggle are now loaded via layout -->
