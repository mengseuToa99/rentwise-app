<div>
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            System Settings
        </h2>
        
        <!-- Success message -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Error message -->
        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Add New Setting Form -->
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">Add New Setting</h4>
            <div class="grid gap-6 mb-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Setting Name</span>
                        <input wire:model="newSetting.setting_name" 
                               class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                               placeholder="Enter setting name" 
                               type="text" />
                    </label>
                    @error('newSetting.setting_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Setting Value</span>
                        <input wire:model="newSetting.setting_value" 
                               class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                               placeholder="Enter setting value" 
                               type="text" />
                    </label>
                    @error('newSetting.setting_value') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Description</span>
                        <textarea wire:model="newSetting.description" 
                                 class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                                 placeholder="Enter setting description" 
                                 rows="2"></textarea>
                    </label>
                </div>
            </div>
            <div class="flex justify-end">
                <button wire:click="addNewSetting" 
                        class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Add Setting
                </button>
            </div>
        </div>

        <!-- Settings List -->
        <div class="w-full overflow-hidden rounded-lg shadow-xs mb-8">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Setting Name</th>
                            <th class="px-4 py-3">Value</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @foreach($settings as $index => $setting)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold">{{ $setting['setting_name'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($editSettingId === $setting['setting_id'])
                                        <input wire:model="settings.{{ $index }}.setting_value" 
                                               class="block w-full text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                                               type="text" />
                                        @error("settings.{$index}.setting_value") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    @else
                                        {{ $setting['setting_value'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $setting['description'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        @if($editSettingId === $setting['setting_id'])
                                            <button wire:click="updateSetting({{ $index }})" 
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" 
                                                    aria-label="Save">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" 
                                                          clip-rule="evenodd" fill-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                            <button wire:click="cancelEdit" 
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-red-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" 
                                                    aria-label="Cancel">
                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <button wire:click="editSetting({{ $setting['setting_id'] }})" 
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" 
                                                    aria-label="Edit">
                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteSetting({{ $setting['setting_id'] }})" 
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" 
                                                    aria-label="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this setting?')">
                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" 
                                                          clip-rule="evenodd"></path>
                                                </svg>
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
</div> 