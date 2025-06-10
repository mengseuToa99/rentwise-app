<div>
    <form wire:submit="save" class="space-y-6">
        <div>
            <x-input-label for="title" :value="__('maintenance.form.title')" />
            <x-text-input wire:model="title" type="text" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" :value="__('maintenance.form.description')" />
            <x-textarea wire:model="description" class="mt-1 block w-full" rows="4" required />
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="priority" :value="__('maintenance.priority.label')" />
            <x-select wire:model="priority" class="mt-1 block w-full">
                <option value="low">{{ __('maintenance.priority.low') }}</option>
                <option value="medium">{{ __('maintenance.priority.medium') }}</option>
                <option value="high">{{ __('maintenance.priority.high') }}</option>
                <option value="urgent">{{ __('maintenance.priority.urgent') }}</option>
            </x-select>
            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="photos" :value="__('maintenance.form.photos')" />
            <input type="file" wire:model="photos" multiple class="mt-1 block w-full" accept="image/*" />
            <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button type="button" wire:click="$dispatch('closeModal')" class="mr-3">
                {{ __('maintenance.cancel') }}
            </x-secondary-button>
            <x-primary-button type="submit">
                {{ __('maintenance.submit') }}
            </x-primary-button>
        </div>
    </form>
</div> 