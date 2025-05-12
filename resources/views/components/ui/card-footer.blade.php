@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg ' . $class]) }}>
    {{ $slot }}
</div> 