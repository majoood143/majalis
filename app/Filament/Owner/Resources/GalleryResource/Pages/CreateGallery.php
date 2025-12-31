<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use App\Filament\Owner\Resources\GalleryResource;
use App\Models\Hall;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * CreateGallery Page for Owner Panel
 *
 * Upload new images to hall gallery.
 */
class CreateGallery extends CreateRecord
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = GalleryResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.gallery.create.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.gallery.create.heading');
    }

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        parent::mount();

        // Pre-fill hall_id if coming from a specific hall
        if (request()->has('hall_id')) {
            $hallId = (int) request()->get('hall_id');
            $hall = Hall::find($hallId);

            // Verify ownership
            if ($hall && $hall->owner_id === Auth::id()) {
                $this->form->fill([
                    'hall_id' => $hallId,
                ]);
            }
        }
    }

    /**
     * Mutate form data before creation.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // Verify hall ownership
        $hall = Hall::find($data['hall_id']);
        if (!$hall || $hall->owner_id !== $user->id) {
            Notification::make()
                ->danger()
                ->title(__('owner.errors.unauthorized'))
                ->send();

            $this->halt();
        }

        // Extract image metadata
        if (!empty($data['image_path'])) {
            $metadata = $this->extractImageMetadata($data['image_path']);
            $data = array_merge($data, $metadata);
        }

        // If type is 'featured', also mark as featured
        if (($data['type'] ?? '') === 'featured') {
            $data['is_featured'] = true;
        }

        return $data;
    }

    /**
     * Handle the record creation.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Generate thumbnail
        $this->generateThumbnail($record);

        // Log the upload
        Log::info('Hall image uploaded by owner', [
            'image_id' => $record->id,
            'hall_id' => $record->hall_id,
            'type' => $record->type,
            'uploaded_by' => Auth::id(),
        ]);

        return $record;
    }

    /**
     * Extract image metadata.
     */
    protected function extractImageMetadata(string $imagePath): array
    {
        $metadata = [
            'file_size' => null,
            'mime_type' => null,
            'width' => null,
            'height' => null,
        ];

        try {
            $fullPath = Storage::disk('public')->path($imagePath);

            if (file_exists($fullPath)) {
                $metadata['file_size'] = Storage::disk('public')->size($imagePath);
                $metadata['mime_type'] = Storage::disk('public')->mimeType($imagePath);

                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo) {
                    $metadata['width'] = $imageInfo[0];
                    $metadata['height'] = $imageInfo[1];
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to extract image metadata: ' . $e->getMessage());
        }

        return $metadata;
    }

    /**
     * Generate thumbnail for the image.
     */
    protected function generateThumbnail(Model $record): void
    {
        try {
            // Check if Intervention Image is available
            if (!class_exists(\Intervention\Image\ImageManager::class)) {
                return;
            }

            $sourcePath = Storage::disk('public')->path($record->image_path);

            if (!file_exists($sourcePath)) {
                return;
            }

            // Generate thumbnail filename
            $filename = pathinfo($record->image_path, PATHINFO_FILENAME);
            $extension = pathinfo($record->image_path, PATHINFO_EXTENSION);
            $thumbnailPath = 'halls/thumbnails/' . $filename . '_thumb.' . $extension;

            // Create thumbnail using Intervention Image
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );

            $thumbnail = $manager->read($sourcePath)
                ->cover(300, 200);

            // Save thumbnail
            Storage::disk('public')->put($thumbnailPath, $thumbnail->toJpeg(80));

            // Update record
            $record->update(['thumbnail_path' => $thumbnailPath]);

            Log::info('Thumbnail generated for image: ' . $record->id);
        } catch (\Exception $e) {
            Log::warning('Thumbnail generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the created notification.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('owner.gallery.notifications.uploaded'))
            ->body(__('owner.gallery.notifications.uploaded_body'));
    }

    /**
     * Get the redirect URL after creation.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
