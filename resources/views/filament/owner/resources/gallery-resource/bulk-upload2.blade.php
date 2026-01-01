<x-filament-panels::page>
    {{-- Hall & Type Selection --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Hall Selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Hall <span class="text-danger-500">*</span>
                </label>
                <select
                    wire:model.live="selectedHallId"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">Select a hall...</option>
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
                    Image Type
                </label>
                <select
                    wire:model.live="imageType"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="gallery">Gallery</option>
                    <option value="exterior">Exterior</option>
                    <option value="interior">Interior</option>
                    <option value="floor_plan">Floor Plan</option>
                </select>
            </div>
        </div>

        @if($this->selectedHall)
            <div class="mt-4 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-primary-700 dark:text-primary-300">
                        Current images: <strong>{{ $this->currentImageCount }}</strong>
                    </span>
                    <span class="text-xs text-primary-600 dark:text-primary-400">
                        Max 20 images per upload
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Upload Zone --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Upload Images
        </h3>

        {{-- Dropzone --}}
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    Drag and drop images here
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                    or
                </p>

                <label class="cursor-pointer">
                    <span class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        Browse Files
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
                    JPEG, PNG, WebP • Max 5MB each • Up to 20 files
                </p>
            </div>
        </div>

        {{-- Upload Progress --}}
        @if($isUploading)
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Uploading...</span>
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
                        Selected Files ({{ count($uploadedFiles) }})
                    </h4>
                    <button
                        wire:click="clearFiles"
                        type="button"
                        class="text-sm text-danger-600 hover:text-danger-700"
                    >
                        Clear All
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
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
                        Cancel
                    </button>
                    <button
                        wire:click="processUploads"
                        type="button"
                        class="px-6 py-2 bg-success-600 text-white text-sm font-medium rounded-lg hover:bg-success-700 transition-colors flex items-center gap-2"
                        @if(!$selectedHallId) disabled @endif
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload All Images
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
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">
                                {{ $successCount }} image(s) uploaded successfully
                            </span>
                        </div>
                    </div>
                @endif

                @if(count($failedUploads) > 0)
                    <div class="p-3 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg">
                        <div class="flex items-center gap-2 text-danger-700 dark:text-danger-400 mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">
                                {{ count($failedUploads) }} image(s) failed
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
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="font-medium mb-2">Upload Tips</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600 dark:text-blue-400">
                    <li>Use high-quality images (1920×1080 or larger recommended)</li>
                    <li>Images will be automatically resized and optimized</li>
                    <li>You can reorder images after uploading</li>
                    <li>Set featured images to highlight key photos</li>
                </ul>
            </div>
        </div>
    </div>
</x-filament-panels::page>
