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
            <div class="w-full max-w-md">
                <form wire:submit="register" class="flex flex-col gap-5">
                    <div class="flex flex-col items-center gap-1 text-center">
                        <h1 class="text-2xl font-bold text-slate-800">Create an account</h1>
                        <p class="text-balance text-sm text-slate-500">
                            Enter your details below to create your account
                        </p>
                    </div>

                    @if(session('error') || $error_message)
                        <div class="p-3 text-sm text-red-600 bg-red-50 rounded-md">
                            {{ session('error') ?: $error_message }}
                        </div>
                    @endif

                    <div class="grid gap-4">
                        <!-- Name -->
                        <div class="grid gap-1.5">
                            <label for="name" class="block text-sm font-medium text-slate-700">Full Name</label>
                            <input 
                                wire:model="name"
                                id="name"
                                type="text"
                                placeholder="John Doe"
                                required
                                autofocus
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('name') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Email Address -->
                        <div class="grid gap-1.5">
                            <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                            <input 
                                wire:model="email"
                                id="email"
                                type="email"
                                placeholder="email@example.com"
                                required
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('email') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Role Selection -->
                        <div class="grid gap-1.5">
                            <label for="role" class="block text-sm font-medium text-slate-700">Register as</label>
                            <select 
                                wire:model="role" 
                                id="role" 
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                required
                            >
                                <option value="tenant">Tenant</option>
                                <option value="landlord">Landlord</option>
                                <option value="admin">Administrator</option>
                            </select>
                            @error('role') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password -->
                        <div class="grid gap-1.5">
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
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
                        
                        <!-- Confirm Password -->
                        <div class="grid gap-1.5">
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                            <input 
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                type="password"
                                required
                                class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            @error('password_confirmation') 
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button 
                            type="submit"
                            class="inline-flex justify-center rounded-md bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 w-full"
                        >
                            <span wire:loading.remove wire:target="register">Create account</span>
                            <span wire:loading wire:target="register">Processing...</span>
                        </button>
                    </div>
                    
                    <div class="text-center text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-blue-500 underline underline-offset-4" wire:navigate>
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