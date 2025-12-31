<x-filament-panels::page>
    {{-- Hall Selector --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Hall Tabs --}}
            <div class="flex flex-wrap gap-2">
                @foreach($this->getOwnerHalls as $hall)
                    <button
                        wire:click="setHall({{ $hall->id }})"
                        type="button"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                            {{ $selectedHallId === $hall->id
                                ? 'bg-primary-600 text-white'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                    >
                        {{ $hall->getTranslation('name', app()->getLocale()) }}
                        <span class="ml-1 text-xs opacity-75">({{ $hall->images_count }})</span>
                    </button>
                @endforeach
            </div>

            {{-- Quick Stats --}}
            @if($this->selectedHall)
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-500 dark:text-gray-400">
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $this->galleryStats['total'] }}</span>
                        {{ __('owner.gallery.stats.total') }}
                    </span>
                    <span class="text-success-600 dark:text-success-400">
                        <span class="font-semibold">{{ $this->galleryStats['active'] }}</span>
                        {{ __('owner.gallery.stats.active') }}
                    </span>
                    <span class="text-warning-600 dark:text-warning-400">
                        <span class="font-semibold">{{ $this->galleryStats['featured'] }}</span>
                        {{ __('owner.gallery.stats.featured') }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    @if($this->selectedHall)
        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Type Filter --}}
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.gallery.filters.type') }}:</span>
                    <div class="flex gap-1">
                        <button
                            wire:click="setTypeFilter(null)"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ !$typeFilter ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            {{ __('owner.gallery.filters.all_types') }}
                        </button>
                        @foreach(['gallery', 'exterior', 'interior', 'floor_plan'] as $type)
                            <button
                                wire:click="setTypeFilter('{{ $type }}')"
                                class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                    {{ $typeFilter === $type ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                            >
                                {{ __("owner.gallery.types.{$type}") }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>

                {{-- Status Filter --}}
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.gallery.filters.status') }}:</span>
                    <div class="flex gap-1">
                        <button
                            wire:click="setStatusFilter(null)"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ !$statusFilter ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            {{ __('owner.gallery.filters.all') }}
                        </button>
                        <button
                            wire:click="setStatusFilter('active')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ $statusFilter === 'active' ? 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            {{ __('owner.gallery.filters.active_only') }}
                        </button>
                        <button
                            wire:click="setStatusFilter('featured')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ $statusFilter === 'featured' ? 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            {{ __('owner.gallery.filters.featured_only') }}
                        </button>
                        <button
                            wire:click="setStatusFilter('inactive')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ $statusFilter === 'inactive' ? 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            {{ __('owner.gallery.filters.inactive_only') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gallery Grid --}}
        @if($this->hallImages->count() > 0)
            <div 
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6"
                x-data="{
                    init() {
                        if (typeof Sortable !== 'undefined') {
                            new Sortable(this.$refs.gallery, {
                                animation: 150,
                                ghostClass: 'opacity-50',
                                onEnd: (evt) => {
                                    const items = [...this.$refs.gallery.children].map(el => el.dataset.id);
                                    @this.updateOrder(items);
                                }
                            });
                        }
                    }
                }"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('owner.gallery.manage.gallery_images') }}
                        <span class="text-sm font-normal text-gray-500">({{ $this->hallImages->count() }})</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('owner.gallery.manage.drag_to_reorder') }}
                    </p>
                </div>

                <div 
                    x-ref="gallery"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4"
                >
                    @foreach($this->hallImages as $image)
                        <div 
                            data-id="{{ $image->id }}"
                            class="relative group cursor-move"
                        >
                            {{-- Image Container --}}
                            <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 border-2 
                                {{ $image->is_featured ? 'border-warning-500' : 'border-transparent' }}
                                {{ !$image->is_active ? 'opacity-50' : '' }}
                            ">
                                <img 
                                    src="{{ Storage::disk('public')->url($image->thumbnail_path ?? $image->image_path) }}"
                                    alt="{{ $image->alt_text ?? 'Hall image' }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            </div>

                            {{-- Badges --}}
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                @if($image->is_featured)
                                    <span class="px-2 py-0.5 bg-warning-500 text-white text-xs font-medium rounded-full">
                                        {{ __('owner.gallery.badges.featured') }}
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 bg-gray-900/70 text-white text-xs rounded-full">
                                    {{ __("owner.gallery.types.{$image->type}") }}
                                </span>
                            </div>

                            {{-- Order Badge --}}
                            <div class="absolute top-2 right-2 w-6 h-6 bg-gray-900/70 text-white text-xs font-medium rounded-full flex items-center justify-center">
                                {{ $image->order }}
                            </div>

                            {{-- Hover Overlay --}}
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex flex-col items-center justify-center gap-2">
                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2">
                                    {{-- Toggle Featured --}}
                                    <button
                                        wire:click="toggleFeatured({{ $image->id }})"
                                        type="button"
                                        class="p-2 rounded-lg transition-colors
                                            {{ $image->is_featured 
                                                ? 'bg-warning-500 text-white' 
                                                : 'bg-white/20 text-white hover:bg-warning-500' }}"
                                        title="{{ $image->is_featured ? __('owner.gallery.actions.unmark_featured') : __('owner.gallery.actions.mark_featured') }}"
                                    >
                                        <x-heroicon-s-star class="w-4 h-4" />
                                    </button>

                                    {{-- Toggle Active --}}
                                    <button
                                        wire:click="toggleActive({{ $image->id }})"
                                        type="button"
                                        class="p-2 rounded-lg transition-colors
                                            {{ $image->is_active 
                                                ? 'bg-success-500 text-white' 
                                                : 'bg-white/20 text-white hover:bg-success-500' }}"
                                        title="{{ $image->is_active ? __('owner.gallery.actions.deactivate') : __('owner.gallery.actions.activate') }}"
                                    >
                                        @if($image->is_active)
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        @else
                                            <x-heroicon-o-eye-slash class="w-4 h-4" />
                                        @endif
                                    </button>

                                    {{-- Set as Hall Featured --}}
                                    <button
                                        wire:click="setAsFeaturedImage({{ $image->id }})"
                                        type="button"
                                        class="p-2 bg-white/20 text-white rounded-lg hover:bg-primary-500 transition-colors"
                                        title="{{ __('owner.gallery.actions.set_hall_featured') }}"
                                    >
                                        <x-heroicon-o-photo class="w-4 h-4" />
                                    </button>

                                    {{-- Delete --}}
                                    <button
                                        wire:click="deleteImage({{ $image->id }})"
                                        wire:confirm="{{ __('owner.gallery.confirm_delete') }}"
                                        type="button"
                                        class="p-2 bg-white/20 text-white rounded-lg hover:bg-danger-500 transition-colors"
                                        title="{{ __('owner.gallery.actions.delete') }}"
                                    >
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>

                                {{-- Edit Link --}}
                                <a 
                                    href="{{ \App\Filament\Owner\Resources\GalleryResource::getUrl('edit', ['record' => $image->id]) }}"
                                    class="text-xs text-white/80 hover:text-white underline"
                                >
                                    {{ __('owner.gallery.actions.edit_details') }}
                                </a>
                            </div>

                            {{-- Status Indicator --}}
                            @if(!$image->is_active)
                                <div class="absolute bottom-2 left-2 right-2">
                                    <span class="block w-full px-2 py-1 bg-gray-900/80 text-white text-xs text-center rounded-lg">
                                        {{ __('owner.gallery.status.inactive') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
                <x-heroicon-o-photo class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('owner.gallery.empty.heading') }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('owner.gallery.empty.description') }}
                </p>
                <a 
                    href="{{ \App\Filament\Owner\Resources\GalleryResource::getUrl('upload') }}?hall_id={{ $selectedHallId }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors"
                >
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    {{ __('owner.gallery.actions.upload') }}
                </a>
            </div>
        @endif
    @else
        {{-- No Hall Selected --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <x-heroicon-o-building-office-2 class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('owner.gallery.manage.no_hall_selected') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('owner.gallery.manage.select_hall_prompt') }}
            </p>
        </div>
    @endif

    {{-- Sortable.js CDN --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @endpush
</x-filament-panels::page>
