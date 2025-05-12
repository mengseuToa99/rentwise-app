@props([
    'id' => null,
    'disabled' => false,
])

<input 
    type="checkbox"
    id="{{ $id }}"
    @disabled($disabled)
    {{ $attributes->class([
        'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm',
        'opacity-50 cursor-not-allowed' => $disabled,
    ]) }}
> 