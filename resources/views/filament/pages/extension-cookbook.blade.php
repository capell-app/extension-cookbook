<x-filament-panels::page>
    @foreach ($this->coverage() as $group => $entries)
        <x-filament::section
            :heading="__('capell-extension-cookbook::admin.coverage.groups.' . strtolower($group))"
        >
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($entries as $entry)
                    <div
                        class="rounded-xl border border-gray-200 p-4 dark:border-white/10"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span
                                class="font-medium"
                                >{{ $entry['type'] }}</span
                            >
                            <x-filament::badge
                                >{{ $entry['status'] }}</x-filament::badge
                            >
                        </div>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $entry['purpose'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endforeach

    <x-filament::section
        :heading="__('capell-extension-cookbook::admin.dashboard.registrar_title')"
    >
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('capell-extension-cookbook::admin.dashboard.registrar_description') }}
        </p>

        @foreach ($this->registrarCoverage() as $group => $methods)
            <h3 class="mb-2 mt-4 text-sm font-semibold">
                {{ __('capell-extension-cookbook::admin.coverage.registrar.groups.' . $group) }}
            </h3>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($methods as $method)
                    <div
                        class="rounded-xl border border-gray-200 p-4 dark:border-white/10"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <code
                                class="font-medium"
                                >{{ $method['method'] }}</code
                            >
                            <x-filament::badge
                                >{{ $method['status'] }}</x-filament::badge
                            >
                        </div>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $method['reason'] }}</p>
                    </div>
                @endforeach
            </div>
        @endforeach
    </x-filament::section>
</x-filament-panels::page>
