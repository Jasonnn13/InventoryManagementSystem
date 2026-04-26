@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 border-black px-1 pt-1 text-sm font-semibold leading-5 tracking-wide text-black transition duration-150 ease-in-out focus:outline-none dark:border-white dark:text-white'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 tracking-wide text-neutral-500 transition duration-150 ease-in-out hover:border-neutral-400 hover:text-black focus:outline-none focus:border-neutral-400 focus:text-black dark:text-neutral-400 dark:hover:border-neutral-500 dark:hover:text-white dark:focus:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
