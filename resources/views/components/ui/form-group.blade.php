@props([
    'label' => '',
    'for' => null,
    'error' => null,
    'helpText' => null
])

<div class="space-y-2">
    @if($label)
        <x-ui.label :for="$for" :error="$error">{{ $label }}</x-ui.label>
    @endif
    
    {{ $slot }}
    
    @if($error)
        <p class="text-xs font-medium text-red-500">{{ $error }}</p>
    @endif
    
    @if($helpText)
        <p class="text-xs text-gray-500">{{ $helpText }}</p>
    @endif
</div> 