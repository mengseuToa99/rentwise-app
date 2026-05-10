@props([
    'options' => [],
    'placeholder' => null,
    'disabled' => false,
    'error' => false,
])

<select
    @disabled($disabled)
    {{ $attributes->class([
        'block w-full rounded-md border border-gray-300 bg-white text-gray-900 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400',
        'border-red-500' => $error,
        'opacity-50 cursor-not-allowed' => $disabled,
    ]) }}
>
    @if($placeholder)
        <option value="" disabled selected>{{ $placeholder }}</option>
    @endif
    
    @foreach($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
    
    {{ $slot }}
</select> 