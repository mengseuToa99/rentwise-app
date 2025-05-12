@props([
    'disabled' => false,
    'error' => false,
    'rows' => 3,
])

<textarea
    rows="{{ $rows }}"
    @disabled($disabled)
    {{ $attributes->class([
        'block w-full rounded-md border border-gray-300 bg-transparent py-2 px-3 shadow-sm resize-none focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 text-sm',
        'border-red-500' => $error,
        'opacity-50 cursor-not-allowed' => $disabled,
    ]) }}
>{{ $slot }}</textarea> 