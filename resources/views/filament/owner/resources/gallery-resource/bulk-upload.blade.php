<x-filament-panels::page>
    {{-- Hall & Type Selection --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Hall Selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.gallery.fields.hall') }} <span class="text-danger-500">*</span>
                </label>
                <select
                    wire:model.live="selectedHallId"
                    wire:change="setHall($event.target.value)"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">{{ __('owner.gallery.bulk_upload.select_hall') }}</option>
                    @foreach($this->getOwnerHalls as $hall)
                        <option value="{{ $hall->id }}">
                            {{ $hall->getTranslation('name', app()->getLocale()) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Type Selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.gallery.fields.type') }}
                </label>
                <select
                    wire:model.live="imageType"
                    wire:change="setType($event.target.value)"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="gallery">{{ __('owner.gallery.types.gallery') }}</option>
                    <option value="exterior">{{ __('owner.gallery.types.exterior') }}</option>
                    <option value="interior">{{ __('owner.gallery.types.interior') }}</option>
                    <option value="floor_plan">{{ __('owner.gallery.types.floor_plan') }}</option>
                </select>
            </div>
        </div>

        @if($this->selectedHall)
            <div class="mt-4 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-primary-700 dark:text-primary-300">
                        {{ __('owner.gallery.bulk_upload.current_images') }}: 
                        <strong>{{ $this->currentImageCount }}</strong>
                    </span>
                    <span class="text-xs text-primary-600 dark:text-primary-400">
                        {{ __('owner.gallery.bulk_upload.max_20') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Upload Zone --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('owner.gallery.bulk_upload.upload_images') }}
        </h3>

        {{-- Dropzone --}}
        <div 
            x-data="{ 
                isDragging: false,
                handleDrop(e) {
                    this.isDragging = false;
                    const files = e.dataTransfer.files;
                    if (files.length) {
                        @this.uploadMultiple('uploadedFiles', files);
                    }
                }
            }"
            x-on:dragover.prevent="isDragging = true"
            x-on:dragleave.prevent="isDragging = false"
            x-on:drop.prevent="handleDrop($event)"
            class="border-2 border-dashed rounded-xl p-8 text-center transition-colors"
            :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-300 dark:border-gray-600'"
        >
            <div class="flex flex-col items-center">
                <x-heroicon-o-cloud-arrow-up class="w-12 h-12 text-gray-400 mb-4" />
                
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    {{ __('owner.gallery.bulk_upload.drag_drop') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                    {{ __('owner.gallery.bulk_upload.or') }}
                </p>

                <label class="cursor-pointer">
                    <span class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        {{ __('owner.gallery.bulk_upload.browse') }}
                    </span>
                    <input 
                        type="file" 
                        wire:model="uploadedFiles"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                    >
                </label>

                <p class="text-xs text-gray-400 mt-4">
                    {{ __('owner.gallery.bulk_upload.allowed_formats') }}
                </p>
            </div>
        </div>

        {{-- Upload Progress --}}
        @if($isUploading)
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('owner.gallery.bulk_upload.uploading') }}
                    </span>
                    <span class="text-sm font-medium text-primary-600">{{ $uploadProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                        class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $uploadProgress }}%"
                    ></div>
                </div>
            </div>
        @endif

        {{-- Preview Grid --}}
        @if(count($uploadedFiles) > 0)
            <div class="mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('owner.gallery.bulk_upload.selected_files') }} ({{ count($uploadedFiles) }})
                    </h4>
                    <button
                        wire:click="clearFiles"
                        type="button"
                        class="text-sm text-danger-600 hover:text-danger-700"
                    >
                        {{ __('owner.gallery.bulk_upload.clear_all') }}
                    </button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($uploadedFiles as $index => $file)
                        <div class="relative group">
                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                @if($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                    <img 
                                        src="{{ $file->temporaryUrl() }}" 
                                        alt="Preview"
                                        class="w-full h-full object-cover"
                                    >
                                @endif
                            </div>
                            <button
                                wire:click="removeFile({{ $index }})"
                                type="button"
                                class="absolute -top-2 -right-2 w-6 h-6 bg-danger-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
                            >
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Upload Button --}}
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="clearFiles"
                        type="button"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        {{ __('owner.gallery.bulk_upload.cancel') }}
                    </button>
                    <button
                        wire:click="processUploads"
                        type="button"
                        class="px-6 py-2 bg-success-600 text-white text-sm font-medium rounded-lg hover:bg-success-700 transition-colors flex items-center gap-2"
                        @if(!$selectedHallId) disabled @endif
                    >
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                        {{ __('owner.gallery.bulk_upload.upload_all') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Upload Results --}}
        @if($successCount > 0 || count($failedUploads) > 0)
            <div class="mt-6 space-y-3">
                @if($successCount > 0)
                    <div class="p-3 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg">
                        <div class="flex items-center gap-2 text-success-700 dark:text-success-400">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            <span class="text-sm font-medium">
                                {{ __('owner.gallery.bulk_upload.success_count', ['count' => $successCount]) }}
                            </span>
                        </div>
                    </div>
                @endif

                @if(count($failedUploads) > 0)
                    <div class="p-3 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg">
                        <div class="flex items-center gap-2 text-danger-700 dark:text-danger-400 mb-2">
                            <x-heroicon-o-x-circle class="w-5 h-5" />
                            <span class="text-sm font-medium">
                                {{ __('owner.gallery.bulk_upload.failed_count', ['count' => count($failedUploads)]) }}
                            </span>
                        </div>
                        <ul class="text-xs text-danger-600 dark:text-danger-400 space-y-1">
                            @foreach($failedUploads as $failed)
                                <li>{{ $failed['name'] }}: {{ $failed['error'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Instructions --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <div class="flex gap-3">
            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="font-medium mb-2">{{ __('owner.gallery.bulk_upload.instructions_title') }}</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600 dark:text-blue-400">
                    <li>{{ __('owner.gallery.bulk_upload.instruction_1') }}</li>
                    <li>{{ __('owner.gallery.bulk_upload.instruction_2') }}</li>
                    <li>{{ __('owner.gallery.bulk_upload.instruction_3') }}</li>
                    <li>{{ __('owner.gallery.bulk_upload.instruction_4') }}</li>
                </ul>
            </div>
        </div>
    </div>
</x-filament-panels::page>
