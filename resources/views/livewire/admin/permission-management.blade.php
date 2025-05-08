<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Permission Management</h1>
        </div>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="mt-6 flex flex-col lg:flex-row lg:space-x-6">
            <!-- Roles Sidebar -->
            <div class="w-full lg:w-1/4 mb-6 lg:mb-0">
                <div class="bg-white shadow overflow-hidden rounded-lg">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-md font-medium leading-6 text-gray-900">Roles</h3>
                    </div>
                    <div class="overflow-y-auto max-h-96">
                        <ul class="divide-y divide-gray-200">
                            @foreach($roles as $role)
                                <li>
                                    <button 
                                        wire:click="selectRole({{ $role->role_id }})" 
                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 {{ $selectedRoleId == $role->role_id ? 'bg-indigo-50 border-l-4 border-indigo-500' : '' }}"
                                    >
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $role->role_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $role->users_count ?? 0 }} users</div>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Permissions Content -->
            <div class="w-full lg:w-3/4">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-md font-medium leading-6 text-gray-900">
                            Permissions for: {{ $selectedRole ? $selectedRole->role_name : 'Select a role' }}
                        </h3>
                        
                        <!-- Create Permission Button -->
                        <button 
                            type="button"
                            x-data="{}"
                            x-on:click="$dispatch('open-modal', 'create-permission-modal')"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150"
                        >
                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Permission
                        </button>
                    </div>
                    
                    <div class="px-4 py-4">
                        <!-- Permission Search and Filter -->
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 mb-6">
                            <div class="w-full sm:w-2/3">
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Search Permissions</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="searchPermission"
                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md sm:text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Search by name or description..."
                                    >
                                </div>
                            </div>
                            <div class="w-full sm:w-1/3">
                                <label for="groupFilter" class="block text-xs font-medium text-gray-700 mb-1">Filter by Group</label>
                                <select
                                    wire:model.live="selectedGroupId"
                                    class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All Groups</option>
                                    @foreach($permissionGroups as $group)
                                        <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Permissions List -->
                        <div class="space-y-6">
                            @forelse($permissionsByGroup as $groupName => $permissions)
                                <div class="border-t border-gray-200 pt-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-4">{{ $groupName }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($permissions as $permission)
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input 
                                                        id="{{ $permission->permission_name }}" 
                                                        wire:click="togglePermission('{{ $permission->permission_name }}')"
                                                        type="checkbox" 
                                                        {{ in_array($permission->permission_name, $rolePermissions) ? 'checked' : '' }}
                                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                        {{ !$selectedRoleId ? 'disabled' : '' }}
                                                    >
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="{{ $permission->permission_name }}" class="font-medium text-gray-700">{{ $permission->permission_name }}</label>
                                                    <p class="text-gray-500">{{ $permission->description }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-gray-500 text-sm">No permissions found. Try a different search or create a new permission.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div
        x-data="{ open: false }"
        x-on:open-modal.window="if ($event.detail === 'create-permission-modal') open = true"
        x-on:close-modal.window="if ($event.detail === 'create-permission-modal') open = false"
        x-show="open"
        class="fixed inset-0 overflow-y-auto z-50"
        style="display: none;"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="open" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="fixed inset-0 transition-opacity"
                aria-hidden="true"
            >
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div 
                x-show="open" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Create New Permission</h3>
                            <div class="mt-4">
                                <form wire:submit.prevent="createPermission">
                                    <!-- Permission Name -->
                                    <div class="mb-4">
                                        <label for="newPermissionName" class="block text-sm font-medium text-gray-700">Permission Name</label>
                                        <div class="mt-1">
                                            <input
                                                type="text"
                                                wire:model="newPermissionName"
                                                id="newPermissionName"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                placeholder="e.g., create_user, view_reports"
                                            >
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Use lowercase letters and underscores only (e.g., manage_users)</p>
                                        @error('newPermissionName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Permission Description -->
                                    <div class="mb-4">
                                        <label for="newPermissionDescription" class="block text-sm font-medium text-gray-700">Description</label>
                                        <div class="mt-1">
                                            <input
                                                type="text"
                                                wire:model="newPermissionDescription"
                                                id="newPermissionDescription"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                placeholder="Describe what this permission allows"
                                            >
                                        </div>
                                        @error('newPermissionDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Permission Group -->
                                    <div>
                                        <label for="newPermissionGroupId" class="block text-sm font-medium text-gray-700">Permission Group</label>
                                        <select
                                            wire:model="newPermissionGroupId"
                                            id="newPermissionGroupId"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        >
                                            <option value="">Select a group</option>
                                            @foreach($permissionGroups as $group)
                                                <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('newPermissionGroupId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        wire:click="createPermission"
                        type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                        {{ !$selectedRoleId ? 'disabled' : '' }}
                    >
                        Create & Assign
                    </button>
                    <button 
                        x-on:click="open = false" 
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
