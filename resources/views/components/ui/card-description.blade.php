@props(['class' => ''])

<p {{ $attributes->merge(['class' => 'mt-1 text-sm text-gray-500 ' . $class]) }}>
    {{ $slot }}
</p> 