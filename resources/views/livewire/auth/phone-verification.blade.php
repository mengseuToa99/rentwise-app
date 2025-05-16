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
                <form wire:submit="{{ $otpSent ? 'verifyOTP' : 'sendOTP' }}" class="flex flex-col gap-5">
                    <div class="flex flex-col items-center gap-1 text-center">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ $otpSent ? 'Verify Phone Number' : 'Login with Phone' }}
                        </h1>
                        <p class="text-balance text-sm text-gray-500 dark:text-gray-400">
                            {{ $otpSent ? 'Enter the OTP sent to your phone' : 'Enter your phone number to receive an OTP' }}
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="p-3 text-sm text-green-600 bg-green-100/40 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-3 text-sm text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid gap-4">
                        <div class="grid gap-1.5">
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Phone Number
                            </label>
                            <input 
                                wire:model="phone"
                                id="phone"
                                type="tel"
                                placeholder="+1234567890"
                                required
                                class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                                {{ $otpSent ? 'disabled' : '' }}
                            >
                            @error('phone') 
                                <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($otpSent)
                            <div class="grid gap-1.5">
                                <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    OTP Code
                                </label>
                                <input 
                                    wire:model="otp"
                                    id="otp"
                                    type="text"
                                    placeholder="123456"
                                    required
                                    maxlength="6"
                                    class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                                >
                                @error('otp') 
                                    <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <button 
                            type="submit"
                            class="inline-flex justify-center rounded-md bg-white border border-gray-300 dark:border-zinc-700 px-5 py-2.5 text-sm font-medium text-black dark:text-black shadow-sm hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2 w-full"
                        >
                            <span wire:loading.remove wire:target="{{ $otpSent ? 'verifyOTP' : 'sendOTP' }}">
                                {{ $otpSent ? 'Verify OTP' : 'Send OTP' }}
                            </span>
                            <span wire:loading wire:target="{{ $otpSent ? 'verifyOTP' : 'sendOTP' }}">
                                Loading...
                            </span>
                        </button>

                        @if ($otpSent)
                            <div class="text-center">
                                <button 
                                    type="button" 
                                    wire:click="$set('otpSent', false)" 
                                    class="text-sm text-gray-500 dark:text-gray-400 underline"
                                >
                                    Change Phone Number
                                </button>
                            </div>
                        @endif

                        <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                            Or 
                            <a href="{{ route('login') }}" class="text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">
                                login with email
                            </a>
                        </div>
                    </div>
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