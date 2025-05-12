<div class="grid h-screen lg:grid-cols-2 overflow-hidden">
    <div class="flex flex-col gap-4 p-4 md:p-8 bg-slate-50">
        <div class="flex justify-center gap-2 md:justify-start">
            <a href="#" class="flex items-center gap-2 font-medium">
                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-500 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M4 5v14h6v-3a3 3 0 0 1 3-3 3 3 0 0 1 3 3v3h6V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2Z"></path>
                        <path d="M22 19v2H2v-2"></path>
                    </svg>
                </div>
                Rentwise
            </a>
        </div>
        <div class="flex flex-1 items-center justify-center">
            <div class="w-full max-w-xs">
                <form wire:submit="login" class="flex flex-col gap-5">
                    <div class="flex flex-col items-center gap-1 text-center">
                        <h1 class="text-2xl font-bold text-slate-800">Login to your account</h1>
                        <p class="text-balance text-sm text-slate-500">
                            Enter your email below to login to your account
                        </p>
                    </div>

                    @if (session('error'))
                        <div class="p-3 text-sm text-red-600 bg-red-50 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid gap-4">
                        <div class="grid gap-1.5">
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input 
                                wire:model="email"
                                id="email"
                                type="email"
                                placeholder="m@example.com"
                                required
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('email') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid gap-1.5">
                            <div class="flex items-center">
                                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                                <a href="#" class="ml-auto text-sm text-blue-500 underline-offset-4 hover:underline">
                                    Forgot password?
                                </a>
                            </div>
                            <input 
                                wire:model="password"
                                id="password"
                                type="password"
                                required
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('password') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <input 
                                wire:model="remember"
                                id="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-blue-500 focus:ring-blue-500"
                            >
                            <label for="remember" class="text-sm text-slate-600">Remember me</label>
                        </div>
                        
                        <button 
                            type="submit"
                            class="inline-flex justify-center rounded-md bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 w-full"
                        >
                            <span wire:loading.remove wire:target="login">Login</span>
                            <span wire:loading wire:target="login">Loading...</span>
                        </button>
                        
                        <div class="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-slate-200">
                            <span class="relative z-10 bg-slate-50 px-2 text-slate-500">
                                Or continue with
                            </span>
                        </div>
                        
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 focus-visible:ring-offset-2 w-full"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 mr-2">
                                <path
                                d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"
                                fill="currentColor"
                                />
                            </svg>
                            Login with GitHub
                        </button>
                    </div>
                    
                    <div class="text-center text-sm">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-blue-500 underline underline-offset-4">
                            Sign up
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="relative hidden bg-slate-100 lg:block">
        <img
            src="https://images.unsplash.com/photo-1554995207-c18c203602cb?q=80&w=1470&auto=format&fit=crop"
            alt="Property Image"
            class="absolute inset-0 h-full w-full object-cover"
        />
    </div>
</div>
