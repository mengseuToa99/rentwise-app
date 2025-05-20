<div class="text-[hsl(var(--foreground))]">
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center my-6">
            <h2 class="text-2xl font-semibold text-[hsl(var(--foreground))]">
                System Settings
            </h2>
            
            <div class="flex items-center space-x-3">
                @include('livewire.components.dark-mode-toggle')
                
                <button 
                    x-data="{}"
                    x-on:click="window.scrollTo({top: 0, behavior: 'smooth'}); $dispatch('open-modal', 'add-setting-modal')"
                    class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" aria-hidden="true" viewBox="0 0 20 20">
                        <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                    </svg>
                    <span>Add Setting</span>
                </button>
            </div>
        </div>
        
        <!-- Success message -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm dark:bg-green-900/50 dark:border-green-600 dark:text-green-100" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error message -->
        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm dark:bg-red-900/50 dark:border-red-600 dark:text-red-100" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Settings List Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-md mb-8">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left uppercase border-b border-[hsl(var(--border))] bg-[hsl(var(--muted))] dark:bg-[#1e1e1e] dark:border-[#333333] dark:text-white">
                            <th class="px-4 py-3">Setting Name</th>
                            <th class="px-4 py-3">Value</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[hsl(var(--card))] dark:bg-[#111111] divide-y divide-[hsl(var(--border))] dark:divide-[#1e1e1e]">
                        @foreach($settings as $index => $setting)
                            <tr class="text-[hsl(var(--card-foreground))] hover:bg-[hsl(var(--muted))] dark:hover:bg-[#1a1a1a]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold">{{ $setting['setting_name'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($editSettingId === $setting['setting_id'])
                                        <div>
                                            <input wire:model="settings.{{ $index }}.setting_value" 
                                                class="block w-full text-sm border-[hsl(var(--input))] bg-[hsl(var(--background))] dark:bg-[#0f172a] dark:border-gray-600 text-[hsl(var(--foreground))] dark:text-gray-300 focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400 rounded-md" 
                                                type="text" />
                                            @error("settings.{$index}.setting_value") 
                                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                    @else
                                        <div class="font-mono bg-[hsl(var(--muted))] dark:bg-[#1e293b] px-2 py-1 rounded text-[hsl(var(--muted-foreground))]">
                                            {{ $setting['setting_value'] }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $setting['description'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2 text-sm">
                                        @if($editSettingId === $setting['setting_id'])
                                            <button wire:click="updateSetting({{ $index }})" 
                                                    class="flex items-center justify-between px-2 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple" 
                                                    aria-label="Save">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" 
                                                          clip-rule="evenodd" fill-rule="evenodd"></path>
                                                </svg>
                                                Save
                                            </button>
                                            <button wire:click="cancelEdit" 
                                                    class="flex items-center justify-between px-2 py-1 text-sm font-medium leading-5 text-[hsl(var(--foreground))] transition-colors duration-150 bg-[hsl(var(--muted))] dark:bg-[#1e293b] border border-transparent rounded-md hover:bg-[hsl(var(--accent))] dark:hover:bg-[#192338] focus:outline-none focus:ring-1 focus:ring-[hsl(var(--ring))]" 
                                                    aria-label="Cancel">
                                                <svg class="w-4 h-4 mr-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                                Cancel
                                            </button>
                                        @else
                                            <button wire:click="editSetting({{ $setting['setting_id'] }})" 
                                                    class="flex items-center justify-between px-2 py-1 text-sm font-medium leading-5 text-purple-600 dark:text-purple-400 rounded-md hover:bg-[hsl(var(--muted))] dark:hover:bg-[#192338]" 
                                                    aria-label="Edit">
                                                <svg class="w-4 h-4 mr-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button wire:click="deleteSetting({{ $setting['setting_id'] }})" 
                                                    class="flex items-center justify-between px-2 py-1 text-sm font-medium leading-5 text-red-600 dark:text-red-400 rounded-md hover:bg-[hsl(var(--muted))] dark:hover:bg-[#192338]" 
                                                    aria-label="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this setting?')">
                                                <svg class="w-4 h-4 mr-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" 
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add New Setting Modal -->
    <div
        x-data="{ isModalOpen: false }"
        x-show="isModalOpen"
        x-on:open-modal.window="if ($event.detail === 'add-setting-modal') isModalOpen = true"
        x-on:keydown.escape.window="isModalOpen = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
        style="display: none;"
    >
        <div
            x-show="isModalOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 transform translate-y-1/2"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 transform translate-y-1/2"
            @click.away="isModalOpen = false"
            class="w-full px-6 py-4 overflow-hidden bg-[hsl(var(--card))] dark:bg-[#111111] rounded-t-lg sm:rounded-lg sm:m-4 sm:max-w-xl"
        >
            <header class="flex justify-between items-center pb-4 border-b border-[hsl(var(--border))] dark:border-[#1e293b]">
                <h3 class="text-lg font-semibold text-[hsl(var(--card-foreground))]">
                    Add New Setting
                </h3>
                <button
                    class="inline-flex items-center justify-center w-6 h-6 rounded-md text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] focus:outline-none focus:ring-1 focus:ring-purple-400"
                    aria-label="close"
                    @click="isModalOpen = false"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </header>
            <div class="mt-4 mb-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">
                        Setting Name
                    </label>
                    <input wire:model="newSetting.setting_name" 
                           class="block w-full mt-1 text-sm border-[hsl(var(--input))] bg-[hsl(var(--background))] dark:bg-[#0f172a] dark:border-gray-600 text-[hsl(var(--foreground))] dark:text-gray-300 focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400 rounded-md" 
                           placeholder="Enter setting name" 
                           type="text" />
                    @error('newSetting.setting_name') 
                        <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">
                        Setting Value
                    </label>
                    <input wire:model="newSetting.setting_value" 
                           class="block w-full mt-1 text-sm border-[hsl(var(--input))] bg-[hsl(var(--background))] dark:bg-[#0f172a] dark:border-gray-600 text-[hsl(var(--foreground))] dark:text-gray-300 focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400 rounded-md" 
                           placeholder="Enter setting value" 
                           type="text" />
                    @error('newSetting.setting_value') 
                        <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> 
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">
                        Description
                    </label>
                    <textarea wire:model="newSetting.description" 
                              class="block w-full mt-1 text-sm border-[hsl(var(--input))] bg-[hsl(var(--background))] dark:bg-[#0f172a] dark:border-gray-600 text-[hsl(var(--foreground))] dark:text-gray-300 focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400 rounded-md" 
                              placeholder="Enter setting description" 
                              rows="3"></textarea>
                </div>
            </div>
            <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-2 sm:space-y-0 sm:space-x-6 sm:flex-row bg-[hsl(var(--muted))] dark:bg-[#0a0a0a] border-t border-[hsl(var(--border))] dark:border-[#1e1e1e]">
                <button
                    @click="isModalOpen = false"
                    class="w-full px-5 py-2 text-sm font-medium leading-5 text-[hsl(var(--muted-foreground))] transition-colors duration-150 border border-[hsl(var(--border))] dark:border-[#1e293b] rounded-lg sm:w-auto sm:px-4 sm:py-2 hover:bg-[hsl(var(--accent))] dark:hover:bg-[#192338] focus:outline-none focus:ring-1 focus:ring-[hsl(var(--ring))]"
                >
                    Cancel
                </button>
                <button
                    wire:click="addNewSetting"
                    @click="if (!$event.detail || $event.detail === 0) isModalOpen = false"
                    class="w-full px-5 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                >
                    Add Setting
                </button>
            </footer>
        </div>
    </div>
</div> 