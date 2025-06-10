<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
                    @if($mode === 'create')
                        {{ __('maintenance.titles.new_request') }}
                    @elseif($mode === 'edit')
                        {{ __('maintenance.titles.edit_request') }}
                    @else
                        {{ __('maintenance.titles.request_details') }}
        @endif
                </h2>

                <form wire:submit="save" class="space-y-6">
                    <!-- Property and Unit Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Property Selection -->
                        <div>
                            <x-input-label for="selectedProperty" :value="__('Property')" />
                            <select wire:model.live="selectedProperty" @if($mode === 'show') disabled @endif class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white">
                                <option value="">-- Select Property --</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->property_id }}">{{ $property->property_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('selectedProperty')" class="mt-2" />
                        </div>

                        <!-- Unit Selection -->
                        <div>
                            <x-input-label for="selectedUnit" :value="__('Room')" />
                            <select wire:model="selectedUnit" @if($mode === 'show') disabled @endif class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white">
                                <option value="">-- Select Room --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->room_id }}">Room {{ $unit->room_number }} - {{ $unit->room_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('selectedUnit')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mt-4">
                        <x-input-label for="title" :value="__('Title')" />
                        <input 
                            type="text" 
                            id="title"
                            wire:model="title" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white" 
                            placeholder="Enter a brief title for your maintenance request"
                            @if($mode === 'show' || ($mode === 'edit' && $isLandlord)) disabled @endif 
                        />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                    <!-- Priority and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Priority -->
                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select wire:model="priority" @if($mode === 'show' || ($mode === 'edit' && $isLandlord)) disabled @endif class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white">
                                @foreach($priorities as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                    </div>

                    @if($isLandlord || $mode === 'show')
                        <!-- Status -->
                    <div>
                                <x-input-label for="status" :value="__('Status')" />
                            <select wire:model="status" @if(!$isLandlord || $mode === 'show') disabled @endif class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea 
                            id="description"
                            wire:model="description" 
                            @if($mode === 'show' || ($mode === 'edit' && $isLandlord)) disabled @endif 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white" 
                            rows="4"
                            placeholder="Describe the maintenance issue in detail"
                        ></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                    @if($isLandlord || $mode === 'show')
                        <!-- Landlord Notes -->
                        <div class="mt-4">
                            <x-input-label for="landlord_notes" :value="__('Landlord Notes')" />
                            <textarea 
                                id="landlord_notes"
                                wire:model="landlord_notes" 
                                @if(!$isLandlord || $mode === 'show') disabled @endif 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white" 
                                rows="4"
                                placeholder="Add your notes about this maintenance request"
                            ></textarea>
                            <x-input-error :messages="$errors->get('landlord_notes')" class="mt-2" />
                        </div>
                    @endif

                    <!-- Photos -->
                    <div class="mt-4">
                        <x-input-label :value="__('Photos')" />
                        
                        <!-- Existing Photos -->
                        @if($existingPhotos && (is_array($existingPhotos) ? !empty($existingPhotos) : $existingPhotos->isNotEmpty()))
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($existingPhotos as $photo)
                                    <div class="relative">
                                        @if(Storage::exists($photo->photo_path))
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Maintenance Photo" class="w-full h-48 object-cover rounded-lg shadow-md border-2 border-white dark:border-zinc-800">
                                        @else
                                            <div class="w-full h-48 flex items-center justify-center bg-gray-100 dark:bg-zinc-700 rounded-lg shadow-md border-2 border-white dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Image not found</span>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-2 rounded-b-lg">
                                            <p>{{ ucfirst($photo->photo_type) }} photo</p>
                                            <p>By: {{ ucfirst($photo->uploaded_by_type) }}</p>
                                            <p>{{ $photo->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        @if(($mode === 'edit' && ((!$isLandlord && $photo->uploaded_by_type === 'tenant') || ($isLandlord && $photo->uploaded_by_type === 'landlord'))))
                                            <button type="button" wire:click="deleteExistingPhoto({{ $photo->photo_id }})" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Photo Upload -->
                        @if($mode !== 'show')
                            <div class="mt-4">
                                <input type="file" wire:model="photos" multiple accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    dark:file:bg-indigo-900/50 dark:file:text-indigo-400
                                    hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/60">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    @if($isLandlord)
                                        Upload photos after maintenance work
                                    @else
                                        Upload photos of the maintenance issue
                                    @endif
                                </p>
                                <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                                
                                @if(!empty($photos))
                                    <div class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach($photos as $index => $photo)
                                            <div class="relative">
                                                @if($photo->temporaryUrl())
                                                    <img src="{{ $photo->temporaryUrl() }}" alt="Temporary Photo" class="w-full h-48 object-cover rounded-lg shadow-md border-2 border-white dark:border-zinc-800">
                                                    <button type="button" wire:click="removePhoto({{ $index }})" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                        </div>
                    @endif
                </div>

                    <!-- Action Buttons -->
                    @if($mode !== 'show')
                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancel
                    </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ $mode === 'create' ? 'Submit' : 'Save Changes' }}
                    </button>
                </div>
                    @else
                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Back to List
                            </a>
                        </div>
                    @endif
            </form>
            </div>
        </div>
    </div>
</div> 