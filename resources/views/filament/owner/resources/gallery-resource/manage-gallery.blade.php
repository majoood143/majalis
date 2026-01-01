<x-filament-panels::page>
    {{-- Hall Selector --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($this->getOwnerHalls() as $hall)
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

        @php $stats = $this->getGalleryStats(); @endphp
        @if($selectedHallId)
            <div class="flex items-center gap-4 text-sm border-t dark:border-gray-700 pt-4">
                <span class="text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</span> total
                </span>
                <span class="text-success-600 dark:text-success-400">
                    <span class="font-semibold">{{ $stats['active'] }}</span> active
                </span>
                <span class="text-warning-600 dark:text-warning-400">
                    <span class="font-semibold">{{ $stats['featured'] }}</span> featured
                </span>
            </div>
        @endif
    </div>

    @if($selectedHallId)
        @php $images = $this->getHallImages(); @endphp
        
        @if($images->count() > 0)
            {{-- Gallery Grid --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach($images as $image)
                        <div class="relative group">
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
                                        Featured
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 bg-gray-900/70 text-white text-xs rounded-full">
                                    {{ ucfirst(str_replace('_', ' ', $image->type)) }}
                                </span>
                            </div>

                            {{-- Hover Overlay --}}
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex flex-col items-center justify-center gap-2">
                                <div class="flex items-center gap-2">
                                    {{-- Toggle Featured --}}
                                    <button
                                        wire:click="toggleFeatured({{ $image->id }})"
                                        type="button"
                                        class="p-2 rounded-lg transition-colors {{ $image->is_featured ? 'bg-warning-500 text-white' : 'bg-white/20 text-white hover:bg-warning-500' }}"
                                        title="{{ $image->is_featured ? 'Unmark Featured' : 'Mark Featured' }}"
                                    >
                                        <x-heroicon-s-star class="w-4 h-4" />
                                    </button>

                                    {{-- Toggle Active --}}
                                    <button
                                        wire:click="toggleActive({{ $image->id }})"
                                        type="button"
                                        class="p-2 rounded-lg transition-colors {{ $image->is_active ? 'bg-success-500 text-white' : 'bg-white/20 text-white hover:bg-success-500' }}"
                                        title="{{ $image->is_active ? 'Deactivate' : 'Activate' }}"
                                    >
                                        @if($image->is_active)
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        @else
                                            <x-heroicon-o-eye-slash class="w-4 h-4" />
                                        @endif
                                    </button>

                                    {{-- Delete --}}
                                    <button
                                        wire:click="deleteImage({{ $image->id }})"
                                        wire:confirm="Are you sure you want to delete this image?"
                                        type="button"
                                        class="p-2 bg-white/20 text-white rounded-lg hover:bg-danger-500 transition-colors"
                                        title="Delete"
                                    >
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>

                                <a 
                                    href="{{ \App\Filament\Owner\Resources\GalleryResource::getUrl('edit', ['record' => $image->id]) }}"
                                    class="text-xs text-white/80 hover:text-white underline"
                                >
                                    Edit Details
                                </a>
                            </div>

                            {{-- Status Indicator --}}
                            @if(!$image->is_active)
                                <div class="absolute bottom-2 left-2 right-2">
                                    <span class="block w-full px-2 py-1 bg-gray-900/80 text-white text-xs text-center rounded-lg">
                                        Inactive
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
                    No Images Yet
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Upload images to showcase your hall.
                </p>
                <a 
                    href="{{ \App\Filament\Owner\Resources\GalleryResource::getUrl('upload') }}?hall_id={{ $selectedHallId }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors"
                >
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    Upload Images
                </a>
            </div>
        @endif
    @else
        {{-- No Hall Selected --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <x-heroicon-o-building-office-2 class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                No Hall Selected
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                Please select a hall to manage its gallery.
            </p>
        </div>
    @endif
</x-filament-panels::page>
