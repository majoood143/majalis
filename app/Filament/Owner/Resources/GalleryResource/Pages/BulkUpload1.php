<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use App\Filament\Owner\Resources\GalleryResource;
use App\Models\Hall;
use App\Models\HallImage;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * BulkUpload Page for Owner Panel
 *
 * Upload multiple images at once to a hall gallery.
 */
class BulkUpload1 extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    /**
     * The resource this page belongs to.
     */
    protected static string $resource = GalleryResource::class;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.resources.gallery-resource.pages.bulk-upload';

    /**
     * Selected hall ID.
     */
    public ?int $selectedHallId = null;

    /**
     * Image type for all uploads.
     */
    public string $imageType = 'gallery';

    /**
     * Uploaded files.
     */
    public array $uploadedFiles = [];

    /**
     * Upload progress.
     */
    public int $uploadProgress = 0;

    /**
     * Whether upload is in progress.
     */
    public bool $isUploading = false;

    /**
     * Successfully uploaded count.
     */
    public int $successCount = 0;

    /**
     * Failed uploads.
     */
    public array $failedUploads = [];

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $halls = $this->getOwnerHalls();

        // Pre-select first hall if only one
        if ($halls->count() === 1) {
            $this->selectedHallId = $halls->first()->id;
        }

        // Check for hall_id in URL
        if (request()->has('hall_id')) {
            $hallId = (int) request()->get('hall_id');
            $hall = Hall::find($hallId);

            if ($hall && $hall->owner_id === Auth::id()) {
                $this->selectedHallId = $hallId;
            }
        }
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.gallery.bulk_upload.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.gallery.bulk_upload.heading');
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.gallery.bulk_upload.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('owner.gallery.actions.back_to_gallery'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => GalleryResource::getUrl('index')),
        ];
    }

    /**
     * Get owner's halls.
     */
    #[Computed]
    public function getOwnerHalls(): Collection
    {
        $user = Auth::user();

        return Hall::where('owner_id', $user?->id)
            ->where('is_active', true)
            ->orderBy('name->en')
            ->get();
    }

    /**
     * Get the selected hall.
     */
    #[Computed]
    public function selectedHall(): ?Hall
    {
        if (!$this->selectedHallId) {
            return null;
        }

        return Hall::find($this->selectedHallId);
    }

    /**
     * Get current image count for selected hall.
     */
    #[Computed]
    public function currentImageCount(): int
    {
        if (!$this->selectedHallId) {
            return 0;
        }

        return HallImage::where('hall_id', $this->selectedHallId)->count();
    }

    /**
     * Set selected hall.
     */
    public function setHall(?int $hallId): void
    {
        $this->selectedHallId = $hallId;
        unset($this->selectedHall);
        unset($this->currentImageCount);
    }

    /**
     * Set image type.
     */
    public function setType(string $type): void
    {
        $this->imageType = $type;
    }

    /**
     * Handle file upload via Livewire.
     */
    public function updatedUploadedFiles(): void
    {
        $this->validateOnly('uploadedFiles', [
            'uploadedFiles' => 'array|max:20',
            'uploadedFiles.*' => 'image|max:5120', // 5MB max per file
        ]);
    }

    /**
     * Process and save uploaded files.
     */
    public function processUploads(): void
    {
        if (!$this->selectedHallId) {
            Notification::make()
                ->warning()
                ->title(__('owner.gallery.notifications.select_hall_first'))
                ->send();
            return;
        }

        if (empty($this->uploadedFiles)) {
            Notification::make()
                ->warning()
                ->title(__('owner.gallery.notifications.no_files'))
                ->send();
            return;
        }

        // Verify hall ownership
        $hall = Hall::find($this->selectedHallId);
        if (!$hall || $hall->owner_id !== Auth::id()) {
            Notification::make()
                ->danger()
                ->title(__('owner.errors.unauthorized'))
                ->send();
            return;
        }

        $this->isUploading = true;
        $this->successCount = 0;
        $this->failedUploads = [];

        $totalFiles = count($this->uploadedFiles);
        $currentOrder = HallImage::where('hall_id', $this->selectedHallId)->max('order') ?? 0;

        foreach ($this->uploadedFiles as $index => $file) {
            try {
                // Store file
                $path = $file->store('halls/images', 'public');

                if (!$path) {
                    throw new \Exception('Failed to store file');
                }

                // Extract metadata
                $fullPath = Storage::disk('public')->path($path);
                $imageInfo = @getimagesize($fullPath);

                // Create record
                HallImage::create([
                    'hall_id' => $this->selectedHallId,
                    'image_path' => $path,
                    'type' => $this->imageType,
                    'is_active' => true,
                    'is_featured' => false,
                    'order' => ++$currentOrder,
                    'file_size' => Storage::disk('public')->size($path),
                    'mime_type' => Storage::disk('public')->mimeType($path),
                    'width' => $imageInfo[0] ?? null,
                    'height' => $imageInfo[1] ?? null,
                ]);

                $this->successCount++;

                // Generate thumbnail (async would be better in production)
                $this->generateThumbnailForPath($path);

            } catch (\Exception $e) {
                Log::error('Bulk upload failed for file: ' . $e->getMessage());
                $this->failedUploads[] = [
                    'name' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }

            // Update progress
            $this->uploadProgress = (int) round((($index + 1) / $totalFiles) * 100);
        }

        $this->isUploading = false;
        $this->uploadedFiles = [];

        // Show result notification
        if ($this->successCount > 0) {
            Notification::make()
                ->success()
                ->title(__('owner.gallery.notifications.bulk_uploaded'))
                ->body(__('owner.gallery.notifications.bulk_uploaded_body', [
                    'count' => $this->successCount,
                ]))
                ->send();
        }

        if (!empty($this->failedUploads)) {
            Notification::make()
                ->warning()
                ->title(__('owner.gallery.notifications.some_failed'))
                ->body(__('owner.gallery.notifications.some_failed_body', [
                    'count' => count($this->failedUploads),
                ]))
                ->send();
        }

        // Reset
        unset($this->currentImageCount);
    }

    /**
     * Generate thumbnail for uploaded image.
     */
    protected function generateThumbnailForPath(string $imagePath): void
    {
        try {
            if (!class_exists(\Intervention\Image\ImageManager::class)) {
                return;
            }

            $sourcePath = Storage::disk('public')->path($imagePath);

            if (!file_exists($sourcePath)) {
                return;
            }

            $filename = pathinfo($imagePath, PATHINFO_FILENAME);
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $thumbnailPath = 'halls/thumbnails/' . $filename . '_thumb.' . $extension;

            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );

            $thumbnail = $manager->read($sourcePath)->cover(300, 200);
            Storage::disk('public')->put($thumbnailPath, $thumbnail->toJpeg(80));

            // Update the record
            HallImage::where('image_path', $imagePath)
                ->update(['thumbnail_path' => $thumbnailPath]);

        } catch (\Exception $e) {
            Log::warning('Thumbnail generation failed during bulk upload: ' . $e->getMessage());
        }
    }

    /**
     * Clear all uploaded files.
     */
    public function clearFiles(): void
    {
        $this->uploadedFiles = [];
        $this->uploadProgress = 0;
        $this->successCount = 0;
        $this->failedUploads = [];
    }

    /**
     * Remove a specific file from the upload queue.
     */
    public function removeFile(int $index): void
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
    }
}
