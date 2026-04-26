@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-sm font-medium text-neutral-700 dark:text-neutral-300']) }}>
        {{ $status }}
    </div>
@endif
