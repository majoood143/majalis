<div class="relative">
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button
                type="button"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 transition dark:text-gray-200 hover:text-gray-900 dark:hover:text-white"
            >
                <x-heroicon-o-language class="w-5 h-5" />
                <span>{{ $localeNames[$currentLocale] ?? $currentLocale }}</span>
                <x-heroicon-m-chevron-down class="w-4 h-4" />
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach($locales as $locale)
                <x-filament::dropdown.list.item
                    wire:click="switchLanguage('{{ $locale }}')"
                    :icon="$currentLocale === $locale ? 'heroicon-o-check' : null"
                >
                    {{ $localeNames[$locale] ?? $locale }}
                </x-filament::dropdown.list.item>
            @endforeach>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
