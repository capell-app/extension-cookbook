@props(['asset' => null, 'meta' => [], 'summary' => null, 'title' => null])

<section
    {{ $attributes->merge(['class' => 'section section-extension-cookbook-note rounded-lg border border-outline p-5']) }}
>
    @if ($title)
        <h2 class="font-display text-lg font-semibold text-on-surface">
            {{ $title }}
        </h2>
    @endif

    @if (filled($meta['note'] ?? null))
        <p class="mt-2 text-sm text-on-surface-variant">{{ $meta['note'] }}</p>
    @endif
</section>
