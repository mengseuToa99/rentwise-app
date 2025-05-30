<div class="py-6">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">User Management</h1>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New User
            </button>
        </div>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Error Message -->
        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Search Box -->
        <div class="mt-4">
            <div class="max-w-md">
                <div class="relative flex items-center w-full h-12 rounded-lg focus-within:shadow-lg bg-white dark:bg-zinc-800 overflow-hidden border border-gray-300 dark:border-zinc-700">
                    <div class="grid place-items-center h-full w-12 text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        class="peer h-full w-full outline-none text-sm text-gray-700 dark:text-gray-200 pr-2 dark:bg-zinc-800 dark:placeholder-gray-400"
                        type="text"
                        placeholder="Search users..." 
                    />
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="mt-4">
            <div class="shadow overflow-hidden border-b border-gray-200 dark:border-zinc-700 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Roles</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                                                <span class="font-medium text-gray-700 dark:text-gray-200">{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @foreach ($user->roles as $role)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $role->role_name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @elseif ($user->status === 'inactive')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Inactive
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Suspended
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="openEditModal({{ $user->user_id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">Edit</button>
                                    <button wire:click="sendResetPassword({{ $user->user_id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">Send Reset Password</button>
                                    <button wire:click="deleteUser({{ $user->user_id }})" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No users found. Try a different search or add a new user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>

        <!-- Modal for Creating/Editing Users -->
        @if ($isModalOpen)
            <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-zinc-900 dark:bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <div class="bg-white dark:bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="mb-6">
                                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                                    {{ $modalMode === 'create' ? 'Create New User' : 'Edit User' }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $modalMode === 'create' ? 'Add a new user to the system' : 'Update user information' }}
                                </p>
                            </div>

                            <form wire:submit.prevent="saveUser" class="space-y-6">
                                <!-- Personal Information -->
                                <div class="border-b border-gray-200 dark:border-zinc-700 pb-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h4>
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <!-- First Name -->
                                        <div>
                                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                                            <input 
                                                wire:model="first_name"
                                                type="text"
                                                id="first_name"
                                                placeholder="John"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('first_name') 
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Last Name -->
                                        <div>
                                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                                            <input 
                                                wire:model="last_name"
                                                type="text"
                                                id="last_name"
                                                placeholder="Doe"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('last_name') 
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Phone Number -->
                                        <div>
                                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                            <input 
                                                wire:model="phone_number"
                                                type="tel"
                                                id="phone_number"
                                                placeholder="+1 (555) 000-0000"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('phone_number') 
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Status -->
                                        <div>
                                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Status</label>
                                            <select 
                                                wire:model="status"
                                                id="status"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="suspended">Suspended</option>
                                            </select>
                                            @error('status') 
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Information -->
                                <div class="border-b border-gray-200 dark:border-zinc-700 pb-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Account Information</h4>
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <!-- Username -->
                                        <div>
                                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                                            <input 
                                                wire:model="username"
                                                type="text"
                                                id="username"
                                                placeholder="johndoe"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('username') 
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                            <input 
                                                wire:model="email"
                                                type="email"
                                                id="email"
                                                placeholder="john.doe@example.com"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('email') 
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="sm:col-span-2">
                                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $modalMode === 'create' ? 'Password' : 'New Password (leave blank to keep current)' }}
                                            </label>
                                            <input 
                                                wire:model="password"
                                                type="password"
                                                id="password"
                                                placeholder="••••••••"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @if($modalMode === 'create')
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Password must be at least 8 characters long and contain at least one uppercase letter, one number, and one special character.
                                                </p>
                                            @endif
                                            @error('password') 
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- User Roles -->
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">User Roles</h4>
                                    <div class="space-y-3">
                                        @foreach ($roles as $role)
                                            <div class="relative flex items-start">
                                                <div class="flex h-5 items-center">
                                                    <input 
                                                        type="checkbox"
                                                        id="role-{{ $role->role_id }}"
                                                        value="{{ $role->role_id }}"
                                                        wire:model="selectedRoles"
                                                        class="h-4 w-4 rounded border-gray-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500 dark:bg-zinc-900"
                                                    >
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="role-{{ $role->role_id }}" class="font-medium text-gray-700 dark:text-gray-300">{{ $role->role_name }}</label>
                                                    @if($role->description)
                                                        <p class="text-gray-500 dark:text-gray-400">{{ $role->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        @error('selectedRoles') 
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-zinc-700">
                            <button 
                                wire:click="saveUser"
                                type="button"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3"
                            >
                                {{ $modalMode === 'create' ? 'Create User' : 'Save Changes' }}
                            </button>
                            <button 
                                wire:click="closeModal"
                                type="button"
                                class="mt-3 inline-flex justify-center rounded-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
