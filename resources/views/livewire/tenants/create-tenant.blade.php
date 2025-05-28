<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create New Tenant</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new tenant to the system</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                        <input 
                            wire:model="first_name"
                            type="text"
                            id="first_name"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('first_name') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                        <input 
                            wire:model="last_name"
                            type="text"
                            id="last_name"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('last_name') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input 
                            wire:model="username"
                            type="text"
                            id="username"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('username') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input 
                            wire:model="email"
                            type="email"
                            id="email"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('email') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                        <input 
                            wire:model="phone_number"
                            type="text"
                            id="phone_number"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('phone_number') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input 
                            wire:model="password"
                            type="password"
                            id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('password') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                        <input 
                            wire:model="password_confirmation"
                            type="password"
                            id="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('password_confirmation') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a 
                        href="{{ route('tenants.index') }}" 
                        class="inline-flex justify-center rounded-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-2 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Create Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 