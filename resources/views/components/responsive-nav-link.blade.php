@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-black bg-neutral-100 px-4 py-3 text-start text-base font-semibold text-black transition duration-150 ease-in-out focus:outline-none dark:border-white dark:bg-neutral-900 dark:text-white'
            : 'block w-full border-l-4 border-transparent px-4 py-3 text-start text-base font-medium text-neutral-600 transition duration-150 ease-in-out hover:border-neutral-400 hover:bg-neutral-50 hover:text-black focus:outline-none focus:border-neutral-400 focus:bg-neutral-50 focus:text-black dark:text-neutral-400 dark:hover:border-neutral-500 dark:hover:bg-neutral-900 dark:hover:text-white dark:focus:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
