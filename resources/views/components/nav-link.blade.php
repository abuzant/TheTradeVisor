@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 pt-1 border-b-2 border-indigo-500 text-sm font-semibold leading-5 text-indigo-600 focus:outline-none focus:border-indigo-700 transition duration-300 ease-in-out'
            : 'inline-flex items-center px-4 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 hover:text-indigo-600 hover:border-indigo-300 focus:outline-none focus:text-indigo-600 focus:border-indigo-300 transition duration-300 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
