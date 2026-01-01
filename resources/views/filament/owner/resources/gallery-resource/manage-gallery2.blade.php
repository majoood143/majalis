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
                        total
                    </span>
                    <span class="text-success-600 dark:text-success-400">
                        <span class="font-semibold">{{ $this->galleryStats['active'] }}</span>
                        active
                    </span>
                    <span class="text-warning-600 dark:text-warning-400">
                        <span class="font-semibold">{{ $this->galleryStats['featured'] }}</span>
                        featured
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
                    <span class="text-sm text-gray-600 dark:text-gray-400">Type:</span>
                    <div class="flex gap-1">
                        <button
                            wire:click="setTypeFilter(null)"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ !$typeFilter ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            All
                        </button>
                        @foreach(['gallery', 'exterior', 'interior', 'floor_plan'] as $type)
                            <button
                                wire:click="setTypeFilter('{{ $type }}')"
                                class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                    {{ $typeFilter === $type ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                            >
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>

                {{-- Status Filter --}}
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                    <div class="flex gap-1">
                        <button
                            wire:click="setStatusFilter(null)"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ !$statusFilter ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            All
                        </button>
                        <button
                            wire:click="setStatusFilter('active')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ $statusFilter === 'active' ? 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            Active
                        </button>
                        <button
                            wire:click="setStatusFilter('featured')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ $statusFilter === 'featured' ? 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            Featured
                        </button>
                        <button
                            wire:click="setStatusFilter('inactive')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors
                                {{ $statusFilter === 'inactive' ? 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}"
                        >
                            Inactive
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gallery Grid --}}
        @if($this->hallImages->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Gallery Images
                        <span class="text-sm font-normal text-gray-500">({{ $this->hallImages->count() }})</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Click to manage images
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach($this->hallImages as $image)
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
                                        title="{{ $image->is_featured ? 'Unmark Featured' : 'Mark Featured' }}"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </button>

                                    {{-- Toggle Active --}}
                                    <button
                                        wire:click="toggleActive({{ $image->id }})"
                                        type="button"
                                        class="p-2 rounded-lg transition-colors
                                            {{ $image->is_active 
                                                ? 'bg-success-500 text-white' 
                                                : 'bg-white/20 text-white hover:bg-success-500' }}"
                                        title="{{ $image->is_active ? 'Deactivate' : 'Activate' }}"
                                    >
                                        @if($image->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                            </svg>
                                        @endif
                                    </button>

                                    {{-- Set as Hall Featured --}}
                                    <button
                                        wire:click="setAsFeaturedImage({{ $image->id }})"
                                        type="button"
                                        class="p-2 bg-white/20 text-white rounded-lg hover:bg-primary-500 transition-colors"
                                        title="Set as Hall Cover"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>

                                    {{-- Delete --}}
                                    <button
                                        wire:click="deleteImage({{ $image->id }})"
                                        wire:confirm="Are you sure you want to delete this image?"
                                        type="button"
                                        class="p-2 bg-white/20 text-white rounded-lg hover:bg-danger-500 transition-colors"
                                        title="Delete"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Edit Link --}}
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
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    No Images Yet
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Upload images to showcase your hall to potential customers.
                </p>
                <a 
                    href="{{ \App\Filament\Owner\Resources\GalleryResource::getUrl('upload') }}?hall_id={{ $selectedHallId }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload Images
                </a>
            </div>
        @endif
    @else
        {{-- No Hall Selected --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                No Hall Selected
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                Please select a hall to manage its gallery
            </p>
        </div>
    @endif
</x-filament-panels::page>
