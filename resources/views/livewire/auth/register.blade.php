<div class="grid h-screen lg:grid-cols-2 overflow-hidden">
    <div class="flex flex-col gap-2 p-3 md:p-5 bg-gray-50 dark:bg-zinc-900 relative">
        <div class="flex justify-center gap-2 md:justify-start">
            <a href="#" class="flex items-center gap-1 font-medium">
                <div class="flex h-5 w-5 items-center justify-center rounded-md bg-gray-800 text-white dark:bg-white dark:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                        <path d="M4 5v14h6v-3a3 3 0 0 1 3-3 3 3 0 0 1 3 3v3h6V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2Z"></path>
                        <path d="M22 19v2H2v-2"></path>
                    </svg>
                </div>
                <span class="text-sm text-gray-800 dark:text-gray-200">Rentwise</span>
            </a>
        </div>
        <div class="flex flex-1 items-center justify-center">
            <div class="w-full max-w-sm">
                <form wire:submit="register" class="flex flex-col gap-3">
                    <div class="flex flex-col items-center gap-1 text-center">
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Create an account</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Enter your details below
                        </p>
                    </div>

                    @if(session('error') || $error_message)
                        <div class="p-2 text-xs text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                            {{ session('error') ?: $error_message }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="p-2 text-xs text-green-600 bg-green-100/40 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid gap-2.5">
                        <!-- Name -->
                        <div class="grid gap-1">
                            <label for="name" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                            <input 
                                wire:model="name"
                                id="name"
                                type="text"
                                placeholder="John Doe"
                                required
                                autofocus
                                class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                            >
                            @error('name') 
                                <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Email Address -->
                        <div class="grid gap-1">
                            <label for="email" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Email address</label>
                            <input 
                                wire:model="email"
                                id="email"
                                type="email"
                                placeholder="email@example.com"
                                required
                                class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                            >
                            @error('email') 
                                <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password -->
                        <div class="grid gap-1">
                            <label for="password" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Password</label>
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
                        
                        <!-- Confirm Password -->
                        <div class="grid gap-1">
                            <label for="password_confirmation" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Confirm password</label>
                            <input 
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                type="password"
                                required
                                class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                            >
                            @error('password_confirmation') 
                                <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Role Selection with Icons -->
                        <div class="grid gap-1.5">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Register as</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <!-- Tenant Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="role" value="tenant" class="sr-only peer" checked>
                                    <div class="flex flex-col items-center py-1.5 rounded-md border border-gray-300 bg-transparent dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 peer-checked:border-gray-800 dark:peer-checked:border-white peer-checked:bg-gray-50 dark:peer-checked:bg-zinc-800 transition-all h-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-gray-500 dark:text-gray-400 peer-checked:text-gray-800 dark:peer-checked:text-white">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 peer-checked:text-gray-800 dark:peer-checked:text-white mt-0.5">Tenant</span>
                                    </div>
                                </label>
                                
                                <!-- Landlord Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="role" value="landlord" class="sr-only peer">
                                    <div class="flex flex-col items-center py-1.5 rounded-md border border-gray-300 bg-transparent dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 peer-checked:border-gray-800 dark:peer-checked:border-white peer-checked:bg-gray-50 dark:peer-checked:bg-zinc-800 transition-all h-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-gray-500 dark:text-gray-400 peer-checked:text-gray-800 dark:peer-checked:text-white">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 peer-checked:text-gray-800 dark:peer-checked:text-white mt-0.5">Landlord</span>
                                    </div>
                                </label>
                            </div>
                            @error('role') 
                                <p class="text-xs text-red-500 dark:text-red-400 text-center">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button 
                            type="submit"
                            class="inline-flex justify-center rounded-md bg-white border border-gray-300 dark:border-zinc-700 px-5 py-2.5 text-sm font-medium text-black dark:text-black shadow-sm hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2 w-full mt-1"
                        >
                            <span wire:loading.remove wire:target="register">Create account</span>
                            <span wire:loading wire:target="register">Processing...</span>
                        </button>
                    </div>
                    
                    <div class="text-center text-xs mt-1 text-gray-600 dark:text-gray-400">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-gray-800 dark:text-white font-medium hover:underline underline-offset-2" wire:navigate>
                            Log in
                        </a>
                    </div>

                    <!-- Theme Toggle Button removed from here and positioned fixed on the screen -->
                </form>
            </div>
        </div>
    </div>
    <div class="relative hidden bg-gray-100 dark:bg-zinc-950 lg:block">
        <img
            src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1470&auto=format&fit=crop"
            alt="Property Image"
            class="absolute inset-0 h-full w-full object-cover"
        />
    </div>
</div> 

<!-- Alpine.js is now loaded via Vite bundle -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Listen for Livewire events
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showSuccess', (message) => {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#4F46E5',
                background: document.documentElement.classList.contains('dark') ? '#18181B' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/dashboard';
                }
            });
        });

        Livewire.on('showError', (message) => {
            // Ensure message is a string
            const errorMessage = typeof message === 'object' ? JSON.stringify(message) : message;
            
            Swal.fire({
                title: 'Registration Error',
                html: `<div class="text-left">
                    <p class="mb-2">${errorMessage}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Please check your input and try again.</p>
                </div>`,
                icon: 'error',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#EF4444',
                background: document.documentElement.classList.contains('dark') ? '#18181B' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000',
                customClass: {
                    htmlContainer: 'text-left'
                }
            });
        });
    });
</script> 