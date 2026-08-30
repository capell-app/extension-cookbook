<main
    class="mx-auto max-w-5xl px-6 py-16"
    aria-labelledby="reference-title"
>
    <div class="grid gap-10 md:grid-cols-[1.2fr_0.8fr] md:items-start">
        <div class="space-y-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">{{ __('capell-extension-cookbook::frontend.eyebrow') }}</p>
            <h1
                id="reference-title"
                class="font-display text-4xl font-semibold tracking-tight text-on-surface"
            >
                {{ __('capell-extension-cookbook::frontend.title') }}
            </h1>
            <p class="max-w-2xl text-lg leading-8 text-on-surface-variant">{{ __('capell-extension-cookbook::frontend.description') }}</p>
        </div>
        <div
            class="rounded-lg border border-outline bg-surface-raised p-6 shadow-sm"
        >
            <p class="text-sm font-semibold text-on-surface">{{ __('capell-extension-cookbook::frontend.included_examples') }}</p>
            <ul class="mt-4 grid gap-3 text-sm text-on-surface-variant">
                @foreach ($examples as $example)
                    <li class="flex gap-3">
                        <span
                            aria-hidden="true"
                            class="text-primary"
                            >✓</span
                        ><span>{{ $example }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</main>
