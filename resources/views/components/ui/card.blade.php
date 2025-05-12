@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-gray-200 bg-white shadow-sm ' . $class]) }}>
    {{ $slot }}
</div> 