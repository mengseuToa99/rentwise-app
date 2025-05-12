<div class="grid h-screen lg:grid-cols-2 overflow-hidden">
    <div class="flex flex-col gap-2 p-3 md:p-5 bg-slate-50">
        <div class="flex justify-center gap-2 md:justify-start">
            <a href="#" class="flex items-center gap-1 font-medium">
                <div class="flex h-5 w-5 items-center justify-center rounded-md bg-blue-500 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                        <path d="M4 5v14h6v-3a3 3 0 0 1 3-3 3 3 0 0 1 3 3v3h6V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2Z"></path>
                        <path d="M22 19v2H2v-2"></path>
                    </svg>
                </div>
                <span class="text-sm">Rentwise</span>
            </a>
        </div>
        <div class="flex flex-1 items-center justify-center">
            <div class="w-full max-w-sm">
                <form wire:submit="register" class="flex flex-col gap-3">
                    <div class="flex flex-col items-center gap-1 text-center">
                        <h1 class="text-xl font-bold text-slate-800">Create an account</h1>
                        <p class="text-xs text-slate-500">
                            Enter your details below
                        </p>
                    </div>

                    @if(session('error') || $error_message)
                        <div class="p-2 text-xs text-red-600 bg-red-50 rounded-md">
                            {{ session('error') ?: $error_message }}
                        </div>
                    @endif

                    <div class="grid gap-2.5">
                        <!-- Name -->
                        <div class="grid gap-1">
                            <label for="name" class="block text-xs font-medium text-slate-700">Full Name</label>
                            <input 
                                wire:model="name"
                                id="name"
                                type="text"
                                placeholder="John Doe"
                                required
                                autofocus
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('name') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Email Address -->
                        <div class="grid gap-1">
                            <label for="email" class="block text-xs font-medium text-slate-700">Email address</label>
                            <input 
                                wire:model="email"
                                id="email"
                                type="email"
                                placeholder="email@example.com"
                                required
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('email') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password -->
                        <div class="grid gap-1">
                            <label for="password" class="block text-xs font-medium text-slate-700">Password</label>
                            <input 
                                wire:model="password"
                                id="password"
                                type="password"
                                required
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('password') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="grid gap-1">
                            <label for="password_confirmation" class="block text-xs font-medium text-slate-700">Confirm password</label>
                            <input 
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                type="password"
                                required
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('password_confirmation') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Role Selection with Icons -->
                        <div class="grid gap-1.5">
                            <label class="block text-xs font-medium text-slate-700 text-center">Register as</label>
                            <div class="grid grid-cols-3 gap-1.5">
                                <!-- Tenant Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="role" value="tenant" class="sr-only peer" checked>
                                    <div class="flex flex-col items-center py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all h-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-slate-500 peer-checked:text-blue-600">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-slate-700 peer-checked:text-blue-600 mt-0.5">Tenant</span>
                                    </div>
                                </label>
                                
                                <!-- Landlord Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="role" value="landlord" class="sr-only peer">
                                    <div class="flex flex-col items-center py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all h-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-slate-500 peer-checked:text-blue-600">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                        </svg>
                                        <span class="text-xs font-medium text-slate-700 peer-checked:text-blue-600 mt-0.5">Landlord</span>
                                    </div>
                                </label>
                                
                                <!-- Admin Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="role" value="admin" class="sr-only peer">
                                    <div class="flex flex-col items-center py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all h-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-slate-500 peer-checked:text-blue-600">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <path d="M9 3v18"></path>
                                            <path d="M14 3v18"></path>
                                            <path d="M3 9h18"></path>
                                            <path d="M3 14h18"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-slate-700 peer-checked:text-blue-600 mt-0.5">Admin</span>
                                    </div>
                                </label>
                            </div>
                            @error('role') 
                                <p class="text-xs text-red-500 text-center">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button 
                            type="submit"
                            class="inline-flex justify-center rounded-md bg-blue-500 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-blue-500 focus-visible:ring-offset-1 w-full mt-1"
                        >
                            <span wire:loading.remove wire:target="register">Create account</span>
                            <span wire:loading wire:target="register">Processing...</span>
                        </button>
                    </div>
                    
                    <div class="text-center text-xs mt-1">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-blue-500 underline underline-offset-2" wire:navigate>
                            Log in
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="relative hidden bg-slate-100 lg:block">
        <img
            src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1470&auto=format&fit=crop"
            alt="Property Image"
            class="absolute inset-0 h-full w-full object-cover"
        />
    </div>
</div> 