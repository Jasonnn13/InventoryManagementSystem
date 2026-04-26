@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200']) }}>
    {{ $value ?? $slot }}
</label>
